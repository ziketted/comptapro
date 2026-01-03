<?php

namespace App\Livewire\Beneficiaries;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Beneficiary;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingBeneficiaryId = null;

    // Form fields
    public $name;
    public $phone;
    public $description;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(Beneficiary $beneficiary)
    {
        $this->editingBeneficiaryId = $beneficiary->id;
        $this->name = $beneficiary->name;
        $this->phone = $beneficiary->phone;
        $this->description = $beneficiary->description;
        $this->is_active = $beneficiary->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;

        Beneficiary::updateOrCreate(
            ['id' => $this->editingBeneficiaryId, 'tenant_id' => $tenant->id],
            [
                'name' => $this->name,
                'phone' => $this->phone,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'type' => 'individual', // Default
            ]
        );

        session()->flash('success', $this->editingBeneficiaryId ? 'Bénéficiaire mis à jour.' : 'Bénéficiaire créé avec succès.');
        $this->closeModal();
    }

    public function toggleStatus(Beneficiary $beneficiary)
    {
        $beneficiary->update(['is_active' => !$beneficiary->is_active]);
        session()->flash('success', 'Statut mis à jour.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingBeneficiaryId = null;
        $this->name = '';
        $this->phone = '';
        $this->description = '';
        $this->is_active = true;
    }

    public function render()
    {
        $tenant = auth()->user()->tenant;
        $beneficiaries = Beneficiary::where('tenant_id', $tenant->id)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.beneficiaries.index', [
            'beneficiaries' => $beneficiaries
        ])->layout('layouts.app');
    }
}
