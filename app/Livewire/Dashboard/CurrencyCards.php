<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Account;
use App\Models\Operation;
use App\Models\Currency;
use App\Services\AccountingService;

class CurrencyCards extends Component
{
    public $currenciesData = [];

    public function mount()
    {
        $this->loadCurrencyData();
    }

    public function loadCurrencyData()
    {
        $tenant = auth()->user()->tenant;
        $activeCurrencies = $tenant->activeCurrencies();

        foreach ($activeCurrencies as $currency) {
            // Calculate total balance from all cashboxes using AccountingService
            // This properly handles INCOME, EXPENSE, and EXCHANGE (transfers) operations
            $accountingService = app(AccountingService::class);
            $totalBalance = 0;
            
            foreach ($tenant->cashboxes as $cashbox) {
                $totalBalance += $accountingService->getBalance($cashbox, $currency);
            }

            // Calculate trend (last 30 days vs previous 30 days)
            $currentPeriodIncome = Operation::income()
                ->validated()
                ->where('currency_id', $currency->id)
                ->where('operation_date', '>=', now()->subDays(30))
                ->sum('original_amount');

            $previousPeriodIncome = Operation::income()
                ->validated()
                ->where('currency_id', $currency->id)
                ->where('operation_date', '>=', now()->subDays(60))
                ->where('operation_date', '<', now()->subDays(30))
                ->sum('original_amount');

            $trend = 0;
            if ($previousPeriodIncome > 0) {
                $trend = (($currentPeriodIncome - $previousPeriodIncome) / $previousPeriodIncome) * 100;
            } elseif ($currentPeriodIncome > 0) {
                $trend = 100;
            }

            $this->currenciesData[] = [
                'code' => $currency->code,
                'amount' => $totalBalance,
                'formatted' => $this->formatCurrency($totalBalance, $currency),
                'trend' => [
                    'percentage' => round($trend, 1),
                    'direction' => $trend >= 0 ? 'up' : 'down',
                    'color' => $trend >= 0 ? 'emerald' : 'red'
                ],
                'icon' => $this->getCurrencyIcon($currency->code),
                'color' => $this->getCurrencyColor($currency->code)
            ];
        }
    }

    private function formatCurrency($amount, $currency)
    {
        $symbol = $currency->symbol ?? $currency->code;

        if ($currency->code === 'CDF') {
            return $symbol . ' ' . number_format($amount, 0, ',', ' ');
        }

        return $symbol . ' ' . number_format($amount, 2, '.', ',');
    }

    public function getCurrencyIcon($code)
    {
        $icons = [
            'USD' => 'lucide:dollar-sign',
            'EUR' => 'lucide:euro',
            'CDF' => 'lucide:banknote'
        ];

        return $icons[$code] ?? 'lucide:coins';
    }

    public function getCurrencyColor($code)
    {
        $colors = [
            'USD' => 'blue',
            'EUR' => 'violet',
            'CDF' => 'emerald'
        ];

        return $colors[$code] ?? 'slate';
    }

    public function render()
    {
        return view('livewire.dashboard.currency-cards');
    }
}
