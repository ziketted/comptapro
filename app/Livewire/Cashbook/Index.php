<?php

namespace App\Livewire\Cashbook;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Operation;
use App\Services\AccountingService;

class Index extends Component
{
    use WithPagination;

    public $selectedCashboxId;
    public $balances = [];

    public function mount()
    {
        $tenant = auth()->user()->tenant;
        $firstBox = $tenant->cashboxes()->first();
        if ($firstBox) {
            $this->selectedCashboxId = $firstBox->id;
        }
        $this->loadBalances();
    }

    public function updatedSelectedCashboxId()
    {
        $this->resetPage();
        $this->loadBalances();
    }

    public function loadBalances()
    {
        if (!$this->selectedCashboxId) return;

        $tenant = auth()->user()->tenant;
        $cashbox = Cashbox::findOrFail($this->selectedCashboxId);
        $currencies = $tenant->activeCurrencies();
        $service = app(AccountingService::class);

        $this->balances = [];
        foreach ($currencies as $curr) {
            $this->balances[] = [
                'currency' => $curr->code,
                'symbol' => $curr->symbol,
                'balance' => $service->getBalance($cashbox, $curr),
            ];
        }
    }

    public function render()
    {
        $query = Operation::where('status', Operation::STATUS_VALIDATED)
            ->with(['currency', 'creator'])
            ->orderBy('validated_at', 'desc');

        if ($this->selectedCashboxId) {
            $query->where(function($q) {
                $q->where('cashbox_id', $this->selectedCashboxId)
                  ->orWhere('target_cashbox_id', $this->selectedCashboxId);
            });
        }

        return view('livewire.cashbook.index', [
            'cashboxes' => auth()->user()->tenant->cashboxes,
            'operations' => $query->paginate(20),
        ])->layout('layouts.app');
    }
}
