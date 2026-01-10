<div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col h-full" 
     x-data="{ 
        chart: null,
        chartData: @entangle('chartData'),
        initChart() {
            const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
            if (this.chart) this.chart.destroy();
            
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            label: 'Recettes',
                            data: this.chartData.income,
                            borderColor: '#10b981',
                            backgroundColor: '#10b98120',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#10b981',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        },
                        {
                            label: 'Dépenses',
                            data: this.chartData.expense,
                            borderColor: '#ef4444',
                            backgroundColor: '#ef444420',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ef4444',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: '#94a3b8',
                                font: { size: 11 },
                                maxRotation: 0
                            }
                        },
                        y: {
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { 
                                color: '#94a3b8',
                                font: { size: 11 },
                                callback: function(value) {
                                    return value.toLocaleString() + ' ' + @js(auth()->user()->tenant->default_currency);
                                }
                            }
                        }
                    }
                }
            });
        }
     }"
     x-init="initChart(); $watch('chartData', () => initChart())"
     wire:ignore
>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="iconify text-blue-600" data-icon="lucide:trending-up"></span>
                Recettes vs Dépenses
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                @if($period === '7J') 7 derniers jours @elseif($period === '30J') 30 derniers jours @elseif($period === '90J') 90 derniers jours @elseif($period === 'MONTHLY') Ce mois-ci @else Cette année @endif
            </p>
        </div>
        
        <div class="flex items-center bg-slate-100 dark:bg-slate-900/50 p-1 rounded-xl">
            @foreach(['7J', '30J', '90J', 'MONTHLY', 'YEARLY'] as $p)
                <button wire:click="setPeriod('{{ $p }}')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === $p ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                    {{ $p === 'MONTHLY' ? 'Mois' : ($p === 'YEARLY' ? 'An' : $p) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Chart Container -->
    <div class="flex-1 min-h-[300px] relative">
        <canvas id="incomeExpenseChart"></canvas>
    </div>

    <!-- Legend -->
    <div class="flex items-center gap-6 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Recettes</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Dépenses</span>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            // Re-init on navigation
            if (typeof initChart === 'function') initChart();
        });
    </script>
</div>
