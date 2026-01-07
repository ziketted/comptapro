@extends('layouts.app')

@section('title', 'Journal de Caisse')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                    Rapports
                </a>
                <span class="text-slate-300">/</span>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Journal de Caisse</h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Historique des opérations validées</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.cash-journal.excel', request()->all()) }}" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-green-600" data-icon="lucide:file-spreadsheet" style="width: 18px; height: 18px;"></span>
                Excel
            </a>
            <a href="{{ route('reports.cash-journal.pdf', request()->all()) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-red-600" data-icon="lucide:file-text" style="width: 18px; height: 18px;"></span>
                PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 mb-6">
        <form action="{{ route('reports.cash-journal') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Date début</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Date fin</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Compte</label>
                <select name="account_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <option value="">Tous les comptes</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->type == 'INCOME' ? 'Rec' : 'Dép' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Devise</label>
                <select name="currency_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <option value="">Toutes les devises</option>
                    @foreach($currencies as $curr)
                        <option value="{{ $curr->id }}" {{ request('currency_id') == $curr->id ? 'selected' : '' }}>{{ $curr->code }} - {{ $curr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Grouper par</label>
                <select name="group_by" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <option value="">Aucun</option>
                    <option value="TYPE" {{ request('group_by') == 'TYPE' ? 'selected' : '' }}>Type d'opération</option>
                    <option value="ACCOUNT" {{ request('group_by') == 'ACCOUNT' ? 'selected' : '' }}>Compte</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 text-sm font-medium transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Totals Summary -->
    <div class="grid grid-cols-1 gap-4 mb-6">
        @foreach($totals as $total)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Résumé {{ $total['currency'] }}</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Income -->
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase mb-1">Recettes</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                        +{{ number_format($total['income'], 2) }} <span class="text-sm font-normal text-slate-500">{{ $total['currency'] }}</span>
                    </p>
                </div>
                <!-- Expense -->
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase mb-1">Dépenses</p>
                    <p class="text-lg font-bold text-red-600 dark:text-red-400">
                        -{{ number_format($total['expense'], 2) }} <span class="text-sm font-normal text-slate-500">{{ $total['currency'] }}</span>
                    </p>
                </div>
                <!-- Balance -->
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase mb-1">Solde Net</p>
                    <p class="text-lg font-bold {{ $total['balance'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $total['balance'] >= 0 ? '+' : '' }}{{ number_format($total['balance'], 2) }} <span class="text-sm font-normal text-slate-500">{{ $total['currency'] }}</span>
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="journalTable">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Compte</th>
                        <th class="px-6 py-3 min-w-[200px]">
                            <div class="flex flex-col gap-2">
                                <span>Bénéficiaire</span>
                                <input type="text" id="beneSearch" placeholder="Recherche rapide..." class="w-full px-2 py-1 text-xs font-normal normal-case rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right">Montant</th>
                        <th class="px-6 py-3">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @php $currentGroup = null; @endphp
                    @forelse($operations as $op)
                    
                    @if(request('group_by') == 'TYPE' && $currentGroup !== $op->type)
                        @php $currentGroup = $op->type; @endphp
                        <tr class="bg-slate-100 dark:bg-slate-900 font-bold group-header">
                            <td colspan="6" class="px-6 py-2 text-slate-700 dark:text-slate-300">
                                {{ $op->type === 'INCOME' ? 'Recettes' : ($op->type === 'EXPENSE' ? 'Dépenses' : 'Transferts') }}
                            </td>
                        </tr>
                    @elseif(request('group_by') == 'ACCOUNT' && $currentGroup !== $op->account_id)
                        @php $currentGroup = $op->account_id; @endphp
                        <tr class="bg-slate-100 dark:bg-slate-900 font-bold group-header">
                            <td colspan="6" class="px-6 py-2 text-slate-700 dark:text-slate-300">
                                {{ $op->account ? $op->account->name : 'Sans Compte' }}
                            </td>
                        </tr>
                    @endif

                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors data-row">
                        <td class="px-6 py-3 whitespace-nowrap text-slate-900 dark:text-white">
                            {{ $op->operation_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            @if($op->type === 'INCOME')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Recette
                                </span>
                            @elseif($op->type === 'EXPENSE')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Dépense
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Transfert
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $op->account ? $op->account->name : '-' }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-slate-600 dark:text-slate-400 text-beneficiary">
                            {{ $op->beneficiary ? $op->beneficiary->name : '-' }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right font-medium text-slate-900 dark:text-white">
                            {{ number_format($op->original_amount, 2) }} {{ $op->currency->code }}
                        </td>
                        <td class="px-6 py-3 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                            {{ $op->description }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            Aucune opération trouvée pour ces critères.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($operations->hasPages())
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 mt-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-slate-600 dark:text-slate-400">
                Affichage de <span class="font-semibold text-slate-900 dark:text-white">{{ $operations->firstItem() }}</span> 
                à <span class="font-semibold text-slate-900 dark:text-white">{{ $operations->lastItem() }}</span> 
                sur <span class="font-semibold text-slate-900 dark:text-white">{{ $operations->total() }}</span> opérations
            </div>
            <div>
                {{ $operations->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('beneSearch');
        
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('tr.data-row');
            
            rows.forEach(row => {
                const beneficiaryCell = row.querySelector('.text-beneficiary');
                if (beneficiaryCell) {
                    const text = beneficiaryCell.textContent || beneficiaryCell.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });
        });
    });
</script>
@endsection
