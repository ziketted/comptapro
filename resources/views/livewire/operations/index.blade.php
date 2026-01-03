<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">Opérations</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Gérez vos recettes, dépenses et transferts en attente de validation.</p>
        </div>
        
        <a href="{{ route('operations.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-500/20">
            <span class="iconify" data-icon="lucide:plus" style="width: 20px;"></span>
            Nouvelle Opération
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Caisse</label>
            <select wire:model.live="filterCashbox" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
                <option value="">Toutes les caisses</option>
                @foreach($cashboxes as $cb)
                    <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-48">
            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Type</label>
            <select wire:model.live="filterType" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
                <option value="">Tous les types</option>
                <option value="INCOME">Recette</option>
                <option value="EXPENSE">Dépense</option>
                <option value="EXCHANGE">Transfert</option>
            </select>
        </div>

        <div class="w-48">
            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Statut</label>
            <select wire:model.live="filterStatus" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
                <option value="">Tous les statuts</option>
                <option value="PENDING">En attente</option>
                <option value="VALIDATED">Validée</option>
                <option value="REJECTED">Rejetée</option>
            </select>
        </div>
    </div>

    <!-- Operations Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Caisse</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Montant</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Statut</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Auteur</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($operations as $op)
                    <tr wire:key="op-{{ $op->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $op->operation_date->format('d/m/Y') }}</div>
                                @if($op->attachment_path)
                                    <span class="iconify text-slate-400" data-icon="lucide:paperclip" style="width: 14px;"></span>
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $op->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium dark:text-slate-300">{{ $op->cashbox->name }}</div>
                            @if($op->beneficiary)
                            <div class="text-[10px] text-slate-500 font-medium mt-1 inline-flex items-center gap-1">
                                <span class="iconify" data-icon="lucide:user" style="width: 10px;"></span>
                                {{ $op->beneficiary->name }}
                            </div>
                            @endif
                            @if($op->type === 'EXCHANGE' && $op->targetCashbox)
                            <div class="text-[10px] text-blue-500 font-bold mt-1 inline-flex items-center gap-1">
                                <span class="iconify" data-icon="lucide:arrow-right" style="width: 10px;"></span>
                                {{ $op->targetCashbox->name }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($op->type === 'INCOME')
                            <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded uppercase">RECETTE</span>
                            @elseif($op->type === 'EXPENSE')
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-bold rounded uppercase">DÉPENSE</span>
                            @else
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded uppercase">TRANSFERT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-black dark:text-white font-mono">
                                {{ number_format($op->original_amount, 2) }} {{ $op->currency->code }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                ≈ {{ number_format($op->converted_amount, 2) }} {{ auth()->user()->tenant->default_currency }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($op->status === 'VALIDATED')
                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Validé
                            </span>
                            @elseif($op->status === 'REJECTED')
                            <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 text-xs font-bold">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Rejeté
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 text-xs font-bold">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> En attente
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[11px] font-medium dark:text-slate-400">{{ $op->creator->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($op->status === 'PENDING' && auth()->user()->isManager())
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="rejectOperation({{ $op->id }})" 
                                        wire:confirm="Êtes-vous sûr de vouloir rejeter cette opération ?"
                                        class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors">
                                    Rejeter
                                </button>
                                <button wire:click="validateOperation({{ $op->id }})" 
                                        class="px-3 py-1 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 shadow-lg shadow-emerald-500/20 transition-all">
                                    Valider
                                </button>
                            </div>
                            @elseif($op->status === 'VALIDATED')
                            <div class="text-[10px] text-slate-400 italic">Validé par {{ $op->validator->name ?? 'Système' }}</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                            Aucune opération trouvée avec ces filtres.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/30 border-t border-slate-200 dark:border-slate-700">
            {{ $operations->links() }}
        </div>
    </div>
</div>
