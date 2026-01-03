<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Account;

class Index extends Component
{
    use WithPagination;

    public $typeFilter = ''; // 'INCOME' or 'EXPENSE'
    public $search = '';
    
    public $showModal = false;
    public $editingAccount = null;

    // Form fields
    public $label;
    public $type = 'INCOME';
    public $account_number;
    public $is_active = true;

    protected $queryString = ['typeFilter' => ['except' => '']];

    public function mount($type = null)
    {
        if ($type) {
            $this->typeFilter = strtoupper($type);
            $this->type = $this->typeFilter;
        }
    }

    public function createAccount()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editAccount(Account $account)
    {
        $this->editingAccount = $account;
        $this->label = $account->label;
        $this->type = $account->type;
        $this->account_number = $account->account_number;
        $this->is_active = $account->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'label' => 'required|string|max:255',
            'type' => 'required|in:INCOME,EXPENSE',
            'account_number' => 'required|string|unique:accounts,account_number,' . ($this->editingAccount->id ?? 'NULL') . ',id,tenant_id,' . auth()->user()->tenant_id,
        ];

        $this->validate($rules);

        if ($this->editingAccount) {
            $this->editingAccount->update([
                'label' => $this->label,
                'type' => $this->type,
                'account_number' => $this->account_number,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Compte mis à jour.');
        } else {
            Account::create([
                'label' => $this->label,
                'type' => $this->type,
                'account_number' => $this->account_number,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Compte créé avec succès.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleStatus(Account $account)
    {
        $account->update(['is_active' => !$account->is_active]);
    }

    private function resetForm()
    {
        $this->editingAccount = null;
        $this->label = '';
        $this->type = $this->typeFilter ?: 'INCOME';
        $this->account_number = '';
        $this->is_active = true;
    }

    public function render()
    {
        $query = Account::query()
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->search, fn($q) => $q->where('label', 'like', '%' . $this->search . '%')
                                           ->orWhere('account_number', 'like', '%' . $this->search . '%'))
            ->orderBy('account_number');

        return view('livewire.accounts.index', [
            'accounts' => $query->paginate(20)
        ])->layout('layouts.app');
    }
}
