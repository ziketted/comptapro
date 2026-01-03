<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\Tenant;
use Illuminate\Support\Carbon;

class CurrencyConversionService
{
    /**
     * Convert an amount from one currency to another using the exchange rate for a given date.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param Tenant $tenant
     * @param Carbon|null $date
     * @return float
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency, Tenant $tenant, Carbon $date = null): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $dateString = ($date ?? now())->toDateString();

        $rate = ExchangeRate::where('tenant_id', $tenant->id)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('date', '<=', $dateString)
            ->orderBy('date', 'desc')
            ->first();

        // If no direct rate, check for inverse rate
        if (!$rate) {
            $inverseRate = ExchangeRate::where('tenant_id', $tenant->id)
                ->where('from_currency', $toCurrency)
                ->where('to_currency', $fromCurrency)
                ->where('date', '<=', $dateString)
                ->orderBy('date', 'desc')
                ->first();

            if ($inverseRate) {
                return $amount / (float) $inverseRate->rate;
            }

            // If still no rate, throw exception or return original as per business requirement
            // For Compta+ Pro, we require an explicit rate.
            throw new \Exception("Pas de taux de change trouvé entre $fromCurrency et $toCurrency pour la date $dateString.");
        }

        return $amount * (float) $rate->rate;
    }

    /**
     * Convert an amount to the tenant's base currency.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param Tenant $tenant
     * @param Carbon|null $date
     * @return float
     */
    public function convertToBase(float $amount, string $fromCurrency, Tenant $tenant, Carbon $date = null): float
    {
        $baseCurrency = $tenant->baseCurrency();
        
        if (!$baseCurrency) {
             throw new \Exception("La devise de base n'est pas configurée pour ce tenant.");
        }

        return $this->convert($amount, $fromCurrency, $baseCurrency->code, $tenant, $date);
    }
}
