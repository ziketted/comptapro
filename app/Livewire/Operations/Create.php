<?php

namespace App\Livewire\Operations;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Operation;
use App\Models\Account;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Beneficiary;
use App\Services\OperationService;
use App\Services\AccountingService;

class Create extends Component
{
    use WithFileUploads;

    public $type = 'EXPENSE';
    public $cashbox_id;
    public $target_cashbox_id;
    public $account_id;
    public $beneficiary_id;
    public $currency_id;
    public $original_amount;
    public $reference;
    public $description;
    public $attachment;
    public $operation_date;

    public $cashboxes = [];
    public $currencies = [];
    public $availableAccounts = [];
    public $beneficiaries = [];

    protected function rules()
    {
        $tenant = auth()->user()->tenant;
        $rules = [
            'type' => 'required|in:INCOME,EXPENSE,EXCHANGE',
            'currency_id' => 'required|exists:currencies,id',
            'original_amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:50',
            'description' => 'required|string|max:255',
            'attachment' => 'nullable|file|max:5120', // Max 5MB
            'operation_date' => 'required|date',
        ];

        if ($tenant->enable_cash_management) {
            $rules['cashbox_id'] = 'required|exists:cashboxes,id';
            $rules['target_cashbox_id'] = 'required_if:type,EXCHANGE|nullable|exists:cashboxes,id';
        }

        if ($tenant->enable_beneficiaries) {
            $rules['beneficiary_id'] = 'required_unless:type,EXCHANGE|nullable|exists:beneficiaries,id';
        }

        if ($tenant->enable_reference) {
            $rules['reference'] = 'nullable|string|max:50';
        }

        if ($tenant->enable_attachment) {
            $rules['attachment'] = 'nullable|file|max:5120'; // Max 5MB
        }

        $rules['account_id'] = 'required_unless:type,EXCHANGE|nullable|exists:accounts,id';

        return $rules;
    }

    public function mount()
    {
        $tenant = auth()->user()->tenant;
        
        // Auto-seed default cashbox if none
        if ($tenant->cashboxes()->count() === 0) {
            $tenant->cashboxes()->create([
                'name' => 'Caisse Principale',
                'description' => 'Caisse centrale de l\'organisation',
                'is_active' => true,
            ]);
        }

        $this->loadOptions();
        $this->loadAccounts();

        $this->operation_date = now()->toDateString();
        
        // Default currency to base
        $base = $tenant->baseCurrency();
        if ($base) {
            $this->currency_id = $base->id;
        }

        // Default cashbox logic
        if (!$tenant->enable_cash_management || !$this->cashbox_id) {
            $mainBox = $tenant->cashboxes()->where('name', 'Caisse Principale')->first();
            if ($mainBox) {
                $this->cashbox_id = $mainBox->id;
            } else {
                $firstBox = $tenant->cashboxes()->first();
                if ($firstBox) {
                    $this->cashbox_id = $firstBox->id;
                }
            }
        }

        if ($tenant->enable_beneficiaries) {
            $this->setDefaultBeneficiary();
        } else {
            $this->beneficiary_id = null;
        }
    }

    private function setDefaultBeneficiary()
    {
        $tenant = auth()->user()->tenant;
        $user = auth()->user();
        
        $beneficiary = Beneficiary::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => $user->name],
            ['type' => 'individual', 'is_active' => true]
        );
        
        $this->beneficiary_id = $beneficiary->id;
        $this->loadOptions(); // Ensure the new beneficiary is in the list
    }

    public function updatedType()
    {
        $tenant = auth()->user()->tenant;
        $this->loadAccounts();
        $this->account_id = null;
        if ($this->type === 'EXCHANGE') {
            $this->target_cashbox_id = null;
            $this->beneficiary_id = null;
        } else {
            if ($tenant->enable_beneficiaries) {
                $this->setDefaultBeneficiary();
            }
        }
    }

    public function loadOptions()
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant->enable_cash_management) {
            $this->cashboxes = $tenant->cashboxes()->where('is_active', true)->get();
        } else {
            $this->cashboxes = []; // Don't load if not needed
        }
        
        $this->currencies = $tenant->activeCurrencies();
        
        if ($tenant->enable_beneficiaries) {
            $this->beneficiaries = Beneficiary::where('tenant_id', $tenant->id)->active()->get();
        } else {
            $this->beneficiaries = [];
        }
    }

    public function loadAccounts()
    {
        if ($this->type === 'EXCHANGE') {
            $this->availableAccounts = [];
            return;
        }

        $tenant = auth()->user()->tenant;
        $this->availableAccounts = Account::where('tenant_id', $tenant->id)
            ->where('type', $this->type)
            ->where('is_active', true)
            ->orderBy('account_number')
            ->get();
    }

    public function save()
    {
        $this->validate();

        $service = app(OperationService::class);
        $user = auth()->user();
        $tenant = $user->tenant;

        // Enforce Backend Safety for Feature Flags
        if (!$tenant->enable_cash_management) {
            // Force default cashbox
            $mainBox = $tenant->cashboxes()->where('name', 'Caisse Principale')->first() 
                ?? $tenant->cashboxes()->first();
            $this->cashbox_id = $mainBox?->id;
        }

        if (!$tenant->enable_beneficiaries) {
            $this->beneficiary_id = null;
        }

        if (!$tenant->enable_reference) {
            $this->reference = null;
        }

        if (!$tenant->enable_attachment) {
            $this->attachment = null;
        }

        try {
            $attachmentPath = null;
            if ($this->attachment && $tenant->enable_attachment) {
                $attachmentPath = $this->attachment->store('attachments/operations', 'public');
            }

            if ($this->type === 'EXCHANGE') {
                if ($this->cashbox_id == $this->target_cashbox_id) {
                    throw new \Exception("La caisse source et cible doivent être différentes.");
                }
                $service->createTransfer([
                    'cashbox_id' => $this->cashbox_id,
                    'target_cashbox_id' => $this->target_cashbox_id,
                    'original_amount' => $this->original_amount,
                    'currency_id' => $this->currency_id,
                    'reference' => $this->reference,
                    'description' => $this->description,
                    'attachment_path' => $attachmentPath,
                    'operation_date' => $this->operation_date,
                ], $user);
            } else {
                $service->createOperation([
                    'type' => $this->type,
                    'cashbox_id' => $this->cashbox_id,
                    'account_id' => $this->account_id,
                    'beneficiary_id' => $this->beneficiary_id,
                    'original_amount' => $this->original_amount,
                    'currency_id' => $this->currency_id,
                    'reference' => $this->reference,
                    'description' => $this->description,
                    'attachment_path' => $attachmentPath,
                    'operation_date' => $this->operation_date,
                ], $user);
            }

            session()->flash('success', 'Opération enregistrée en attente de validation.');
            return redirect()->route('operations.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.operations.create')->layout('layouts.app');
    }
}
