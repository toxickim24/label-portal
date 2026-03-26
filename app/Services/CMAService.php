<?php

namespace App\Services;

use App\Models\CMAReport;
use App\Models\Contact;
use Illuminate\Support\Collection;

class CMAService
{
    /**
     * Create a new CMA report
     */
    public function createReport(array $data): CMAReport
    {
        return CMAReport::create([
            'contact_id' => $data['contact_id'] ?? null,
            'user_id' => auth()->id(),
            'title' => $data['title'] ?? 'Untitled CMA Report',
            'subject_property' => $data['subject_property'] ?? null,
            'comparables' => $data['comparables'] ?? [],
            'adjustments' => $data['adjustments'] ?? [],
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
        ]);
    }

    /**
     * Update existing CMA report
     */
    public function updateReport(CMAReport $report, array $data): CMAReport
    {
        $report->update([
            'contact_id' => $data['contact_id'] ?? $report->contact_id,
            'title' => $data['title'] ?? $report->title,
            'subject_property' => $data['subject_property'] ?? $report->subject_property,
            'comparables' => $data['comparables'] ?? $report->comparables,
            'adjustments' => $data['adjustments'] ?? $report->adjustments,
            'notes' => $data['notes'] ?? $report->notes,
        ]);

        // Recalculate valuation if comparables or adjustments changed
        if (isset($data['comparables']) || isset($data['adjustments'])) {
            $this->calculateValuation($report);
        }

        return $report->fresh();
    }

    /**
     * Calculate valuation based on comparables and adjustments
     */
    public function calculateValuation(CMAReport $report): CMAReport
    {
        $comparables = $report->comparables ?? [];

        if (empty($comparables)) {
            $report->update([
                'valuation_low' => null,
                'valuation_avg' => null,
                'valuation_high' => null,
            ]);
            return $report;
        }

        $adjustedPrices = [];

        foreach ($comparables as $index => $comp) {
            $salePrice = (float) ($comp['sale_price'] ?? 0);
            $adjustments = $report->adjustments[$index] ?? [];

            // Apply adjustments
            $totalAdjustment = 0;
            foreach ($adjustments as $adjustment) {
                $totalAdjustment += (float) ($adjustment['amount'] ?? 0);
            }

            $adjustedPrice = $salePrice + $totalAdjustment;
            if ($adjustedPrice > 0) {
                $adjustedPrices[] = $adjustedPrice;
            }
        }

        if (empty($adjustedPrices)) {
            return $report;
        }

        sort($adjustedPrices);

        // Calculate low, avg, high
        $count = count($adjustedPrices);
        $low = $adjustedPrices[0];
        $high = $adjustedPrices[$count - 1];
        $avg = array_sum($adjustedPrices) / $count;

        $report->update([
            'valuation_low' => $low,
            'valuation_avg' => round($avg, 2),
            'valuation_high' => $high,
        ]);

        return $report->fresh();
    }

    /**
     * Calculate price per square foot for a property
     */
    public function calculatePricePerSquareFoot(float $price, float $squareFeet): ?float
    {
        if ($squareFeet <= 0) {
            return null;
        }

        return round($price / $squareFeet, 2);
    }

    /**
     * Add comparable property to report
     */
    public function addComparable(CMAReport $report, array $comparable): CMAReport
    {
        $comparables = $report->comparables ?? [];
        $comparables[] = $comparable;

        $report->update(['comparables' => $comparables]);

        // Recalculate valuation
        $this->calculateValuation($report);

        return $report->fresh();
    }

    /**
     * Update comparable at specific index
     */
    public function updateComparable(CMAReport $report, int $index, array $comparable): CMAReport
    {
        $comparables = $report->comparables ?? [];

        if (isset($comparables[$index])) {
            $comparables[$index] = array_merge($comparables[$index], $comparable);
            $report->update(['comparables' => $comparables]);

            // Recalculate valuation
            $this->calculateValuation($report);
        }

        return $report->fresh();
    }

    /**
     * Remove comparable at specific index
     */
    public function removeComparable(CMAReport $report, int $index): CMAReport
    {
        $comparables = $report->comparables ?? [];

        if (isset($comparables[$index])) {
            array_splice($comparables, $index, 1);
            $report->update(['comparables' => $comparables]);

            // Recalculate valuation
            $this->calculateValuation($report);
        }

        return $report->fresh();
    }

    /**
     * Update adjustments for a specific comparable
     */
    public function updateAdjustments(CMAReport $report, int $compIndex, array $adjustments): CMAReport
    {
        $allAdjustments = $report->adjustments ?? [];
        $allAdjustments[$compIndex] = $adjustments;

        $report->update(['adjustments' => $allAdjustments]);

        // Recalculate valuation
        $this->calculateValuation($report);

        return $report->fresh();
    }

    /**
     * Finalize the report
     */
    public function finalizeReport(CMAReport $report): CMAReport
    {
        // Ensure valuation is calculated
        $this->calculateValuation($report);

        $report->finalize();

        // Log activity
        activity()
            ->performedOn($report)
            ->causedBy(auth()->user())
            ->log('CMA report finalized');

        return $report->fresh();
    }

    /**
     * Unfinalize the report (convert back to draft) - Admin only
     */
    public function unfinalizeReport(CMAReport $report): CMAReport
    {
        $report->update(['status' => 'draft']);

        // Log activity
        activity()
            ->performedOn($report)
            ->causedBy(auth()->user())
            ->log('CMA report converted back to draft');

        return $report->fresh();
    }

    /**
     * Duplicate an existing report
     */
    public function duplicateReport(CMAReport $report): CMAReport
    {
        return CMAReport::create([
            'contact_id' => $report->contact_id,
            'user_id' => auth()->id(),
            'title' => $report->title . ' (Copy)',
            'subject_property' => $report->subject_property,
            'comparables' => $report->comparables,
            'adjustments' => $report->adjustments,
            'notes' => $report->notes,
            'status' => 'draft',
        ]);
    }

    /**
     * Get reports for a specific contact
     */
    public function getContactReports(Contact $contact): Collection
    {
        return CMAReport::where('contact_id', $contact->id)
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Get reports created by a specific user
     */
    public function getUserReports($userId = null): Collection
    {
        $userId = $userId ?? auth()->id();

        return CMAReport::where('user_id', $userId)
            ->with(['contact', 'user'])
            ->latest()
            ->get();
    }

    /**
     * Archive (soft delete) a report
     */
    public function archiveReport(CMAReport $report): bool
    {
        activity()
            ->performedOn($report)
            ->causedBy(auth()->user())
            ->log('CMA report archived');

        return $report->delete();
    }

    /**
     * Restore archived report
     */
    public function restoreReport(int $reportId): ?CMAReport
    {
        $report = CMAReport::withTrashed()->find($reportId);

        if ($report) {
            $report->restore();

            activity()
                ->performedOn($report)
                ->causedBy(auth()->user())
                ->log('CMA report restored');
        }

        return $report;
    }

    /**
     * Get comparison data for display
     */
    public function getComparisonData(CMAReport $report): array
    {
        $subjectProperty = $report->subject_property;
        $comparables = $report->comparables ?? [];

        $comparisonData = [
            'subject' => $subjectProperty,
            'comparables' => [],
        ];

        foreach ($comparables as $index => $comp) {
            $salePrice = (float) ($comp['sale_price'] ?? 0);
            $squareFeet = (float) ($comp['square_feet'] ?? 0);
            $adjustments = $report->adjustments[$index] ?? [];

            $totalAdjustment = 0;
            foreach ($adjustments as $adjustment) {
                $totalAdjustment += (float) ($adjustment['amount'] ?? 0);
            }

            $adjustedPrice = $salePrice + $totalAdjustment;
            $pricePerSqFt = $squareFeet > 0 ? round($adjustedPrice / $squareFeet, 2) : null;

            $comparisonData['comparables'][] = [
                'data' => $comp,
                'adjustments' => $adjustments,
                'total_adjustment' => $totalAdjustment,
                'adjusted_price' => $adjustedPrice,
                'price_per_sqft' => $pricePerSqFt,
            ];
        }

        return $comparisonData;
    }
}
