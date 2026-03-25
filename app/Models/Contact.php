<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'contact_type',
        'status',
        'source',
        'priority',
        'assigned_to',
        'last_contact_date',
    ];

    protected $casts = [
        'last_contact_date' => 'datetime',
    ];

    /**
     * Get the full name of the contact.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user assigned to this contact.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all notes for this contact.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(ContactNote::class);
    }

    /**
     * Get all tags for this contact.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ContactTag::class, 'contact_tag');
    }

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'phone', 'status', 'contact_type', 'assigned_to'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
