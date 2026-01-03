<?php

namespace App\Livewire\Exchange;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\CurrencyConverter;
use Livewire\Component;

class Create extends Component
{
    public $fromCurrency = 'USD';
    public $toCurrency = 'EUR';
    public $fromAmount = 0;
    public $toAmount = 0;
    public $fromAccount = null;
    public $toAccount = null;
    public $exchangeRate = 1;
    public $description = '';

    protected $rules = [
        'fromCurrency' => 'required|string|in:USD,EUR,CDF',
        'toCurrency' => 'required|string|in:USD,EUR,CDF',
        'fromAmount' => 'required|numeric|min:0.01',
        'fromAccount' => 'required|exists:accounts,id',
        'toAccount' => 'required|exists:accounts,id',
        'description' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->updateExchangeRate();
    }

    public function updatedFromCurrency()
    {
        $this->updateExchangeRate();
        $this->calculateToAmount();
    }

    public function updatedToCurrency()
    {
        $this->updateExchangeRate();
        $this->calculateToAmount();
    }

    public function updatedFromAmount()
    {
        $this->calculateToAmount();
    }

    public function swapCurrencies()
    {
        $temp = $this->fromCurrency;
        $this->fromCurrency = $this->toCurrency;
        $this->toCurrency = $temp;

        $tempAccount = $this->fromAccount;
        $this->fromAccount = $this->toAccount;
        $this->toAccount = $tempAccount;

        $this->updateExchangeRate();
        $this->calculateToAmount();
    }

    protected function updateExchangeRate()
    {
        if ($this->fromCurrency === $this->toCurrency) {
            $this->exchangeRate = 1;
            return;
        }

        try {
            $converter = new CurrencyConverter(auth()->user()->tenant);
            $this->exchangeRate = $converter->getExchangeRate(
                $this->fromCurrency,
                $this->toCurrency
            );
        } catch (\Exception $e) {
            $this->exchangeRate = 0;
            session()->flash('error', 'Taux de change non disponible pour cette paire de devises.');
        }
    }

    protected function calculateToAmount()
    {
        $this->toAmount = round($this->fromAmount * $this->exchangeRate, 2);
    }

    public function executeExchange()
    {
        $this->validate();

        // Validate same currency check
        if ($this->fromCurrency === $this->toCurrency) {
            session()->flash('error', 'Les devises source et destination doivent être différentes.');
            return;
        }

        // Validate different accounts
        if ($this->fromAccount === $this->toAccount) {
            session()->flash('error', 'Les comptes source et destination doivent être différents.');
            return;
        }

        // Check sufficient balance
        $fromAccountModel = Account::find($this->fromAccount);
        if ($fromAccountModel->balance < $this->fromAmount) {
            session()->flash('error', 'Solde insuffisant dans le compte source.');
            return;
        }

        try {
            \DB::beginTransaction();

            // Create outgoing transaction (from source account)
            $outgoingTransaction = Transaction::create([
                'tenant_id' => auth()->user()->tenant_id,
                'account_id' => $this->fromAccount,
                'type' => 'exchange_out',
                'amount' => $this->fromAmount,
                'currency' => $this->fromCurrency,
                'exchange_rate' => $this->exchangeRate,
                'transaction_date' => now(),
                'description' => $this->description ?: "Échange {$this->fromCurrency} → {$this->toCurrency}",
                'status' => 'validated', // Exchange transactions are auto-validated
                'user_id' => auth()->id(),
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

            // Create incoming transaction (to destination account)
            $incomingTransaction = Transaction::create([
                'tenant_id' => auth()->user()->tenant_id,
                'account_id' => $this->toAccount,
                'type' => 'exchange_in',
                'amount' => $this->toAmount,
                'currency' => $this->toCurrency,
                'exchange_rate' => 1 / $this->exchangeRate,
                'transaction_date' => now(),
                'description' => $this->description ?: "Échange {$this->fromCurrency} → {$this->toCurrency}",
                'status' => 'validated',
                'user_id' => auth()->id(),
                'validated_by' => auth()->id(),
                'validated_at' => now(),
                'reference' => $outgoingTransaction->id, // Link transactions
            ]);

            // Update account balances
            $fromAccountModel->decrement('balance', $this->fromAmount);
            Account::find($this->toAccount)->increment('balance', $this->toAmount);

            \DB::commit();

            session()->flash('success', 'Échange de devises effectué avec succès.');
            
            // Reset form
            $this->reset(['fromAmount', 'toAmount', 'description']);
            $this->dispatch('exchange-completed');

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'échange: ' . $e->getMessage());
        }
    }

    public function getAccountsProperty()
    {
        return Account::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->get();
    }

    public function getFromAccountsProperty()
    {
        return $this->accounts->where('currency', $this->fromCurrency);
    }

    public function getToAccountsProperty()
    {
        return $this->accounts->where('currency', $this->toCurrency);
    }

    public function getFromBalanceProperty()
    {
        if (!$this->fromAccount) return 0;
        return Account::find($this->fromAccount)->balance ?? 0;
    }

    public function getToBalanceProperty()
    {
        if (!$this->toAccount) return 0;
        return Account::find($this->toAccount)->balance ?? 0;
    }

    public function render()
    {
        return view('livewire.exchange.create', [
            'accounts' => $this->accounts,
            'fromAccounts' => $this->fromAccounts,
            'toAccounts' => $this->toAccounts,
            'fromBalance' => $this->fromBalance,
            'toBalance' => $this->toBalance,
        ])->layout('layouts.app');
    }
}
