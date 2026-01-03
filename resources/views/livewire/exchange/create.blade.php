<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight mb-1 dark:text-white">Bureau de Change</h1>
        <p class="text-slate-600 dark:text-slate-400">Convertir entre les devises de vos caisses</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Exchange Calculator -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8">
                <h3 class="font-semibold mb-6 dark:text-white">Nouvel Échange</h3>

                <!-- From Currency -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">De</label>
                    <div class="p-6 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center gap-4 mb-4">
                            <select wire:model.live="fromCurrency" class="flex-1 px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white dark:bg-slate-700 font-medium">
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="CDF">CDF - Franc Congolais</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <select wire:model.live="fromAccount" class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white dark:bg-slate-700">
                                <option value="">Sélectionner un compte...</option>
                                @foreach($fromAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input wire:model.live.debounce.500ms="fromAmount" type="number" step="0.01" placeholder="0.00" class="w-full px-4 py-4 border border-slate-300 dark:border-slate-600 rounded-lg text-2xl font-semibold focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white dark:bg-slate-700">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-2">Disponible: {{ $fromCurrency }} {{ number_format($fromBalance, 2) }}</div>
                    </div>
                </div>

                <!-- Exchange Icon -->
                <div class="flex justify-center -my-3 relative z-10">
                    <button wire:click="swapCurrencies" type="button" class="p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors">
                        <span class="iconify" data-icon="lucide:arrow-down-up" style="width: 24px; height: 24px;"></span>
                    </button>
                </div>

                <!-- To Currency -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Vers</label>
                    <div class="p-6 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center gap-4 mb-4">
                            <select wire:model.live="toCurrency" class="flex-1 px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white dark:bg-slate-700 font-medium">
                                <option value="EUR">EUR - Euro</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="CDF">CDF - Franc Congolais</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <select wire:model.live="toAccount" class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white dark:bg-slate-700">
                                <option value="">Sélectionner un compte...</option>
                                @foreach($toAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full px-4 py-4 border border-slate-300 dark:border-slate-600 rounded-lg text-2xl font-semibold bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400">
                            {{ number_format($toAmount, 2) }}
                        </div>
                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-2">Solde après: {{ $toCurrency }} {{ number_format($toBalance + $toAmount, 2) }}</div>
                    </div>
                </div>

                <!-- Exchange Rate Info -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Taux de Change</span>
                        <span class="text-sm font-semibold dark:text-white">1 {{ $fromCurrency }} = {{ number_format($exchangeRate, 4) }} {{ $toCurrency }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Mis à jour aujourd'hui</span>
                        <a href="{{ route('settings.currency') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Gérer les taux</a>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description (optionnel)</label>
                    <textarea wire:model="description" rows="2" placeholder="Notes sur cet échange..." class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent resize-none bg-white dark:bg-slate-700"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button wire:click="$refresh" type="button" class="flex-1 px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Annuler
                    </button>
                    <button wire:click="executeExchange" type="button" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="lucide:repeat" style="width: 18px; height: 18px;"></span>
                        Exécuter l'Échange
                    </button>
                </div>
            </div>
        </div>

        <!-- Exchange Rates Panel -->
        <div class="space-y-6">
            <!-- Current Rates -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="font-semibold mb-4 dark:text-white">Taux Actuels</h3>
                <div class="space-y-4">
                    @php
                        $converter = new \App\Services\CurrencyConverter(auth()->user()->organization);
                        $pairs = [
                            ['USD', 'EUR'],
                            ['USD', 'CDF'],
                            ['EUR', 'CDF'],
                        ];
                    @endphp
                    @foreach($pairs as $pair)
                        @php
                            try {
                                $rate = $converter->getExchangeRate($pair[0], $pair[1]);
                            } catch (\Exception $e) {
                                $rate = 0;
                            }
                        @endphp
                        <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium dark:text-white">{{ $pair[0] }} → {{ $pair[1] }}</span>
                                <span class="text-sm font-semibold dark:text-white">{{ $rate > 0 ? number_format($rate, 4) : 'N/A' }}</span>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Mis à jour aujourd'hui</div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('settings.currency') }}" class="w-full mt-4 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors block text-center">
                    Gérer les Taux
                </a>
            </div>

            <!-- Account Balances -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="font-semibold mb-4 dark:text-white">Soldes des Comptes</h3>
                <div class="space-y-3">
                    @foreach($accounts as $account)
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium dark:text-white">{{ $account->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $account->currency }}</div>
                            </div>
                            <div class="text-sm font-semibold dark:text-white">{{ number_format($account->balance, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
