<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Account;
use App\Models\Beneficiary;
use App\Models\Currency;
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

        // Get total balance in base currency
        $income = Operation::validated()->where('type', Operation::TYPE_INCOME)->sum('converted_amount');
        $expense = Operation::validated()->where('type', Operation::TYPE_EXPENSE)->sum('converted_amount');
        $totalBalance = $income - $expense;

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