<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CMAReport extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'cma_reports';

    protected $fillable = [
        'contact_id',
        'user_id',
        'title',
        'subject_property',
        'comparables',
        'adjustments',
        'valuation_low',
        'valuation_avg',
        'valuation_high',
        'status',
        'generated_pdf_path',
        'notes',
    ];

    protected $casts = [
        'subject_property' => 'array',
        'comparables' => 'array',
        'adjustments' => 'array',
        'valuation_low' => 'decimal:2',
        'valuation_avg' => 'decimal:2',
        'valuation_high' => 'decimal:2',
    ];

    /**
     * Get the contact this report is for.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user (agent) who created this report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter draft reports
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to filter finalized reports
     */
    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    /**
     * Check if report is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if report is finalized
     */
    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    /**
     * Finalize the report
     */
    public function finalize(): bool
    {
        return $this->update(['status' => 'finalized']);
    }

    /**
     * Get subject property address
     */
    public function getSubjectAddressAttribute(): ?string
    {
        if (!$this->subject_property) {
            return null;
        }

        return $this->subject_property['address'] ?? null;
    }

    /**
     * Get number of comparables
     */
    public function getComparablesCountAttribute(): int
    {
        return count($this->comparables ?? []);
    }

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'valuation_low', 'valuation_avg', 'valuation_high'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
