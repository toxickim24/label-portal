<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
        'is_suspended',
        'approved_at',
        'approved_by',
        'suspended_at',
        'suspended_by',
        'invitation_token',
        'invitation_sent_at',
        'invitation_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'is_suspended' => 'boolean',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
            'invitation_sent_at' => 'datetime',
            'invitation_accepted_at' => 'datetime',
        ];
    }

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_approved', 'is_suspended'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relationships
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_approved', true)
                    ->where('is_suspended', false);
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_approved && !$this->is_suspended;
    }

    /**
     * Check if user needs approval
     */
    public function needsApproval(): bool
    {
        return !$this->is_approved;
    }

    /**
     * Check if user has a pending invitation
     */
    public function hasPendingInvitation(): bool
    {
        return !empty($this->invitation_token) && is_null($this->invitation_accepted_at);
    }

    /**
     * Check if user is a client
     */
    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    /**
     * Get client notifications
     */
    public function clientNotifications()
    {
        return $this->hasMany(ClientNotification::class);
    }

    /**
     * Get client activities
     */
    public function clientActivities()
    {
        return $this->hasMany(ClientActivity::class);
    }
}
