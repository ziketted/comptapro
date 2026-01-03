<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class ExchangeRate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'from_currency',
        'to_currency',
        'rate',
        'date',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getRate(string $fromCurrency, string $toCurrency, $date = null): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $date = $date ?? now()->toDateString();

        $rate = static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        return $rate ? (float) $rate->rate : 1.0;
    }

    public static function convert(float $amount, string $fromCurrency, string $toCurrency, $date = null): float
    {
        $rate = static::getRate($fromCurrency, $toCurrency, $date);
        return $amount * $rate;
    }
}
