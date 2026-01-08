<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Livre de Caisse</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Consultez les soldes réels et l'historique des opérations validées.</p>
        </div>

        <div class="w-full md:w-72">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Choisir une caisse</label>
            <select wire:model.live="selectedCashboxId" class="w-full bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-4 py-3 font-bold dark:text-white shadow-sm focus:ring-2 focus:ring-blue-600 transition-all">
                @foreach($cashboxes as $cb)
                    <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($balances as $item)
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/50 dark:shadow-none relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black shadow-lg shadow-blue-500/30">
                    {{ $item['symbol'] }}
                </div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Solde Actuel</div>
            </div>

            <div class="text-4xl font-black text-slate-900 dark:text-white font-mono tracking-tighter">
                {{ number_format($item['balance'], 2) }}
            </div>
            <div class="text-sm font-bold text-slate-400 mt-1">{{ $item['currency'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Validated History -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mt-12">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Brouillard de Caisse (Validé)</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <th class="px-8 py-4">Validé le</th>
                        <th class="px-8 py-4">Description</th>
                        <th class="px-8 py-4">Flux</th>
                        <th class="px-8 py-4 text-right">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($operations as $op)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $op->validated_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-slate-400">{{ $op->validated_at->format('H:i') }}</div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $op->description }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Réf: {{ $op->reference ?? 'N/A' }}</div>
                        </td>
                        <td class="px-8 py-5">
                            @if($op->type === 'INCOME')
                                <span class="inline-flex items-center gap-1 text-emerald-600 font-bold text-[10px] uppercase">
                                    <span class="iconify" data-icon="lucide:arrow-down-left"></span> Entrée
                                </span>
                            @elseif($op->type === 'EXPENSE')
                                <span class="inline-flex items-center gap-1 text-red-600 font-bold text-[10px] uppercase">
                                    <span class="iconify" data-icon="lucide:arrow-up-right"></span> Sortie
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-blue-600 font-bold text-[10px] uppercase">
                                    <span class="iconify" data-icon="lucide:repeat"></span> Transfert
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right font-mono">
                            @php 
                                $isOut = ($op->type === 'EXPENSE' || ($op->type === 'EXCHANGE' && $op->cashbox_id == $selectedCashboxId));
                            @endphp
                            <span class="text-sm font-black {{ $isOut ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $isOut ? '-' : '+' }} {{ number_format($op->original_amount, 2) }} {{ $op->currency->code }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-slate-400 italic">Aucune opération validée pour cette caisse.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
            {{ $operations->links() }}
        </div>
    </div>
</div>
