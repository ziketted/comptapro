<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('operations.validate') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-blue-600 transition-colors mb-4">
            <span class="iconify" data-icon="lucide:arrow-left" style="width: 16px;"></span>
            Retour aux validations
        </a>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Modifier l'Opération</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Modifiez les détails de l'opération en attente.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        
        @if(session('success'))
            <div class="m-8 mb-0 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 flex items-start gap-3">
                <span class="iconify text-emerald-600 dark:text-emerald-400 mt-0.5" data-icon="lucide:check-circle" style="width: 20px; height: 20px;"></span>
                <div class="flex-1 text-sm font-medium text-emerald-900 dark:text-emerald-100">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="m-8 mb-0 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3">
                <span class="iconify text-red-600 dark:text-red-400 mt-0.5" data-icon="lucide:x-circle" style="width: 20px; height: 20px;"></span>
                <div class="flex-1 text-sm font-medium text-red-900 dark:text-red-100">{{ session('error') }}</div>
            </div>
        @endif

        <form wire:submit.prevent="save" class="divide-y divide-slate-100 dark:divide-slate-700">
            <!-- Transaction Type Selection -->
            <div class="p-8">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Type de mouvement</label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" wire:model.live="type" value="INCOME" class="sr-only peer">
                        <div class="p-4 rounded-xl border-2 border-slate-100 dark:border-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all text-center">
                            <span class="iconify mx-auto mb-2 {{ $type === 'INCOME' ? 'text-emerald-600' : 'text-slate-400' }}" data-icon="lucide:trending-up" style="width: 24px;"></span>
                            <div class="text-sm font-bold dark:text-white">Recette</div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer group">
                        <input type="radio" wire:model.live="type" value="EXPENSE" class="sr-only peer">
                        <div class="p-4 rounded-xl border-2 border-slate-100 dark:border-slate-700 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 transition-all text-center">
                            <span class="iconify mx-auto mb-2 {{ $type === 'EXPENSE' ? 'text-red-600' : 'text-slate-400' }}" data-icon="lucide:trending-down" style="width: 24px;"></span>
                            <div class="text-sm font-bold dark:text-white">Dépense</div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer group">
                        <input type="radio" wire:model.live="type" value="EXCHANGE" class="sr-only peer">
                        <div class="p-4 rounded-xl border-2 border-slate-100 dark:border-slate-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                            <span class="iconify mx-auto mb-2 {{ $type === 'EXCHANGE' ? 'text-blue-600' : 'text-slate-400' }}" data-icon="lucide:repeat" style="width: 24px;"></span>
                            <div class="text-sm font-bold dark:text-white">Transfert</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Date Section -->
            <div class="p-8 pb-4 bg-slate-50/30 dark:bg-slate-900/10">
                <div class="max-w-xs">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="iconify text-slate-400" data-icon="lucide:calendar" style="width: 14px;"></span>
                        Date de l'opération
                    </label>
                    <input type="date" wire:model="operation_date" 
                           class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-bold">
                    @error('operation_date') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Financial Information (Amount & Currency) -->
            <div class="p-8 pt-4 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="lucide:banknote" style="width: 14px;"></span>
                            Montant de la transaction
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="original_amount" 
                                   placeholder="0.00"
                                   class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-50 dark:border-slate-800 rounded-2xl focus:ring-2 focus:ring-blue-600 dark:text-white font-black text-2xl font-mono transition-all">
                        </div>
                        @error('original_amount') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="lucide:coins" style="width: 14px;"></span>
                            Devise de règlement
                        </label>
                        <select wire:model.live="currency_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-50 dark:border-slate-800 rounded-2xl focus:ring-2 focus:ring-blue-600 dark:text-white font-bold text-lg transition-all">
                            @foreach($currencies as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->symbol }}</option>
                            @endforeach
                        </select>
                        @error('currency_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Source and/or Target Cashbox & Account -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($type === 'EXCHANGE')
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Caisse Source</label>
                        <select wire:model="cashbox_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                            @endforeach
                        </select>
                        @error('cashbox_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Caisse Cible</label>
                        <select wire:model="target_cashbox_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                            <option value="">Choisir la destination...</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                            @endforeach
                        </select>
                        @error('target_cashbox_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                    </div>
                    @else
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Compte Comptable</label>
                        <select wire:model="account_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                            <option value="">Sélectionner un compte...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->account_number }} - {{ $acc->label }}</option>
                            @endforeach
                        </select>
                        @error('account_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Caisse</label>
                        <select wire:model="cashbox_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                            @endforeach
                        </select>
                        @error('cashbox_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>
            </div>

            <!-- Beneficiary and Details -->
            <div class="p-8 space-y-6">
                @if($type !== 'EXCHANGE')
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Bénéficiaire / Tiers (Optionnel)</label>
                    <select wire:model="beneficiary_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                        <option value="">Sélectionner un bénéficiaire...</option>
                        @foreach($beneficiaries as $ben)
                            <option value="{{ $ben->id }}">{{ $ben->name }}</option>
                        @endforeach
                    </select>
                    @error('beneficiary_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Description / Motif</label>
                    <textarea wire:model="description" rows="3" placeholder="Ex: Paiement facture fournisseur X, Achat fournitures..."
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white text-sm"></textarea>
                    @error('description') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Référence (Optionnel)</label>
                    <input type="text" wire:model="reference" placeholder="N° Facture, Reçu..."
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white text-sm">
                    @error('reference') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="p-8 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between">
                <a href="{{ route('operations.validate') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Annuler</a>
                <button type="submit" wire:loading.attr="disabled" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black shadow-2xl shadow-blue-500/40 transition-all transform hover:-translate-y-1">
                    <span wire:loading.remove>Enregistrer les modifications</span>
                    <span wire:loading>Traitement en cours...</span>
                </button>
            </div>
        </form>
    </div>
</div>
