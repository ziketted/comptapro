<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Beneficiary;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'beneficiary', 'user', 'validator'])
            ->orderBy('transaction_date', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20);
        $accounts = Account::active()->get();
        $beneficiaries = Beneficiary::active()->get();

        return view('transactions.index', compact('transactions', 'accounts', 'beneficiaries'));
    }

    public function create()
    {
        $accounts = Account::active()->get();
        $beneficiaries = Beneficiary::active()->get();

        return view('transactions.create', compact('accounts', 'beneficiaries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'type' => 'required|in:income,expense,exchange',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,card,bank,mobile',
            'transaction_date' => 'required|date',
            'attachments' => 'nullable|array',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        $organization = auth()->user()->organization;

        // Calculate exchange rate and base currency amount
        $exchangeRate = 1;
        $amountInBaseCurrency = $validated['amount'];

        if ($validated['currency'] !== $organization->default_currency) {
            $exchangeRate = ExchangeRate::getRate(
                $validated['currency'],
                $organization->default_currency,
                $validated['transaction_date']
            );
            $amountInBaseCurrency = $validated['amount'] * $exchangeRate;
        }

        $transaction = Transaction::create([
            'organization_id' => auth()->user()->organization_id,
            'user_id' => auth()->id(),
            'account_id' => $validated['account_id'],
            'beneficiary_id' => $validated['beneficiary_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'exchange_rate' => $exchangeRate,
            'amount_in_base_currency' => $amountInBaseCurrency,
            'reference' => $validated['reference'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'transaction_date' => $validated['transaction_date'],
            'attachments' => $validated['attachments'] ?? [],
            'status' => 'pending',
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['account', 'beneficiary', 'user', 'validator']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if (!$transaction->isPending()) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'Only pending transactions can be edited.');
        }

        $accounts = Account::active()->get();
        $beneficiaries = Beneficiary::active()->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'beneficiaries'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if (!$transaction->isPending()) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'Only pending transactions can be updated.');
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'type' => 'required|in:income,expense,exchange',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,card,bank,mobile',
            'transaction_date' => 'required|date',
        ]);

        $organization = auth()->user()->organization;

        // Recalculate exchange rate and base currency amount
        $exchangeRate = 1;
        $amountInBaseCurrency = $validated['amount'];

        if ($validated['currency'] !== $organization->default_currency) {
            $exchangeRate = ExchangeRate::getRate(
                $validated['currency'],
                $organization->default_currency,
                $validated['transaction_date']
            );
            $amountInBaseCurrency = $validated['amount'] * $exchangeRate;
        }

        $transaction->update([
            'account_id' => $validated['account_id'],
            'beneficiary_id' => $validated['beneficiary_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'exchange_rate' => $exchangeRate,
            'amount_in_base_currency' => $amountInBaseCurrency,
            'reference' => $validated['reference'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'transaction_date' => $validated['transaction_date'],
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        if (!$transaction->isPending()) {
            return redirect()->route('transactions.index')
                ->with('error', 'Only pending transactions can be deleted.');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function validate(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->canValidateTransactions()) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'You do not have permission to validate transactions.');
        }

        if (!$transaction->isPending()) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'Only pending transactions can be validated.');
        }

        $transaction->update([
            'status' => 'validated',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Update account balance
        $transaction->account->updateBalance();

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction validated successfully.');
    }

    public function reject(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->canValidateTransactions()) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'You do not have permission to reject transactions.');
        }

        if (!$transaction->isPending()) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'Only pending transactions can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $transaction->update([
            'status' => 'rejected',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction rejected successfully.');
    }
}
