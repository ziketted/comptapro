@extends('layouts.app')

@section('title', 'Rapport par Compte')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
           <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                    Rapports
                </a>
                <span class="text-slate-300">/</span>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Rapport par Compte</h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Ventilation des opérations par compte budgétaire</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.account-report.excel', request()->all()) }}" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-green-600" data-icon="lucide:file-spreadsheet" style="width: 18px; height: 18px;"></span>
                Excel
            </a>
            <a href="{{ route('reports.account-report.pdf', request()->all()) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                <span class="iconify text-red-600" data-icon="lucide:file-text" style="width: 18px; height: 18px;"></span>
                PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 mb-6">
        <form action="{{ route('reports.account-report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Incomes -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
             <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 flex justify-between items-center">
                <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="iconify text-emerald-500" data-icon="lucide:arrow-down-left"></span>
                    Comptes de Recettes
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-700/30 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3">Compte</th>
                            <th class="px-4 py-3 text-right">Montant ({{ $baseCurrency }})</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($income as $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $row['account']->name }}</div>
                                <div class="text-xs text-slate-500">{{ $row['account']->account_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($row['total'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                Aucun compte de recette mouvementé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expenses -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
             <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 flex justify-between items-center">
                <h3 class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="iconify text-red-500" data-icon="lucide:arrow-up-right"></span>
                    Comptes de Dépenses
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-700/30 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3">Compte</th>
                            <th class="px-4 py-3 text-right">Montant ({{ $baseCurrency }})</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($expense as $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $row['account']->name }}</div>
                                <div class="text-xs text-slate-500">{{ $row['account']->account_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600 dark:text-red-400">
                                {{ number_format($row['total'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                Aucun compte de dépense mouvementé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
