<div class="space-y-6">
    <!-- Currency Activation Management -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold dark:text-white">Gestion des Devises</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Min 2, Max 3 devises actives. Une devise de base obligatoire.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Symbole</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-center">Statut</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($availableCurrencies as $currency)
                    <tr wire:key="currency-{{ $currency->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $currency->code }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono">{{ $currency->symbol }}</td>
                        <td class="px-6 py-4">
                            @if($currency->is_base)
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold rounded">BASE</span>
                            @else
                            <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-xs font-medium rounded">SECONDAIRE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($currency->is_active)
                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-sm font-medium">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Actif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 text-sm font-medium">
                                <span class="w-2 h-2 bg-slate-400 rounded-full"></span> Inactif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="editCurrency({{ $currency->id }})" 
                                        wire:loading.attr="disabled"
                                        class="p-2 text-slate-400 hover:text-blue-600 transition-colors disabled:opacity-50">
                                    <span class="iconify" data-icon="lucide:edit-3" style="width: 18px;"></span>
                                </button>

                                @if(!$currency->is_base)
                                <button wire:click="toggleCurrency({{ $currency->id }})" 
                                        wire:loading.attr="disabled"
                                        class="text-sm font-semibold {{ $currency->is_active ? 'text-red-400 hover:text-red-500' : 'text-blue-400 hover:text-blue-500' }} transition-colors disabled:opacity-50">
                                    {{ $currency->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($baseCurrency)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Update Rates Form -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 h-fit">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold dark:text-white">Fixation des Taux</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Saisir la valeur de 1 {{ $baseCurrency->code }}</p>
            </div>

            <form wire:submit.prevent="updateRates" class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date d'effet</label>
                    <input type="datetime-local" wire:model="effectiveDate" 
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm">
                </div>

                @foreach($newRates as $code => $data)
                <div wire:key="rate-input-{{ $code }}" class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">1 {{ $baseCurrency->code }} = ? {{ $code }}</label>
                    <div class="relative">
                        <input type="number" step="0.000001" wire:model="newRates.{{ $code }}.rate" 
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">{{ $code }}</div>
                    </div>
                    @error("newRates.{$code}.rate") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endforeach

                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-500/20 disabled:bg-blue-400">
                    <span wire:loading.remove wire:target="updateRates">Mettre à jour le marché</span>
                    <span wire:loading wire:target="updateRates">Mise à jour...</span>
                </button>
            </form>
        </div>

        <!-- Current Rates Overview -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($rates as $code => $data)
                <div wire:key="rate-card-{{ $code }}" class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform"></div>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="px-2 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold rounded">
                            1 {{ $baseCurrency->code }} = {{ $code }}
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium">{{ $data['last_updated'] }}</span>
                    </div>
                    
                    <div class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight mb-2">
                        {{ number_format($data['current_rate'], 4) }}
                    </div>
                    
                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-2">
                        <span class="iconify" data-icon="lucide:user" style="width: 12px;"></span>
                        <span>Confirmé par {{ $data['updated_by'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Simple History (Minimalist) -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Évolution du Marché</h4>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($rates as $code => $data)
                        @php $history = $this->getHistoricalRates($code); @endphp
                        <div wire:key="rate-history-{{ $code }}" class="p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $baseCurrency->code }} → {{ $code }}</span>
                            </div>
                            <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                                @forelse($history as $h)
                                <div wire:key="history-entry-{{ $h->id }}" class="flex-shrink-0 bg-slate-50 dark:bg-slate-900 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
                                    <div class="text-xs font-bold dark:text-white font-mono">{{ number_format($h->rate, 2) }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">{{ $h->date->format('d/m') }}</div>
                                </div>
                                @empty
                                <span class="text-xs text-slate-400 italic font-normal">Aucun historique</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Currency Modal (Livewire Controlled) -->
    @if($showEditModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showEditModal', false)"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                <h3 class="text-lg font-bold dark:text-white">Modifier la Devise</h3>
                <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <span class="iconify" data-icon="lucide:x" style="width: 24px;"></span>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Code (ex: USD)</label>
                    <input type="text" wire:model="editingCode" class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-bold uppercase">
                    @error('editingCode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Symbole (ex: $)</label>
                    <input type="text" wire:model="editingSymbol" class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white">
                    @error('editingSymbol') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex gap-3">
                <button wire:click="$set('showEditModal', false)" class="flex-1 py-2 text-slate-600 dark:text-slate-400 font-bold hover:text-slate-900 transition-colors">Annuler</button>
                <button wire:click="saveCurrency" 
                        wire:loading.attr="disabled"
                        class="flex-1 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-colors disabled:bg-blue-400">
                    <span wire:loading.remove wire:target="saveCurrency">Enregistrer</span>
                    <span wire:loading wire:target="saveCurrency">Enregistrement...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
