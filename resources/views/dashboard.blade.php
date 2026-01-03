@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight mb-1 dark:text-white">Dashboard</h1>
        <p class="text-slate-600 dark:text-slate-400">Bienvenue, {{ auth()->user()->name }}. Voici votre aperçu financier.</p>
    </div>

    <!-- Currency Balance Cards -->
    <div class="mb-6">
        <livewire:dashboard.currency-cards />
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs text-slate-600 dark:text-slate-400 uppercase tracking-wide">Comptes Actifs</div>
                <span class="iconify text-blue-600 dark:text-blue-400" data-icon="lucide:wallet" style="width: 16px; height: 16px;"></span>
            </div>
            <div class="text-2xl font-semibold tracking-tight mb-1 dark:text-white">{{ $totalAccounts }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Comptes</div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs text-slate-600 dark:text-slate-400 uppercase tracking-wide">Bénéficiaires</div>
                <span class="iconify text-purple-600 dark:text-purple-400" data-icon="lucide:users" style="width: 16px; height: 16px;"></span>
            </div>
            <div class="text-2xl font-semibold tracking-tight mb-1 dark:text-white">{{ $totalBeneficiaries }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Actifs</div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs text-slate-600 dark:text-slate-400 uppercase tracking-wide">Solde Total</div>
                <span class="iconify text-emerald-600 dark:text-emerald-400" data-icon="lucide:dollar-sign" style="width: 16px; height: 16px;"></span>
            </div>
            <div class="text-2xl font-semibold tracking-tight mb-1 dark:text-white">{{ number_format($totalBalance, 2) }} {{ auth()->user()->tenant->default_currency }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Total (Valeur estimée)</div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs text-slate-600 dark:text-slate-400 uppercase tracking-wide">En Attente</div>
                <span class="iconify text-amber-600 dark:text-amber-400" data-icon="lucide:clock" style="width: 16px; height: 16px;"></span>
            </div>
            <div class="text-2xl font-semibold tracking-tight mb-1 dark:text-white">{{ $pendingTransactions }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">À valider</div>
        </div>
    </div>
    
    {{-- Financial Summary (NEW - Phase 5) - COMMENTED FOR FUTURE USE --}}
    {{-- 
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-slate-600 dark:text-slate-400">Total Recettes</div>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                    <span class="iconify text-emerald-600 dark:text-emerald-400" data-icon="lucide:arrow-down-left" style="width: 20px; height: 20px;"></span>
                </div>
            </div>
            <div class="text-3xl font-semibold tracking-tight mb-1 dark:text-white">{{ number_format($income ?? 0, 2) }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Opérations validées</div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-slate-600 dark:text-slate-400">Total Dépenses</div>
                <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <span class="iconify text-red-600 dark:text-red-400" data-icon="lucide:arrow-up-right" style="width: 20px; height: 20px;"></span>
                </div>
            </div>
            <div class="text-3xl font-semibold tracking-tight mb-1 dark:text-white">{{ number_format($expense ?? 0, 2) }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Opérations validées</div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-slate-600 dark:text-slate-400">Résultat Net</div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <span class="iconify text-blue-600 dark:text-blue-400" data-icon="lucide:trending-up" style="width: 20px; height: 20px;"></span>
                </div>
            </div>
            <div class="text-3xl font-semibold tracking-tight mb-1 {{ ($income - $expense) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                {{ ($income - $expense) >= 0 ? '+' : '' }}{{ number_format($income - $expense, 2) }}
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Recettes - Dépenses</div>
        </div>
    </div>
    --}}

    <!-- Dynamic Income vs Expense Chart (Full width) -->
    <div class="mb-6">
        <livewire:dashboard.income-expense-chart />
    </div>

    <!-- Bottom Section: Monthly Operations + Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Monthly Operations Summary -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold dark:text-white">Opérations mensuelles</h3>
                <p class="text-sm text-blue-600 dark:text-blue-400">Résumé détaillé sur les opérations mensuelles :</p>
            </div>

            <div class="space-y-6 max-h-96 overflow-y-auto pr-2">
                @foreach($monthlyOperations as $monthData)
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-4 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">{{ $monthData['month'] }}</h4>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-lg font-bold {{ $monthData['total'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($monthData['total'], 2) }} {{ auth()->user()->tenant->default_currency }}
                                    </span>
                                    @if($monthData['total'] >= 0)
                                        <span class="iconify text-emerald-600 dark:text-emerald-400" data-icon="lucide:trending-up" style="width: 16px; height: 16px;"></span>
                                    @else
                                        <span class="iconify text-red-600 dark:text-red-400" data-icon="lucide:trending-down" style="width: 16px; height: 16px;"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right space-y-1">
                                <div class="flex items-center gap-2 justify-end">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Produit :</span>
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($monthData['income'], 2) }} {{ auth()->user()->tenant->default_currency }}
                                    </span>
                                    @if($monthData['income'] > 0)
                                        <span class="iconify text-blue-600 dark:text-blue-400" data-icon="lucide:arrow-up" style="width: 14px; height: 14px;"></span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 justify-end">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Charge :</span>
                                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                        {{ number_format($monthData['expense'], 2) }} {{ auth()->user()->tenant->default_currency }}
                                    </span>
                                    @if($monthData['expense'] > 0)
                                        <span class="iconify text-red-600 dark:text-red-400" data-icon="lucide:arrow-down" style="width: 14px; height: 14px;"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions (Moved here) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-semibold mb-4 dark:text-white">Actions Rapides</h3>
            <div class="space-y-4">
                <a href="{{ route('operations.create') }}" class="flex items-center gap-4 px-5 py-4 border border-slate-200 dark:border-slate-600 rounded-xl hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-left group">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl group-hover:scale-110 transition-transform">
                        <span class="iconify text-blue-600 dark:text-blue-400" data-icon="lucide:plus" style="width: 24px; height: 24px;"></span>
                    </div>
                    <div>
                        <div class="text-base font-semibold dark:text-white">Nouvelle Opération</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">Enregistrer une recette ou une dépense</div>
                    </div>
                </a>

                @if($pendingTransactions > 0 && auth()->user()->canValidateTransactions())
                <a href="{{ route('operations.validate') }}" class="flex items-center gap-4 px-5 py-4 border border-amber-200 dark:border-amber-800 rounded-xl hover:border-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all text-left group">
                    <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl group-hover:scale-110 transition-transform">
                        <span class="iconify text-amber-600 dark:text-amber-400" data-icon="lucide:check-square" style="width: 24px; height: 24px;"></span>
                    </div>
                    <div class="flex-1">
                        <div class="text-base font-semibold dark:text-white">Valider les opérations</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">{{ $pendingTransactions }} opérations en attente</div>
                    </div>
                    <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-sm font-bold rounded-full border border-amber-200 dark:border-amber-800">{{ $pendingTransactions }}</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
