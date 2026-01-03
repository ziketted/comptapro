<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black dark:text-white">
                Gestion des Comptes 
                @if($typeFilter === 'INCOME') (Recettes) @elseif($typeFilter === 'EXPENSE') (Dépenses) @endif
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Configurez vos comptes comptables pour catégoriser vos flux financiers.</p>
        </div>
        
        <button wire:click="createAccount" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-500/20">
            <span class="iconify" data-icon="lucide:plus" style="width: 20px;"></span>
            Nouveau Compte
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[300px] relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <span class="iconify" data-icon="lucide:search"></span>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par libellé ou numéro..." class="w-full pl-10 bg-slate-50 dark:bg-slate-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
        </div>

        <div class="w-48">
            <select wire:model.live="typeFilter" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
                <option value="">Tous les types</option>
                <option value="INCOME">Comptes de Recettes</option>
                <option value="EXPENSE">Comptes de Dépenses</option>
            </select>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase">N° Compte</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase">Libellé</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase text-center">Type</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase text-center">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($accounts as $account)
                <tr wire:key="acc-{{ $account->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $account->account_number }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $account->label }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($account->type === 'INCOME')
                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded">RECETTE</span>
                        @else
                        <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-bold rounded">DÉPENSE</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button wire:click="toggleStatus({{ $account->id }})" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none {{ $account->is_active ? 'bg-blue-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform duration-200 {{ $account->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="editAccount({{ $account->id }})" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                            <span class="iconify" data-icon="lucide:edit-3"></span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Aucun compte trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $accounts->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-black dark:text-white">{{ $editingAccount ? 'Modifier le Compte' : 'Nouveau Compte' }}</h3>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Type de compte</label>
                    <select wire:model="type" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 font-bold dark:text-white">
                        <option value="INCOME">Compte de Recette</option>
                        <option value="EXPENSE">Compte de Dépense</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Libellé</label>
                    <input type="text" wire:model="label" placeholder="Ex: Vente de marchandises" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 font-bold dark:text-white">
                    @error('label') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Numéro de compte</label>
                    <input type="text" wire:model="account_number" placeholder="Ex: 701" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 font-bold dark:text-white">
                    @error('account_number') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" wire:model="is_active" id="is_active_check" class="rounded text-blue-600 focus:ring-blue-600">
                    <label for="is_active_check" class="text-sm font-bold text-slate-600 dark:text-slate-400">Compte actif</label>
                </div>

                <div class="flex gap-3 pt-6">
                    <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold">Annuler</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/20">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
