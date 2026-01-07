<?php

namespace App\Livewire\Operations;

use Livewire\Component;
use App\Models\Operation;
use App\Models\Account;
use App\Models\Beneficiary;
use App\Models\Currency;
use App\Models\Cashbox;
use App\Services\OperationService;

class Edit extends Component
{
    public Operation $operation;
    
    public $type;
    public $cashbox_id;
    public $target_cashbox_id;
    public $account_id;
    public $beneficiary_id;
    public $currency_id;
    public $original_amount;
    public $operation_date;
    public $description;
    public $reference;

    public function mount(Operation $operation)
    {
        // Only managers can edit
        if (!auth()->user()->isManager()) {
            abort(403, 'Seuls les managers peuvent modifier les opérations.');
        }

        // Only PENDING operations can be edited
        if ($operation->status !== Operation::STATUS_PENDING) {
            session()->flash('error', 'Seules les opérations en attente peuvent être modifiées.');
            return redirect()->route('operations.validate');
        }

        $this->operation = $operation;
        
        // Load current values
        $this->type = $operation->type;
        $this->cashbox_id = $operation->cashbox_id;
        $this->target_cashbox_id = $operation->target_cashbox_id;
        $this->account_id = $operation->account_id;
        $this->beneficiary_id = $operation->beneficiary_id;
        $this->currency_id = $operation->currency_id;
        $this->original_amount = $operation->original_amount;
        $this->operation_date = $operation->operation_date->format('Y-m-d');
        $this->description = $operation->description;
        $this->reference = $operation->reference;
    }

    protected function rules()
    {
        $rules = [
            'type' => 'required|in:INCOME,EXPENSE,EXCHANGE',
            'operation_date' => 'required|date',
            'original_amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'required|string|max:500',
        ];

        if ($this->type === 'EXCHANGE') {
            $rules['cashbox_id'] = 'required|exists:cashboxes,id';
            $rules['target_cashbox_id'] = 'required_if:type,EXCHANGE|nullable|exists:cashboxes,id';
        } else {
            $rules['cashbox_id'] = 'required|exists:cashboxes,id';
            $rules['account_id'] = 'required|exists:accounts,id';
            $rules['beneficiary_id'] = 'nullable|exists:beneficiaries,id';
        }

        $tenant = auth()->user()->tenant;
        if ($tenant->enable_reference) {
            $rules['reference'] = 'nullable|string|max:100';
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        // Verify operation is still pending
        if ($this->operation->status !== Operation::STATUS_PENDING) {
            session()->flash('error', 'Cette opération ne peut plus être modifiée.');
            return redirect()->route('operations.validate');
        }

        try {
            $operationService = app(OperationService::class);
            
            $data = [
                'type' => $this->type,
                'cashbox_id' => $this->cashbox_id,
                'target_cashbox_id' => $this->target_cashbox_id,
                'account_id' => $this->account_id,
                'beneficiary_id' => $this->beneficiary_id,
                'currency_id' => $this->currency_id,
                'original_amount' => $this->original_amount,
                'operation_date' => $this->operation_date,
                'description' => $this->description,
                'reference' => $this->reference,
            ];

            $operationService->updateOperation($this->operation, $data);

            session()->flash('success', 'Opération modifiée avec succès.');
            return redirect()->route('operations.validate');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function updatedType()
    {
        // Reset fields when type changes
        if ($this->type !== 'EXCHANGE') {
            $this->target_cashbox_id = null;
        }
        
        if ($this->type === 'EXCHANGE') {
            $this->account_id = null;
        }
    }

    public function render()
    {
        $tenant = auth()->user()->tenant;
        
        $cashboxes = Cashbox::where('tenant_id', $tenant->id)->get();
        $accounts = Account::where('tenant_id', $tenant->id)->active()->get();
        $beneficiaries = Beneficiary::where('tenant_id', $tenant->id)->active()->get();
        $currencies = Currency::where('tenant_id', $tenant->id)->where('is_active', true)->get();

        return view('livewire.operations.edit', [
            'cashboxes' => $cashboxes,
            'accounts' => $accounts,
            'beneficiaries' => $beneficiaries,
            'currencies' => $currencies,
        ])->layout('layouts.app');
    }
}
