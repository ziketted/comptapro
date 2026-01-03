<?php

namespace App\Livewire\Operations;

use Livewire\Component;
use App\Models\Operation;
use App\Services\AccountingService;

class Validate extends Component
{
    public function mount()
    {
        // Only managers can access validation screen
        if (!auth()->user()->canValidateTransactions()) {
            abort(403, 'Accès refusé. Seuls les managers peuvent valider les opérations.');
        }
    }

    public function validateOperation($operationId)
    {
        try {
            $operation = Operation::findOrFail($operationId);
            
            $accountingService = new AccountingService();
            $accountingService->validate($operation, auth()->user());

            session()->flash('success', 'Opération validée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    public function rejectOperation($operationId)
    {
        try {
            $operation = Operation::findOrFail($operationId);
            
            $accountingService = new AccountingService();
            $accountingService->reject($operation, auth()->user());

            session()->flash('success', 'Opération rejetée.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du rejet : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $pendingOperations = Operation::with(['cashbox', 'account', 'beneficiary', 'currency', 'creator'])
            ->pending()
            ->orderBy('operation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.operations.validate', [
            'pendingOperations' => $pendingOperations
        ])->layout('layouts.app');
    }
}
