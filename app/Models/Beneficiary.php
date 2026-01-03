<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\BelongsToTenant;

class Beneficiary extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'type',
        'email',
        'phone',
        'address',
        'tax_number',
        'bank_details',
        'is_active',
    ];

    protected $casts = [
        'bank_details' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCompanies(Builder $query): Builder
    {
        return $query->where('type', 'company');
    }

    public function scopeIndividuals(Builder $query): Builder
    {
        return $query->where('type', 'individual');
    }
}
