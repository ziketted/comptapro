<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountingService
{
    /**
     * Calculate the balance of a specific cashbox for a specific currency.
     * ONLY considers VALIDATED operations.
     */
    public function getBalance(Cashbox $cashbox, Currency $currency): float
    {
        $tenantId = $cashbox->tenant_id;

        // 1. Incomes (+)
        $incomes = Operation::where('cashbox_id', $cashbox->id)
            ->where('type', Operation::TYPE_INCOME)
            ->where('currency_id', $currency->id)
            ->validated()
            ->sum('original_amount');

        // 2. Expenses (-)
        $expenses = Operation::where('cashbox_id', $cashbox->id)
            ->where('type', Operation::TYPE_EXPENSE)
            ->where('currency_id', $currency->id)
            ->validated()
            ->sum('original_amount');

        // 3. Transfers OUT (EXCHANGE where this box is source) (-)
        $transfersOut = Operation::where('cashbox_id', $cashbox->id)
            ->where('type', Operation::TYPE_EXCHANGE)
            ->where('currency_id', $currency->id)
            ->validated()
            ->sum('original_amount');

        // 4. Transfers IN (EXCHANGE where this box is target) (+)
        $transfersIn = Operation::where('target_cashbox_id', $cashbox->id)
            ->where('type', Operation::TYPE_EXCHANGE)
            ->where('currency_id', $currency->id)
            ->validated()
            ->sum('original_amount');

        return (float) ($incomes - $expenses - $transfersOut + $transfersIn);
    }

    /**
     * Validate a pending operation.
     * Only Managers can perform this.
     */
    public function validate(Operation $operation, User $validator): bool
    {
        if (!$validator->isManager()) {
            throw ValidationException::withMessages([
                'auth' => 'Seul un gestionnaire peut valider les opérations.',
            ]);
        }

        if ($operation->status !== Operation::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Seules les opérations en attente peuvent être validées.',
            ]);
        }

        return $operation->update([
            'status' => Operation::STATUS_VALIDATED,
            'validated_by' => $validator->id,
            'validated_at' => now(),
        ]);
    }

    /**
     * Reject a pending operation.
     */
    public function reject(Operation $operation, User $validator): bool
    {
        if (!$validator->isManager()) {
            throw ValidationException::withMessages([
                'auth' => 'Seul un gestionnaire peut rejeter les opérations.',
            ]);
        }

        if ($operation->status !== Operation::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Seules les opérations en attente peuvent être rejetées.',
            ]);
        }

        return $operation->update([
            'status' => Operation::STATUS_REJECTED,
            'validated_by' => $validator->id,
            'validated_at' => now(),
        ]);
    }

    /**
     * Get all pending operations for a tenant.
     */
    public function getPendingOperations(int $tenantId)
    {
        return Operation::where('tenant_id', $tenantId)
            ->where('status', Operation::STATUS_PENDING)
            ->with(['cashbox', 'currency', 'creator'])
            ->orderBy('operation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
