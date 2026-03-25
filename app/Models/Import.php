<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'original_filename',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'skipped_rows',
        'status',
        'mapping',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'mapping' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who initiated the import.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the errors associated with this import.
     */
    public function errors(): HasMany
    {
        return $this->hasMany(ImportError::class);
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_rows === 0) {
            return 0;
        }

        $processed = $this->successful_rows + $this->failed_rows + $this->skipped_rows;
        return (int) round(($processed / $this->total_rows) * 100);
    }

    /**
     * Check if import is complete.
     */
    public function isComplete(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }

    /**
     * Check if import is in progress.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
