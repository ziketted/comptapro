<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\ExchangeRate;

class Currency extends Component
{
    public $rates = [];
    public $availableCurrencies = [];
    public $baseCurrency;
    public $newRates = [];
    public $effectiveDate;

    // Editing properties
    public $showEditModal = false;
    public $editingCurrencyId;
    public $editingCode;
    public $editingSymbol;

    protected $rules = [
        'newRates.*.rate' => 'required|numeric|min:0.000001',
        'effectiveDate' => 'required|date'
    ];

    public function mount()
    {
        $tenant = auth()->user()->tenant;
        
        // Auto-seed if empty (for existing tenants before the multi-currency update)
        if ($tenant->currencies()->count() === 0) {
            $tenant->currencies()->createMany([
                ['code' => 'USD', 'symbol' => '$', 'is_active' => true, 'is_base' => true],
                ['code' => 'CDF', 'symbol' => 'FC', 'is_active' => true, 'is_base' => false],
                ['code' => 'EUR', 'symbol' => '€', 'is_active' => true, 'is_base' => false],
            ]);
        }

        $this->baseCurrency = $tenant->baseCurrency();
        $this->effectiveDate = now()->format('Y-m-d\TH:i');
        
        $this->loadCurrencies();
        $this->loadCurrentRates();
        $this->initializeNewRates();
    }

    public function loadCurrencies()
    {
        $this->availableCurrencies = auth()->user()->tenant->currencies()
            ->orderBy('code')
            ->get();
    }

    public function loadCurrentRates()
    {
        $tenant = auth()->user()->tenant;
        $activeCurrencies = $tenant->activeCurrencies();

        foreach ($activeCurrencies as $currency) {
            if ($currency->is_base) continue;

            // Direction: Base -> Secondary (1 USD = ? CDF)
            $rate = ExchangeRate::where('tenant_id', $tenant->id)
                ->where('from_currency', $this->baseCurrency->code)
                ->where('to_currency', $currency->code)
                ->orderBy('date', 'desc')
                ->first();

            $this->rates[$currency->code] = [
                'current_rate' => $rate ? $rate->rate : 1.0,
                'last_updated' => $rate ? $rate->date->format('d/m/Y') : 'Jamais',
                'updated_by' => $rate && $rate->creator ? $rate->creator->name : 'N/A'
            ];
        }
    }

    public function initializeNewRates()
    {
        $this->newRates = [];
        foreach ($this->availableCurrencies as $currency) {
            if ($currency->is_base || !$currency->is_active) continue;

            $this->newRates[$currency->code] = [
                'rate' => $this->rates[$currency->code]['current_rate'] ?? 1.0
            ];
        }
    }

    public function editCurrency($id)
    {
        $currency = \App\Models\Currency::findOrFail($id);
        $this->editingCurrencyId = $currency->id;
        $this->editingCode = $currency->code;
        $this->editingSymbol = $currency->symbol;
        $this->showEditModal = true;
    }

    public function saveCurrency()
    {
        $this->validate([
            'editingCode' => 'required|string|max:10',
            'editingSymbol' => 'required|string|max:5',
        ]);

        $currency = \App\Models\Currency::findOrFail($this->editingCurrencyId);
        $currency->update([
            'code' => strtoupper($this->editingCode),
            'symbol' => $this->editingSymbol,
        ]);

        $this->showEditModal = false;
        $this->mount(); // Refresh everything
        session()->flash('success', "Devise mise à jour.");
    }

    public function toggleCurrency($currencyId)
    {
        try {
            $currency = \App\Models\Currency::findOrFail($currencyId);
            $currency->is_active = !$currency->is_active;
            $currency->save();

            $this->loadCurrencies();
            $this->loadCurrentRates();
            $this->initializeNewRates();
            
            session()->flash('success', "Statut de la devise {$currency->code} mis à jour.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updateRates()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;
        $updatedCount = 0;

        foreach ($this->newRates as $code => $data) {
            // Direction: Base -> Secondary (1 USD = ? CDF)
            // Use updateOrCreate to avoid unique constraint violations for the same day
            ExchangeRate::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'from_currency' => $this->baseCurrency->code,
                    'to_currency' => $code,
                    'date' => \Illuminate\Support\Carbon::parse($this->effectiveDate)->toDateString(),
                ],
                [
                    'rate' => $data['rate'],
                    'created_by' => auth()->id()
                ]
            );

            $updatedCount++;
        }

        if ($updatedCount > 0) {
            $this->loadCurrentRates();
            session()->flash('success', "$updatedCount taux de change mis à jour avec succès.");
        }
    }

    public function getHistoricalRates($code)
    {
        $tenant = auth()->user()->tenant;

        // Direction: Base -> Secondary
        return ExchangeRate::where('tenant_id', $tenant->id)
            ->where('from_currency', $this->baseCurrency->code)
            ->where('to_currency', $code)
            ->with('creator')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.settings.currency')->layout('layouts.app');
    }
}
