<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        Log::info('Transaction created', [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'user_id' => $transaction->user_id,
        ]);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Log changes to status
        if ($transaction->isDirty('status')) {
            Log::info('Transaction status changed', [
                'id' => $transaction->id,
                'old_status' => $transaction->getOriginal('status'),
                'new_status' => $transaction->status,
                'validated_by' => $transaction->validated_by,
            ]);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        Log::warning('Transaction deleted', [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        Log::info('Transaction restored', [
            'id' => $transaction->id,
        ]);
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        Log::warning('Transaction force deleted', [
            'id' => $transaction->id,
        ]);
    }
}
