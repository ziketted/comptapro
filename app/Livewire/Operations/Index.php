<?php

namespace App\Livewire\Operations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Operation;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Services\AccountingService;

class Index extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterType = '';
    public $filterCashbox = '';

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterCashbox' => ['except' => ''],
    ];

    public function validateOperation($id)
    {
        $operation = Operation::findOrFail($id);
        $service = app(AccountingService::class);
        
        try {
            $service->validate($operation, auth()->user());
            session()->flash('success', "Opération validée avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function rejectOperation($id)
    {
        $operation = Operation::findOrFail($id);
        $service = app(AccountingService::class);
        
        try {
            $service->reject($operation, auth()->user());
            session()->flash('success', "Opération rejetée.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $query = Operation::with(['cashbox', 'currency', 'creator', 'validator'])
            ->orderBy('operation_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterCashbox) {
            $query->where('cashbox_id', $this->filterCashbox);
        }

        return view('livewire.operations.index', [
            'operations' => $query->paginate(15),
            'cashboxes' => Cashbox::all(),
        ])->layout('layouts.app');
    }
}
