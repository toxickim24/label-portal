<?php

namespace App\Services;

use App\Models\CMAReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CMAPdfGeneratorService
{
    protected CMAService $cmaService;

    public function __construct(CMAService $cmaService)
    {
        $this->cmaService = $cmaService;
    }

    /**
     * Generate PDF for a CMA report
     */
    public function generatePdf(CMAReport $report): string
    {
        // Get comparison data
        $comparisonData = $this->cmaService->getComparisonData($report);

        // Get branding settings
        $branding = $this->getBrandingSettings();

        // Prepare data for PDF
        $data = [
            'report' => $report,
            'subject' => $comparisonData['subject'],
            'comparables' => $comparisonData['comparables'],
            'branding' => $branding,
            'agent' => $report->user,
            'contact' => $report->contact,
            'generatedDate' => now()->format('F j, Y'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.cma-report', $data)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        // Generate filename
        $filename = $this->generateFilename($report);

        // Save PDF
        $path = 'cma-reports/' . $filename;
        Storage::disk('local')->put($path, $pdf->output());

        // Update report with PDF path
        $report->update(['generated_pdf_path' => $path]);

        return $path;
    }

    /**
     * Generate unique filename for PDF
     */
    protected function generateFilename(CMAReport $report): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $reportId = $report->id;

        $subjectAddress = '';
        if ($report->subject_property && isset($report->subject_property['address'])) {
            $subjectAddress = str_replace(' ', '_', $report->subject_property['address']);
            $subjectAddress = preg_replace('/[^A-Za-z0-9_-]/', '', $subjectAddress);
            $subjectAddress = substr($subjectAddress, 0, 50);
            $subjectAddress = '_' . $subjectAddress;
        }

        return "CMA_Report_{$reportId}{$subjectAddress}_{$timestamp}.pdf";
    }

    /**
     * Get branding settings from database or config
     */
    protected function getBrandingSettings(): array
    {
        // Try to get branding from settings
        $settings = \App\Models\Setting::first();

        return [
            'app_name' => $settings->app_name ?? config('app.name'),
            'logo_light' => $settings->logo_light ?? null,
            'logo_dark' => $settings->logo_dark ?? null,
        ];
    }

    /**
     * Download PDF (returns PDF as response)
     */
    public function downloadPdf(CMAReport $report)
    {
        // Check if PDF exists
        if ($report->generated_pdf_path && Storage::disk('local')->exists($report->generated_pdf_path)) {
            return Storage::disk('local')->download($report->generated_pdf_path);
        }

        // Generate new PDF if not exists
        $path = $this->generatePdf($report);

        return Storage::disk('local')->download($path);
    }

    /**
     * Get PDF for inline viewing
     */
    public function viewPdf(CMAReport $report)
    {
        // Check if PDF exists
        if ($report->generated_pdf_path && Storage::disk('local')->exists($report->generated_pdf_path)) {
            return response()->file(Storage::disk('local')->path($report->generated_pdf_path));
        }

        // Generate new PDF if not exists
        $path = $this->generatePdf($report);

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Regenerate PDF (force new generation)
     */
    public function regeneratePdf(CMAReport $report): string
    {
        // Delete old PDF if exists
        if ($report->generated_pdf_path) {
            Storage::disk('local')->delete($report->generated_pdf_path);
        }

        // Generate new PDF
        return $this->generatePdf($report);
    }

    /**
     * Get PDF URL for sharing
     */
    public function getPdfUrl(CMAReport $report): ?string
    {
        if (!$report->generated_pdf_path) {
            return null;
        }

        return Storage::disk('local')->url($report->generated_pdf_path);
    }

    /**
     * Email PDF to contact
     */
    public function emailPdf(CMAReport $report): bool
    {
        if (!$report->contact || !$report->contact->email) {
            return false;
        }

        // Ensure PDF is generated
        if (!$report->generated_pdf_path) {
            $this->generatePdf($report);
        }

        // Send email with PDF attachment
        \Illuminate\Support\Facades\Mail::to($report->contact->email)->send(
            new \App\Mail\CMAReportMail(
                report: $report,
                pdfPath: Storage::disk('local')->path($report->generated_pdf_path)
            )
        );

        // Log activity
        activity()
            ->performedOn($report)
            ->causedBy(auth()->user())
            ->log('CMA report emailed to ' . $report->contact->email);

        return true;
    }
}
