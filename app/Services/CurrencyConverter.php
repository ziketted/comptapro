<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\Tenant;

class CurrencyConverter
{
    protected Tenant $tenant;

    public function __construct(Tenant $tenant = null)
    {
        $this->tenant = $tenant ?? auth()->user()?->tenant;
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency, ?\DateTime $date = null): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency, $toCurrency, $date);

        return round($amount * $rate, 2);
    }

    /**
     * Convert amount to organization's base currency
     */
    public function toBaseCurrency(float $amount, string $fromCurrency, ?\DateTime $date = null): float
    {
        $baseCurrency = $this->tenant->default_currency;
        return $this->convert($amount, $fromCurrency, $baseCurrency, $date);
    }

    /**
     * Get exchange rate between two currencies
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency, ?\DateTime $date = null): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $date = $date ?? now();

        // Try to find direct rate
        $rate = ExchangeRate::where('tenant_id', $this->tenant->id)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        if ($rate) {
            return $rate->rate;
        }

        // Try inverse rate
        $inverseRate = ExchangeRate::where('tenant_id', $this->tenant->id)
            ->where('from_currency', $toCurrency)
            ->where('to_currency', $fromCurrency)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        if ($inverseRate) {
            return 1 / $inverseRate->rate;
        }

        // Try conversion through base currency
        $baseCurrency = $this->tenant->default_currency;

        if ($fromCurrency !== $baseCurrency && $toCurrency !== $baseCurrency) {
            $fromBaseRate = $this->getExchangeRate($fromCurrency, $baseCurrency, $date);
            $toBaseRate = $this->getExchangeRate($baseCurrency, $toCurrency, $date);

            return $fromBaseRate * $toBaseRate;
        }

        // Default rate if no rate found
        return 1.0;
    }

    /**
     * Get current exchange rate for a currency pair
     */
    public function getCurrentRate(string $fromCurrency, string $toCurrency): float
    {
        return $this->getExchangeRate($fromCurrency, $toCurrency, now());
    }

    /**
     * Format currency amount with symbol
     */
    public function formatCurrency(float $amount, string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'CDF' => 'FC'
        ];

        $symbol = $symbols[$currency] ?? $currency;

        if ($currency === 'CDF') {
            return $symbol . ' ' . number_format($amount, 0, ',', '.');
        }

        return $symbol . number_format($amount, 2, '.', ',');
    }
}
