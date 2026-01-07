<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Account;
use App\Models\Beneficiary;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user has tenant
        if (!$user->tenant_id) {
            return redirect()->route('organization.setup')->with('error', 'Veuillez configurer votre organisation.');
        }

        $tenant = $user->tenant;

        // Get summary statistics
        $totalAccounts = Account::active()->count();
        $totalBeneficiaries = Beneficiary::active()->count();
        $pendingTransactions = Operation::pending()->count();

        // Get total balance using AccountingService (properly handles transfers and multi-cashbox)
        $accountingService = app(AccountingService::class);
        $balancesByCurrency = [];
        
        foreach ($tenant->cashboxes as $cashbox) {
            foreach ($tenant->activeCurrencies() as $currency) {
                $balance = $accountingService->getBalance($cashbox, $currency);
                
                if (!isset($balancesByCurrency[$currency->code])) {
                    $balancesByCurrency[$currency->code] = [
                        'amount' => 0,
                        'symbol' => $currency->symbol,
                        'code' => $currency->code,
                    ];
                }
                
                $balancesByCurrency[$currency->code]['amount'] += $balance;
            }
        }
        
        // Calculate total balance by converting all currencies to base currency
        $baseCurrency = $tenant->baseCurrency();
        $totalBalance = 0;
        
        if ($baseCurrency) {
            foreach ($balancesByCurrency as $currencyCode => $data) {
                if ($currencyCode === $baseCurrency->code) {
                    // Already in base currency
                    $totalBalance += $data['amount'];
                } else {
                    // Convert to base currency using exchange rate
                    // Try direct rate first (e.g., CDF -> USD)
                    $rate = ExchangeRate::getRate($currencyCode, $baseCurrency->code);
                    
                    if ($rate == 1.0) {
                        // No direct rate found, try inverse rate (e.g., USD -> CDF = 2200, so CDF -> USD = 1/2200)
                        $inverseRate = ExchangeRate::getRate($baseCurrency->code, $currencyCode);
                        if ($inverseRate != 1.0) {
                            $rate = 1 / $inverseRate;
                        }
                    }
                    
                    $convertedAmount = $data['amount'] * $rate;
                    $totalBalance += $convertedAmount;
                }
            }
        }
        
        // Keep legacy calculations for monthly statistics
        $income = Operation::validated()->where('type', Operation::TYPE_INCOME)->sum('converted_amount');
        $expense = Operation::validated()->where('type', Operation::TYPE_EXPENSE)->sum('converted_amount');

        // Get recent operations
        $recentTransactions = Operation::with(['account', 'beneficiary', 'currency', 'creator'])
            ->orderBy('operation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get monthly summary (Current month)
        $monthlyIncome = Operation::income()
            ->validated()
            ->whereMonth('operation_date', now()->month)
            ->whereYear('operation_date', now()->year)
            ->sum('converted_amount') ?? 0;

        $monthlyExpenses = Operation::expense()
            ->validated()
            ->whereMonth('operation_date', now()->month)
            ->whereYear('operation_date', now()->year)
            ->sum('converted_amount') ?? 0;

        // Get monthly operations summary for last 6 months
        $monthlyOperations = collect();
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $monthIncome = Operation::validated()
                ->where('type', Operation::TYPE_INCOME)
                ->whereMonth('operation_date', $date->month)
                ->whereYear('operation_date', $date->year)
                ->sum('converted_amount') ?? 0;
            
            $monthExpense = Operation::validated()
                ->where('type', Operation::TYPE_EXPENSE)
                ->whereMonth('operation_date', $date->month)
                ->whereYear('operation_date', $date->year)
                ->sum('converted_amount') ?? 0;
            
            $monthlyOperations->push([
                'month' => strtoupper($date->translatedFormat('F Y')),
                'total' => $monthIncome - $monthExpense,
                'income' => $monthIncome,
                'expense' => $monthExpense,
                'income_trend' => $monthIncome > 0 ? 'up' : 'neutral',
                'expense_trend' => $monthExpense > 0 ? 'down' : 'neutral',
            ]);
        }


        return view('dashboard', [
            'organization' => $tenant,
            'totalAccounts' => $totalAccounts,
            'totalBeneficiaries' => $totalBeneficiaries,
            'pendingTransactions' => $pendingTransactions,
            'totalBalance' => $totalBalance,
            'recentTransactions' => $recentTransactions,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'income' => $income,
            'expense' => $expense,
            'monthlyOperations' => $monthlyOperations,
        ]);
    }
}