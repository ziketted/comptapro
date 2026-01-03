<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Validation des Opérations</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Approuvez ou rejetez les opérations en attente de validation.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 flex items-start gap-3">
            <span class="iconify text-emerald-600 dark:text-emerald-400 mt-0.5" data-icon="lucide:check-circle" style="width: 20px; height: 20px;"></span>
            <div class="flex-1 text-sm font-medium text-emerald-900 dark:text-emerald-100">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3">
            <span class="iconify text-red-600 dark:text-red-400 mt-0.5" data-icon="lucide:x-circle" style="width: 20px; height: 20px;"></span>
            <div class="flex-1 text-sm font-medium text-red-900 dark:text-red-100">{{ session('error') }}</div>
        </div>
    @endif

    @if($pendingOperations->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="iconify text-slate-300" data-icon="lucide:check-circle" style="width: 40px; height: 40px;"></span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Aucune opération en attente</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Toutes les opérations ont été validées ou il n'y a pas de nouvelles opérations à valider.</p>
        </div>
    @else
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="font-semibold dark:text-white">Opérations en Attente</h2>
                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-full">{{ $pendingOperations->count() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="text-left text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Date</th>
                            <th class="text-left text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Type</th>
                            <th class="text-left text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Compte/Caisse</th>
                            <th class="text-left text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Bénéficiaire</th>
                            <th class="text-left text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Description</th>
                            <th class="text-right text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Montant</th>
                            <th class="text-left text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Créé par</th>
                            <th class="text-center text-xs font-medium text-slate-600 dark:text-slate-400 px-4 py-3 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($pendingOperations as $op)
                            @php
                                $isIncome = $op->type === 'INCOME';
                                $isExpense = $op->type === 'EXPENSE';
                                $isExchange = $op->type === 'EXCHANGE';
                                $color = $isIncome ? 'emerald' : ($isExpense ? 'red' : 'blue');
                                $icon = $isIncome ? 'arrow-down-left' : ($isExpense ? 'arrow-up-right' : 'repeat');
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-4 py-3 text-sm dark:text-slate-300">{{ $op->operation_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 text-sm">
                                        <span class="iconify text-{{ $color }}-600 dark:text-{{ $color }}-400" data-icon="lucide:{{ $icon }}" style="width: 16px; height: 16px;"></span>
                                        <span class="dark:text-white">{{ $isIncome ? 'Recette' : ($isExpense ? 'Dépense' : 'Transfert') }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm dark:text-slate-300">
                                    @if($isExchange)
                                        {{ $op->cashbox?->name ?? 'N/A' }}
                                    @else
                                        {{ $op->account?->name ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm dark:text-slate-300">{{ $op->beneficiary?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate">{{ $op->description }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-right dark:text-white">
                                    <span class="text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                        {{ $isIncome ? '+' : ($isExpense ? '-' : '') }}{{ number_format($op->original_amount, 2) }} {{ $op->currency->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm dark:text-slate-300">{{ $op->creator?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="validateOperation({{ $op->id }})" 
                                                class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5">
                                            <span class="iconify" data-icon="lucide:check" style="width: 14px; height: 14px;"></span>
                                            Valider
                                        </button>
                                        <button wire:click="rejectOperation({{ $op->id }})" 
                                                class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-1.5">
                                            <span class="iconify" data-icon="lucide:x" style="width: 14px; height: 14px;"></span>
                                            Rejeter
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
