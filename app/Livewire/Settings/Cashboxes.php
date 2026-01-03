<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Cashbox;
use Illuminate\Validation\Rule;

class Cashboxes extends Component
{
    public $name;
    public $description;
    public $is_active = true;
    public $editingCashboxId = null;
    public $showModal = false;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cashboxes', 'name')
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->ignore($this->editingCashboxId),
            ],
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->reset(['name', 'description', 'is_active', 'editingCashboxId']);
        $this->showModal = true;
    }

    public function edit(Cashbox $cashbox)
    {
        $this->editingCashboxId = $cashbox->id;
        $this->name = $cashbox->name;
        $this->description = $cashbox->description;
        $this->is_active = $cashbox->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'tenant_id' => auth()->user()->tenant_id,
        ];

        if ($this->editingCashboxId) {
            Cashbox::find($this->editingCashboxId)->update($data);
            session()->flash('success', 'Caisse mise à jour avec succès.');
        } else {
            Cashbox::create($data);
            session()->flash('success', 'Caisse créée avec succès.');
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'is_active', 'editingCashboxId']);
    }

    public function toggleStatus(Cashbox $cashbox)
    {
        $cashbox->update(['is_active' => !$cashbox->is_active]);
        $status = $cashbox->is_active ? 'activée' : 'désactivée';
        session()->flash('success', "La caisse a été $status.");
    }

    public function render()
    {
        return view('livewire.settings.cashboxes', [
            'cashboxes' => Cashbox::where('tenant_id', auth()->user()->tenant_id)->get()
        ])->layout('layouts.app');
    }
}
