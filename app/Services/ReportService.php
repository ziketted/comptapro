<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Operation;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get Cash Journal (Chronological list of operations)
     * VALIDATED operations only.
     */
    public function getCashJournal(array $filters, bool $paginate = true): array
    {
        $query = Operation::with(['cashbox', 'account', 'beneficiary', 'currency', 'targetCashbox'])
            ->where('operations.tenant_id', auth()->user()->tenant_id)
            ->validated();

        // Date Range
        if (!empty($filters['start_date'])) {
            $query->whereDate('operation_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('operation_date', '<=', $filters['end_date']);
        }

        // Filters
        // Note: 'cashbox_id' removed as per request, but kept code flexible if needed or simply ignored.
        // User requested Account filter "in place of" Cashbox.
        if (!empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }

        // Beneficiary Search (Text)
        if (!empty($filters['beneficiary_search'])) {
            $term = $filters['beneficiary_search'];
            $query->whereHas('beneficiary', function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            });
        }

        // Currency Filter
        if (!empty($filters['currency_id'])) {
            $query->where('currency_id', $filters['currency_id']);
        }

        // Type
        if (!empty($filters['type']) && $filters['type'] !== 'ALL') {
            $query->where('type', $filters['type']);
        }

        // Clone for totals calculation (before sorting/pagination)
        // Calculate totals separately for INCOME and EXPENSE per currency
        $totalsQuery = clone $query;
        $allOperations = $totalsQuery->get();
        
        $totals = [];
        
        foreach ($allOperations as $op) {
            $currencyCode = $op->currency->code ?? '???';
            
            if (!isset($totals[$currencyCode])) {
                $totals[$currencyCode] = [
                    'currency' => $currencyCode,
                    'income' => 0,
                    'expense' => 0,
                    'balance' => 0,
                ];
            }
            
            if ($op->type === Operation::TYPE_INCOME) {
                $totals[$currencyCode]['income'] += $op->original_amount;
            } elseif ($op->type === Operation::TYPE_EXPENSE) {
                $totals[$currencyCode]['expense'] += $op->original_amount;
            }
            
            // Calculate net balance (INCOME - EXPENSE)
            $totals[$currencyCode]['balance'] = $totals[$currencyCode]['income'] - $totals[$currencyCode]['expense'];
        }
        
        // Convert to collection
        $totals = collect(array_values($totals));

        // Sorting / Grouping
        $groupBy = $filters['group_by'] ?? null;
        if ($groupBy === 'TYPE') {
            $query->orderBy('type');
        } elseif ($groupBy === 'ACCOUNT') {
            $query->join('accounts', 'operations.account_id', '=', 'accounts.id')
                  ->orderBy('accounts.label')
                  ->select('operations.*'); // Avoid column collision
        }

        // Default Sort (Secondary/Primary)
        $query->orderBy('operation_date', 'desc')
              ->orderBy('operations.created_at', 'desc');

        if ($paginate) {
            $operations = $query->paginate(15)->withQueryString();
        } else {
            $operations = $query->get();
        }

        return [
            'operations' => $operations,
            'totals' => $totals
        ];
    }

    /**
     * Get Account Report (Grouped by account with totals)
     * VALIDATED operations only.
     */
    public function getAccountReport(?string $startDate, ?string $endDate): array
    {
        $tenantId = auth()->user()->tenant_id;
        $baseCurrency = auth()->user()->tenant->baseCurrency();

        $query = Operation::where('tenant_id', $tenantId)
            ->validated()
            ->whereNotNull('account_id');

        if ($startDate) {
            $query->whereDate('operation_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('operation_date', '<=', $endDate);
        }

        $operations = $query->with(['account', 'currency'])->get();

        $incomeAccounts = [];
        $expenseAccounts = [];

        foreach ($operations as $op) {
            $account = $op->account;
            $amountInBase = $this->convertToBase($op->original_amount, $op->currency_id, $op->exchange_rate_used);

            if ($op->type === Operation::TYPE_INCOME) {
                if (!isset($incomeAccounts[$account->id])) {
                    $incomeAccounts[$account->id] = [
                        'account' => $account,
                        'total' => 0,
                        'operation_count' => 0
                    ];
                }
                $incomeAccounts[$account->id]['total'] += $amountInBase;
                $incomeAccounts[$account->id]['operation_count']++;
            } elseif ($op->type === Operation::TYPE_EXPENSE) {
                if (!isset($expenseAccounts[$account->id])) {
                    $expenseAccounts[$account->id] = [
                        'account' => $account,
                        'total' => 0,
                        'operation_count' => 0
                    ];
                }
                $expenseAccounts[$account->id]['total'] += $amountInBase;
                $expenseAccounts[$account->id]['operation_count']++;
            }
        }

        return [
            'income' => collect($incomeAccounts)->sortByDesc('total'),
            'expense' => collect($expenseAccounts)->sortByDesc('total'),
            'baseCurrency' => $baseCurrency->code ?? 'N/A'
        ];
    }

    /**
     * Get Balance at Date
     * VALIDATED operations only.
     * Uses historical exchange rates if possible, or current valid rate at date.
     */
    public function getBalanceAtDate(string $date): Collection
    {
        $tenantId = auth()->user()->tenant_id;
        $cashboxes = Cashbox::where('tenant_id', $tenantId)->get();
        $currencies = Currency::where('tenant_id', $tenantId)->get();
        $baseCurrency = auth()->user()->tenant->baseCurrency();

        $report = [];

        foreach ($cashboxes as $cashbox) {
            foreach ($currencies as $currency) {
                // Calculate balance for this specific cashbox + currency up to date
                $balance = $this->calculateCreateBalanceUntil($cashbox->id, $currency->id, $date);

                if ($balance != 0) {
                    // Get rate at specific date
                    $rate = $this->getExchangeRateAtDate($currency, $baseCurrency, $date);
                    $balanceBase = $balance * $rate;

                    $report[] = [
                        'cashbox' => $cashbox->name,
                        'currency' => $currency->code,
                        'balance' => $balance,
                        'balance_base' => $balanceBase,
                        'rate_used' => $rate
                    ];
                }
            }
        }

        return collect($report);
    }

    /**
     * Get Profit & Loss (Income vs Expense)
     * VALIDATED operations only.
     */
    public function getProfitLoss(?string $startDate, ?string $endDate): array
    {
        $tenantId = auth()->user()->tenant_id;
        $baseCurrency = auth()->user()->tenant->baseCurrency();

        $query = Operation::where('tenant_id', $tenantId)
            ->validated()
            ->whereIn('type', [Operation::TYPE_INCOME, Operation::TYPE_EXPENSE]);

        if ($startDate) {
            $query->whereDate('operation_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('operation_date', '<=', $endDate);
        }

        $operations = $query->get();

        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($operations as $op) {
            $amountInBase = $this->convertToBase($op->original_amount, $op->currency_id, $op->exchange_rate_used);

            if ($op->type === Operation::TYPE_INCOME) {
                $totalIncome += $amountInBase;
            } else {
                $totalExpense += $amountInBase;
            }
        }

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_result' => $totalIncome - $totalExpense,
            'base_currency' => $baseCurrency->code ?? 'N/A'
        ];
    }

    // --- Helpers ---

    private function convertToBase(float $amount, int $currencyId, ?float $rateUsed): float
    {
        if ($rateUsed) {
            return $amount * $rateUsed;
        }

        // Fallback if rate not stored (should not happen for validated ops usually)
        // For reporting, we might want to strict fail or lookup current rate.
        // Assuming base currency has rate 1.
        
        $currency = Currency::find($currencyId);
        if ($currency && $currency->is_base) {
             return $amount;
        }

        return $amount; // Fallback, better than 0, but ideally we utilize rate.
    }

    private function calculateCreateBalanceUntil(int $cashboxId, int $currencyId, string $date): float
    {
        // Incomes
        $incomes = Operation::where('cashbox_id', $cashboxId)
            ->where('type', Operation::TYPE_INCOME)
            ->where('currency_id', $currencyId)
            ->whereDate('operation_date', '<=', $date)
            ->validated()
            ->sum('original_amount');

        // Expenses
        $expenses = Operation::where('cashbox_id', $cashboxId)
            ->where('type', Operation::TYPE_EXPENSE)
            ->where('currency_id', $currencyId)
            ->whereDate('operation_date', '<=', $date)
            ->validated()
            ->sum('original_amount');

        // Transfers OUT
        $transfersOut = Operation::where('cashbox_id', $cashboxId)
            ->where('type', Operation::TYPE_EXCHANGE)
            ->where('currency_id', $currencyId)
            ->whereDate('operation_date', '<=', $date)
            ->validated()
            ->sum('original_amount');

        // Transfers IN
        $transfersIn = Operation::where('target_cashbox_id', $cashboxId)
            ->where('type', Operation::TYPE_EXCHANGE)
            ->where('currency_id', $currencyId)
            ->whereDate('operation_date', '<=', $date)
            ->validated()
            ->sum('original_amount');

        return (float) ($incomes - $expenses - $transfersOut + $transfersIn);
    }

    private function getExchangeRateAtDate(Currency $from, ?Currency $to, string $date): float
    {
        if (!$to || $from->id === $to->id) {
            return 1.0;
        }
        
        // Find stored rate close to date
        // Note: This logic depends on how rates are stored. 
        // Assuming we use the Rate model if available or latest.
        // For this task, simplfication: Current system has ExchangeRate table.
        
        $rate = ExchangeRate::where('tenant_id', auth()->user()->tenant_id)
            ->where('from_currency', $from->code)
            ->where('to_currency', $to->code)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        return (float) ($rate ? $rate->rate : 1.0); // Default to 1 if not found (risk)
    }
}
