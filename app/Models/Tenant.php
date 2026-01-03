<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'business_type',
        'status', // TRIAL, ACTIVE, EXPIRED
        'email',
        'phone',
        'address',
        'default_currency',
        'on_trial',
        'trial_ends_at',
        'subscription_active',
        'subscription_ends_at',
        'settings',
        'enable_cash_management',
        'enable_beneficiaries',
        'enable_reference',
        'enable_attachment',
    ];

    protected $casts = [
        'on_trial' => 'boolean',
        'subscription_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'settings' => 'array',
        'enable_cash_management' => 'boolean',
        'enable_beneficiaries' => 'boolean',
        'enable_reference' => 'boolean',
        'enable_attachment' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    public function licenseKeys(): HasMany
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

    public function cashboxes(): HasMany
    {
        return $this->hasMany(Cashbox::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function baseCurrency()
    {
        return $this->currencies()->where('is_base', true)->first();
    }

    public function activeCurrencies()
    {
        return $this->currencies()->where('is_active', true)->get();
    }

    public function activeLicense()
    {
        return $this->licenseKeys()->where('status', 'USED')->where('expires_at', '>', now())->first();
    }

    public function hasActiveLicense(): bool
    {
        return $this->licenseKeys()->where('status', 'USED')->where('expires_at', '>', now())->exists();
    }

    public function activateLicense(string $code): bool
    {
        $license = LicenseKey::where('key', $code)->where('status', 'UNUSED')->first();

        if (!$license) {
            return false;
        }

        $license->update([
            'tenant_id' => $this->id,
            'status' => 'USED',
            'activated_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $this->update(['status' => 'ACTIVE']);

        return true;
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->subscription_active || ($this->on_trial && $this->trial_ends_at && $this->trial_ends_at->isFuture())) {
            return true;
        }

        return $this->hasActiveLicense();
    }

    public function getTrialDaysRemaining(): int
    {
        if (!$this->on_trial || !$this->trial_ends_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    public function updateStatus(): void
    {
        // 1. Check trial
        if ($this->status === 'TRIAL' && $this->trial_ends_at && $this->trial_ends_at->isPast()) {
            // Check if there is an active license before marking as EXPIRED
            if (!$this->hasActiveLicense()) {
                $this->update(['status' => 'EXPIRED']);
            } else {
                $this->update(['status' => 'ACTIVE']);
            }
        }

        // 2. Check manual recurring subscription
        if ($this->subscription_active && $this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
             if (!$this->hasActiveLicense()) {
                $this->update(['status' => 'EXPIRED', 'subscription_active' => false]);
            }
        }
        
        // 3. Check License expiration
        $activeLicense = $this->activeLicense();
        if ($this->status === 'ACTIVE' && !$this->subscription_active && !$activeLicense && (!$this->on_trial || $this->trial_ends_at->isPast())) {
            $this->update(['status' => 'EXPIRED']);
        }

        // 4. Mark expired licenses in DB
        $this->licenseKeys()->where('status', 'USED')->where('expires_at', '<=', now())->update(['status' => 'EXPIRED']);
    }

    public function isExpired(): bool
    {
        return $this->status === 'EXPIRED';
    }
}
