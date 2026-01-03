<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TenantFeatures extends Component
{
    public $enable_cash_management;
    public $enable_beneficiaries;
    public $enable_reference;
    public $enable_attachment;

    public function mount()
    {
        $tenant = Auth::user()->tenant;
        $this->enable_cash_management = $tenant->enable_cash_management;
        $this->enable_beneficiaries = $tenant->enable_beneficiaries;
        $this->enable_reference = $tenant->enable_reference;
        $this->enable_attachment = $tenant->enable_attachment;
    }

    public function updated($propertyName)
    {
        $tenant = Auth::user()->tenant;
        
        $fields = [
            'enable_cash_management', 
            'enable_beneficiaries',
            'enable_reference',
            'enable_attachment'
        ];

        if (in_array($propertyName, $fields)) {
            $tenant->update([$propertyName => $this->$propertyName]);
        }
        
        session()->flash('success', 'Paramètres mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.settings.tenant-features')->layout('layouts.app');
    }
}
