<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ContactNote extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'contact_id',
        'user_id',
        'content',
        'is_pinned',
        'is_visible_to_client',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_visible_to_client' => 'boolean',
    ];

    /**
     * Scope to get notes visible to client
     */
    public function scopeVisibleToClient($query)
    {
        return $query->where('is_visible_to_client', true);
    }

    /**
     * Get the contact that owns this note.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user who created this note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content', 'is_pinned'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
