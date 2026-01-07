<?php

namespace App\Livewire\Organization;

use App\Models\Tenant;
use App\Models\User;
use Livewire\Component;

class Setup extends Component
{
    // ... (rest of the properties remain the same)
    // Steps
    public $currentStep = 1;
    public $totalSteps = 4;

    // Organization Info (renamed to Tenant in backend but keeping UI variables for now)
    public $name = '';
    public $business_type = '';
    public $business_type_other = '';
    
    // Exchange Rates
    public $usd_to_eur = 0.92;
    public $usd_to_cdf = 2850;
    public $eur_to_cdf = 3081;

    // Hidden defaults
    public $phone = null;
    public $default_currency = 'USD';

    protected function rules()
    {
        if ($this->currentStep == 1) {
            return [
                'name' => 'required|string|max:255',
            ];
        } elseif ($this->currentStep == 2) {
            return [
                'business_type' => 'required|string|max:255',
                'business_type_other' => $this->business_type === 'Autres' ? 'required|string|max:255' : 'nullable',
            ];
        } elseif ($this->currentStep == 3) {
            return [
                'usd_to_cdf' => 'required|numeric|min:0',
            ];
        }
        
        return [];
    }

    public function nextStep()
    {
        $this->validate();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function mount()
    {
        // Redirect if user already has an organization
        if (auth()->user()->tenant_id) {
            return redirect()->route('dashboard');
        }
    }

    public function submitForm()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->nextStep();
        } else {
            $this->setupOrganization();
        }
    }

    protected function getAllRules()
    {
        return [
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'business_type_other' => $this->business_type === 'Autres' ? 'required|string|max:255' : 'nullable',
            'usd_to_cdf' => 'required|numeric|min:0',
        ];
    }

    public function setupOrganization()
    {
        try {
            $this->validate($this->getAllRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->errors() as $field => $errors) {
                foreach ($errors as $error) {
                    session()->flash('error', "$field: $error");
                }
            }
            return;
        }

        try {
            \DB::beginTransaction();

            // Create tenant
            $tenant = Tenant::create([
                'name' => $this->name,
                'slug' => \Illuminate\Support\Str::slug($this->name),
                'business_type' => $this->business_type === 'Autres' ? $this->business_type_other : $this->business_type,
                'phone' => $this->phone,
                'default_currency' => $this->default_currency,
                'on_trial' => true,
                'trial_ends_at' => now()->addDays(7),
                'status' => 'TRIAL',
            ]);

            // Update user with tenant
            auth()->user()->update([
                'tenant_id' => $tenant->id,
                'role' => 'manager',
            ]);

            // Create default currencies
            $currenciesData = [
                ['code' => 'USD', 'symbol' => '$', 'is_base' => true, 'is_active' => true],
                ['code' => 'CDF', 'symbol' => 'FC', 'is_base' => false, 'is_active' => true],
                ['code' => 'EUR', 'symbol' => '€', 'is_base' => false, 'is_active' => true],
            ];

            foreach ($currenciesData as $currencyData) {
                \App\Models\Currency::create(array_merge($currencyData, [
                    'tenant_id' => $tenant->id,
                ]));
            }

            // Create initial exchange rates
            $rates = [
                ['from_currency' => 'USD', 'to_currency' => 'EUR', 'rate' => $this->usd_to_eur],
                ['from_currency' => 'USD', 'to_currency' => 'CDF', 'rate' => $this->usd_to_cdf],
                ['from_currency' => 'EUR', 'to_currency' => 'CDF', 'rate' => $this->eur_to_cdf],
                // Add inverse rates for completeness if needed, but service handles standard inverse
            ];

            foreach ($rates as $rate) {
                \App\Models\ExchangeRate::create([
                    'tenant_id' => $tenant->id,
                    'from_currency' => $rate['from_currency'],
                    'to_currency' => $rate['to_currency'],
                    'rate' => $rate['rate'],
                    'date' => now(),
                    'created_by' => auth()->id(),
                ]);
            }

            // Create default accounts for each currency
            $currencies = ['USD', 'EUR', 'CDF'];
            foreach ($currencies as $currency) {
                \App\Models\Account::create([
                    'tenant_id' => $tenant->id,
                    'label' => "Caisse Principale {$currency}",
                    'type' => 'cash',
                    'is_active' => true,
                ]);
            }

            \DB::commit();

            // Update user session
            auth()->user()->refresh();

            session()->flash('success', 'Organisation configurée avec succès !');
            
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Tenant setup failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Erreur lors de la configuration: ' . $e->getMessage());
            $this->dispatch('error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.organization.setup')->layout('layouts.setup');
    }
}
