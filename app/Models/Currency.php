<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Currency extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'symbol',
        'is_active',
        'is_base',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_base' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (Currency $currency) {
            $currency->enforceBusinessRules();
        });

        static::deleting(function (Currency $currency) {
            if ($currency->is_base) {
                throw ValidationException::withMessages([
                    'currency' => 'Impossible de supprimer la devise de base.',
                ]);
            }
        });
    }

    public function enforceBusinessRules()
    {
        // 1. Exactly one base currency per tenant
        if ($this->is_base) {
            // Ensure no other currency is base
            static::where('tenant_id', $this->tenant_id)
                ->where('id', '!=', $this->id)
                ->where('is_base', true)
                ->update(['is_base' => false]);
            
            // Base currency must be active
            $this->is_active = true;
        } else {
            // Check if this is the only currency, it MUST be base
            $count = static::where('tenant_id', $this->tenant_id)->count();
            if ($count === 0) {
                $this->is_base = true;
                $this->is_active = true;
            }
        }

        // 2. Min 2, Max 3 active currencies
        $activeCount = static::where('tenant_id', $this->tenant_id)
            ->where('is_active', true)
            ->where('id', '!=', $this->id)
            ->count();
        
        if ($this->is_active) {
            $activeCount++;
        }

        if ($activeCount > 3) {
            throw ValidationException::withMessages([
                'is_active' => 'Un maximum de 3 devises actives est autorisé.',
            ]);
        }

        // Only enforce min rule if not creating the first few currencies
        $totalCount = static::where('tenant_id', $this->tenant_id)->count();
        if ($totalCount >= 2 && $activeCount < 2) {
            throw ValidationException::withMessages([
                'is_active' => 'Un minimum de 2 devises actives est requis.',
            ]);
        }
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
