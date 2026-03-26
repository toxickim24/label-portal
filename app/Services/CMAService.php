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
     * Generate valuation summary paragraph
     */
    public function generateValuationSummary(CMAReport $report): string
    {
        $comparableCount = count($report->comparables ?? []);
        $avgValue = number_format($report->valuation_avg, 0);
        $lowValue = number_format($report->valuation_low, 0);
        $highValue = number_format($report->valuation_high, 0);
        $address = $report->subject_property['address'] ?? 'the subject property';

        if ($comparableCount === 0) {
            return "No comparable properties have been added to this analysis yet.";
        }

        $summary = "Based on a comprehensive analysis of {$comparableCount} comparable " .
                   ($comparableCount === 1 ? 'property' : 'properties') .
                   " in the surrounding area, the estimated market value for {$address} is \${$avgValue}. ";

        $summary .= "The valuation range spans from a low estimate of \${$lowValue} to a high estimate of \${$highValue}, ";

        $range = $report->valuation_high - $report->valuation_low;
        $rangePercent = round(($range / $report->valuation_avg) * 100, 1);

        if ($rangePercent < 10) {
            $summary .= "indicating a tight market consensus with strong pricing confidence. ";
        } elseif ($rangePercent < 20) {
            $summary .= "reflecting a moderate range typical of stable market conditions. ";
        } else {
            $summary .= "suggesting some market variability that should be considered in pricing decisions. ";
        }

        $summary .= "This valuation takes into account property characteristics, location factors, condition differences, and recent market activity.";

        return $summary;
    }

    /**
     * Calculate confidence score for the valuation (1-5 scale)
     */
    public function calculateConfidenceScore(CMAReport $report): array
    {
        $score = 5.0; // Start at maximum
        $reasons = [];

        $comparableCount = count($report->comparables ?? []);

        // Factor 1: Number of comparables (weight: 30%)
        if ($comparableCount < 3) {
            $score -= 1.5;
            $reasons[] = "Limited number of comparables ({$comparableCount})";
        } elseif ($comparableCount < 5) {
            $score -= 0.5;
            $reasons[] = "Moderate number of comparables ({$comparableCount})";
        }

        // Factor 2: Price variance (weight: 30%)
        if ($report->valuation_avg > 0) {
            $range = $report->valuation_high - $report->valuation_low;
            $variance = ($range / $report->valuation_avg) * 100;

            if ($variance > 30) {
                $score -= 1.5;
                $reasons[] = "High price variance (" . round($variance, 1) . "%)";
            } elseif ($variance > 15) {
                $score -= 0.5;
                $reasons[] = "Moderate price variance (" . round($variance, 1) . "%)";
            }
        }

        // Factor 3: Recency of sales (weight: 20%)
        $recentSales = 0;
        $sixMonthsAgo = now()->subMonths(6);

        foreach ($report->comparables ?? [] as $comp) {
            if (!empty($comp['sale_date'])) {
                $saleDate = \Carbon\Carbon::parse($comp['sale_date']);
                if ($saleDate->gte($sixMonthsAgo)) {
                    $recentSales++;
                }
            }
        }

        if ($comparableCount > 0) {
            $recencyRatio = $recentSales / $comparableCount;
            if ($recencyRatio < 0.3) {
                $score -= 1.0;
                $reasons[] = "Most comparables are older than 6 months";
            } elseif ($recencyRatio < 0.6) {
                $score -= 0.3;
            }
        }

        // Factor 4: Adjustments magnitude (weight: 20%)
        $largeAdjustments = 0;
        foreach ($report->comparables ?? [] as $index => $comp) {
            $adjustments = $report->adjustments[$index] ?? [];
            $totalAdj = 0;
            foreach ($adjustments as $adj) {
                $totalAdj += abs((float) ($adj['amount'] ?? 0));
            }
            $salePrice = (float) ($comp['sale_price'] ?? 1);
            if ($salePrice > 0 && ($totalAdj / $salePrice) > 0.15) {
                $largeAdjustments++;
            }
        }

        if ($largeAdjustments > 0) {
            $score -= ($largeAdjustments * 0.3);
            $reasons[] = "{$largeAdjustments} comparable(s) required significant adjustments";
        }

        // Ensure score stays within 1-5 range
        $score = max(1.0, min(5.0, $score));

        // Determine confidence level
        $level = 'Very High';
        if ($score < 2.5) {
            $level = 'Low';
        } elseif ($score < 3.5) {
            $level = 'Moderate';
        } elseif ($score < 4.5) {
            $level = 'High';
        }

        return [
            'score' => round($score, 1),
            'level' => $level,
            'reasons' => $reasons,
        ];
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
