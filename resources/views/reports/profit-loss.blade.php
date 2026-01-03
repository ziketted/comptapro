@extends('layouts.app')

@section('title', 'Résultat Simplifié')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
           <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                    Rapports
                </a>
                <span class="text-slate-300">/</span>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Résultat Simplifié</h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Compte de résultat (Recettes - Dépenses)</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.profit-loss.excel', request()->all()) }}" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-green-600" data-icon="lucide:file-spreadsheet" style="width: 18px; height: 18px;"></span>
                Excel
            </a>
            <a href="{{ route('reports.profit-loss.pdf', request()->all()) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-red-600" data-icon="lucide:file-text" style="width: 18px; height: 18px;"></span>
                PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 mb-6">
        <form action="{{ route('reports.profit-loss') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Date début</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Date fin</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 text-sm font-medium transition-colors">
                    Calculer
                </button>
            </div>
        </form>
    </div>

    <!-- P&L Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Compte de Résultat</h2>
            @if(request('start_date') && request('end_date'))
                <p class="text-sm text-slate-500">Période du {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} au {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</p>
            @endif
        </div>

        <div class="p-6 space-y-4">
            <!-- Income -->
            <div class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-full">
                        <span class="iconify text-emerald-600 dark:text-emerald-300" data-icon="lucide:arrow-down-left" style="width: 20px;"></span>
                    </div>
                    <span class="font-medium text-slate-700 dark:text-slate-300">Total Recettes</span>
                </div>
                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ number_format($total_income, 2) }} {{ $base_currency }}
                </span>
            </div>

            <!-- Expense -->
            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-800 rounded-full">
                         <span class="iconify text-red-600 dark:text-red-300" data-icon="lucide:arrow-up-right" style="width: 20px;"></span>
                    </div>
                    <span class="font-medium text-slate-700 dark:text-slate-300">Total Dépenses</span>
                </div>
                <span class="text-xl font-bold text-red-600 dark:text-red-400">
                    {{ number_format($total_expense, 2) }} {{ $base_currency }}
                </span>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-700 my-4"></div>

            <!-- Result -->
            <div class="flex items-center justify-between p-4 {{ $net_result >= 0 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-red-50 dark:bg-red-900/20' }} rounded-xl border {{ $net_result >= 0 ? 'border-blue-200 dark:border-blue-800' : 'border-red-200 dark:border-red-800' }}">
                <div class="flex items-center gap-3">
                    <span class="text-lg font-bold text-slate-900 dark:text-white">RÉSULTAT NET</span>
                </div>
                <span class="text-2xl font-bold {{ $net_result >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $net_result >= 0 ? '+' : '' }}{{ number_format($net_result, 2) }} {{ $base_currency }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
