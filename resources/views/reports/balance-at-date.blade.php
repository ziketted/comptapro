@extends('layouts.app')

@section('title', 'Solde à Date')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
         <div>
           <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                    Rapports
                </a>
                <span class="text-slate-300">/</span>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Solde à Date</h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Situation de trésorerie à une date précise</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.balance.excel', request()->all()) }}" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-green-600" data-icon="lucide:file-spreadsheet" style="width: 18px; height: 18px;"></span>
                Excel
            </a>
            <a href="{{ route('reports.balance.pdf', request()->all()) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-red-600" data-icon="lucide:file-text" style="width: 18px; height: 18px;"></span>
                PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 mb-6 max-w-md">
        <form action="{{ route('reports.balance') }}" method="GET" class="flex items-end gap-4">
            <div class="w-full">
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Date de situation</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition-colors">
                Calculer
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-3">Caisse</th>
                        <th class="px-6 py-3 text-right">Solde Original</th>
                        <th class="px-6 py-3 text-right">Contre-valeur ({{ auth()->user()->tenant->default_currency }})</th>
                        <th class="px-6 py-3 text-right">Taux utilisé</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($balances as $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-3 font-medium text-slate-900 dark:text-white">
                            {{ $row['cashbox'] }}
                        </td>
                        <td class="px-6 py-3 text-right font-mono text-slate-700 dark:text-slate-300">
                            {{ number_format($row['balance'], 2) }} {{ $row['currency'] }}
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-slate-900 dark:text-white">
                            {{ number_format($row['balance_base'], 2) }}
                        </td>
                         <td class="px-6 py-3 text-right text-slate-500 dark:text-slate-400 text-xs">
                            {{ number_format($row['rate_used'], 4) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            Aucune caisse active avec solde à cette date.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
