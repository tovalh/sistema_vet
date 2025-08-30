<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'email',
        'phone',
        'address',
        'logo_url',
        'settings',
        'status',
        'trial_ends_at',
        'suspended_at',
        'suspension_reason',
        'public_booking_enabled',
        'primary_color',
        'booking_settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'booking_settings' => 'array',
        'trial_ends_at' => 'datetime',
        'suspended_at' => 'datetime',
        'public_booking_enabled' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function mainBranch(): HasOne
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['active', 'trial'])
            ->where('ends_at', '>', now())
            ->latest();
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'clinic-owner');
            });
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
