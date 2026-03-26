<?php

namespace App\Http\Controllers;

use App\Models\CMAReport;
use App\Models\Contact;
use App\Services\CMAService;
use App\Services\CMAPdfGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CMAReportController extends Controller
{
    protected CMAService $cmaService;
    protected CMAPdfGeneratorService $pdfService;

    public function __construct(CMAService $cmaService, CMAPdfGeneratorService $pdfService)
    {
        $this->cmaService = $cmaService;
        $this->pdfService = $pdfService;
    }

    /**
     * Display a listing of CMA reports
     */
    public function index(): Response
    {
        $reports = CMAReport::with(['contact', 'user'])
            ->latest()
            ->paginate(15);

        return Inertia::render('CMA/Index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Show the form for creating a new CMA report
     */
    public function create(Request $request): Response
    {
        $contacts = Contact::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('last_name')
            ->get()
            ->map(fn($contact) => [
                'id' => $contact->id,
                'name' => $contact->full_name,
                'email' => $contact->email,
            ]);

        // Pre-select contact if provided
        $contactId = $request->query('contact_id');

        return Inertia::render('CMA/Create', [
            'contacts' => $contacts,
            'preselectedContactId' => $contactId ? (int) $contactId : null,
        ]);
    }

    /**
     * Store a newly created CMA report
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'subject_property' => ['required', 'array'],
            'subject_property.address' => ['required', 'string'],
            'subject_property.square_feet' => ['required', 'numeric', 'min:0'],
            'subject_property.bedrooms' => ['nullable', 'integer', 'min:0'],
            'subject_property.bathrooms' => ['nullable', 'numeric', 'min:0'],
            'subject_property.lot_size' => ['nullable', 'numeric', 'min:0'],
            'subject_property.year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'subject_property.property_type' => ['nullable', 'string'],
            'comparables' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
        ]);

        $report = $this->cmaService->createReport($validated);

        return redirect()->route('cma.edit', $report)
            ->with('success', 'CMA report created successfully.');
    }

    /**
     * Display the specified CMA report
     */
    public function show(CMAReport $cma): Response
    {
        $cma->load(['contact', 'user']);

        $comparisonData = $this->cmaService->getComparisonData($cma);

        return Inertia::render('CMA/Show', [
            'report' => $cma,
            'comparisonData' => $comparisonData,
        ]);
    }

    /**
     * Show the form for editing the specified CMA report
     */
    public function edit(CMAReport $cma): Response
    {
        $cma->load(['contact', 'user']);

        $contacts = Contact::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('last_name')
            ->get()
            ->map(fn($contact) => [
                'id' => $contact->id,
                'name' => $contact->full_name,
                'email' => $contact->email,
            ]);

        $comparisonData = $this->cmaService->getComparisonData($cma);

        return Inertia::render('CMA/Edit', [
            'report' => $cma,
            'contacts' => $contacts,
            'comparisonData' => $comparisonData,
        ]);
    }

    /**
     * Update the specified CMA report
     */
    public function update(Request $request, CMAReport $cma): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'subject_property' => ['sometimes', 'array'],
            'comparables' => ['sometimes', 'array'],
            'adjustments' => ['sometimes', 'array'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->cmaService->updateReport($cma, $validated);

        return back()->with('success', 'CMA report updated successfully.');
    }

    /**
     * Remove the specified CMA report (soft delete)
     */
    public function destroy(CMAReport $cma): RedirectResponse
    {
        $this->cmaService->archiveReport($cma);

        return redirect()->route('cma.index')
            ->with('success', 'CMA report archived successfully.');
    }

    /**
     * Add comparable to report
     */
    public function addComparable(Request $request, CMAReport $cma): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string'],
            'square_feet' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'sale_date' => ['nullable', 'date'],
            'distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->cmaService->addComparable($cma, $validated);

        return back()->with('success', 'Comparable property added successfully.');
    }

    /**
     * Update comparable
     */
    public function updateComparable(Request $request, CMAReport $cma, int $index): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['sometimes', 'string'],
            'square_feet' => ['sometimes', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['sometimes', 'numeric', 'min:0'],
            'sale_date' => ['nullable', 'date'],
            'distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->cmaService->updateComparable($cma, $index, $validated);

        return back()->with('success', 'Comparable property updated successfully.');
    }

    /**
     * Remove comparable
     */
    public function removeComparable(CMAReport $cma, int $index): RedirectResponse
    {
        $this->cmaService->removeComparable($cma, $index);

        return back()->with('success', 'Comparable property removed successfully.');
    }

    /**
     * Update adjustments for a comparable
     */
    public function updateAdjustments(Request $request, CMAReport $cma, int $index): RedirectResponse
    {
        $validated = $request->validate([
            'adjustments' => ['required', 'array'],
            'adjustments.*.category' => ['required', 'string'],
            'adjustments.*.amount' => ['required', 'numeric'],
            'adjustments.*.notes' => ['nullable', 'string'],
        ]);

        $this->cmaService->updateAdjustments($cma, $index, $validated['adjustments']);

        return back()->with('success', 'Adjustments updated successfully.');
    }

    /**
     * Finalize the report
     */
    public function finalize(CMAReport $cma): RedirectResponse
    {
        $this->cmaService->finalizeReport($cma);

        return back()->with('success', 'CMA report finalized successfully.');
    }

    /**
     * Duplicate report
     */
    public function duplicate(CMAReport $cma): RedirectResponse
    {
        $newReport = $this->cmaService->duplicateReport($cma);

        return redirect()->route('cma.edit', $newReport)
            ->with('success', 'CMA report duplicated successfully.');
    }

    /**
     * Generate PDF
     */
    public function generatePdf(CMAReport $cma): RedirectResponse
    {
        $this->pdfService->generatePdf($cma);

        return back()->with('success', 'PDF generated successfully.');
    }

    /**
     * Download PDF
     */
    public function downloadPdf(CMAReport $cma)
    {
        return $this->pdfService->downloadPdf($cma);
    }

    /**
     * View PDF
     */
    public function viewPdf(CMAReport $cma)
    {
        return $this->pdfService->viewPdf($cma);
    }

    /**
     * Email PDF to contact
     */
    public function emailPdf(CMAReport $cma): RedirectResponse
    {
        if (!$cma->contact || !$cma->contact->email) {
            return back()->with('error', 'No contact email available to send the report.');
        }

        $this->pdfService->emailPdf($cma);

        return back()->with('success', 'PDF sent to ' . $cma->contact->email . ' successfully.');
    }
}
