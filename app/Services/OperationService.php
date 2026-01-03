<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OperationService
{
    protected $conversionService;

    public function __construct(CurrencyConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    /**
     * Create a new operation (INCOME or EXPENSE)
     */
    public function createOperation(array $data, User $user): Operation
    {
        return DB::transaction(function () use ($data, $user) {
            $tenant = $user->tenant;
            $currency = Currency::findOrFail($data['currency_id']);
            
            // Get conversion to base currency
            $convertedAmount = $this->conversionService->convertToBase(
                $data['original_amount'],
                $currency->code,
                $tenant
            );

            // Get current rate used for this conversion
            $baseCurrency = $tenant->baseCurrency();
            $rateUsed = 1.0;
            if ($currency->code !== $baseCurrency->code) {
                $rateUsed = $convertedAmount / $data['original_amount'];
            }

            return Operation::create([
                'tenant_id' => $tenant->id,
                'cashbox_id' => $data['cashbox_id'],
                'account_id' => $data['account_id'],
                'beneficiary_id' => $data['beneficiary_id'] ?? null,
                'type' => $data['type'],
                'operation_date' => $data['operation_date'] ?? now()->toDateString(),
                'original_amount' => $data['original_amount'],
                'currency_id' => $currency->id,
                'exchange_rate_used' => $rateUsed,
                'converted_amount' => $convertedAmount,
                'status' => Operation::STATUS_PENDING,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'created_by' => $user->id,
            ]);
        });
    }

    /**
     * Create a transfer between two cashboxes.
     */
    public function createTransfer(array $data, User $user): Operation
    {
        return DB::transaction(function () use ($data, $user) {
            $tenant = $user->tenant;
            $currency = Currency::findOrFail($data['currency_id']);
            
            // Get exchange rate if needed (not usually needed for transfers between same currency cashboxes, 
            // but the system supports different currencies per cashbox potentially)
            $rateUsed = $data['exchange_rate_used'] ?? 1;
            $convertedAmount = $data['original_amount'] * $rateUsed;

            return Operation::create([
                'tenant_id' => $tenant->id,
                'cashbox_id' => $data['cashbox_id'], // Source
                'target_cashbox_id' => $data['target_cashbox_id'], // Target
                'account_id' => null,
                'beneficiary_id' => $data['beneficiary_id'] ?? null,
                'type' => Operation::TYPE_EXCHANGE,
                'operation_date' => $data['operation_date'] ?? now()->toDateString(),
                'original_amount' => $data['original_amount'],
                'currency_id' => $currency->id,
                'exchange_rate_used' => $rateUsed,
                'converted_amount' => $convertedAmount,
                'status' => Operation::STATUS_PENDING,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'created_by' => $user->id,
            ]);
        });
    }
}
