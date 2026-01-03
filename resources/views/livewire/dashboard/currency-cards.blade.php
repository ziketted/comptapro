<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($currenciesData as $currency)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-{{ $currency['color'] }}-50 dark:bg-{{ $currency['color'] }}-900/20 rounded-lg">
                        <span class="iconify text-{{ $currency['color'] }}-600 dark:text-{{ $currency['color'] }}-400" data-icon="{{ $currency['icon'] }}" style="width: 24px; height: 24px;"></span>
                    </div>
                    <div>
                        <div class="font-semibold text-lg dark:text-white">{{ $currency['code'] }}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">Total en {{ $currency['code'] }}</div>
                    </div>
                </div>

                <div class="text-right">
                    <div class="flex items-center gap-1 text-sm">
                        <span class="iconify text-{{ $currency['trend']['color'] }}-500"
                              data-icon="lucide:trending-{{ $currency['trend']['direction'] }}"
                              style="width: 16px; height: 16px;"></span>
                        <span class="text-{{ $currency['trend']['color'] }}-600 dark:text-{{ $currency['trend']['color'] }}-400 font-medium">
                            {{ $currency['trend']['percentage'] }}%
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">30 derniers jours</div>
                </div>
            </div>

            <div class="mb-4">
                <div class="text-3xl font-bold tracking-tight mb-1 dark:text-white">
                    {{ $currency['formatted'] }}
                </div>
                <div class="text-sm text-slate-600 dark:text-slate-400">Solde disponible</div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('operations.create', ['type' => 'EXCHANGE']) }}"
                        class="text-sm text-{{ $currency['color'] }}-600 dark:text-{{ $currency['color'] }}-400 hover:text-{{ $currency['color'] }}-700 dark:hover:text-{{ $currency['color'] }}-300 font-medium flex items-center gap-1">
                    <span class="iconify" data-icon="lucide:repeat" style="width: 14px; height: 14px;"></span>
                    Transférer
                </a>

                <a href="{{ route('cashbook.index') }}"
                   class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 font-medium flex items-center gap-1">
                    <span class="iconify" data-icon="lucide:eye" style="width: 14px; height: 14px;"></span>
                    Détails
                </a>
            </div>
        </div>
    @endforeach
</div>
