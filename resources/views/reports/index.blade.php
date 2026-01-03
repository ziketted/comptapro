@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Rapports Financiers</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Consultez et exportez les états financiers de votre organisation.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        <!-- Journal de Caisse -->
        <a href="{{ route('reports.cash-journal') }}" class="group block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-blue-500 hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40 transition-colors">
                    <span class="iconify text-blue-600 dark:text-blue-400" data-icon="lucide:list" style="width: 24px; height: 24px;"></span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">Journal de Caisse</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Liste chronologique détaillée de toutes les opérations validées avec filtres avancés.</p>
                </div>
            </div>
        </a>

        <!-- Rapport par Compte -->
        <a href="{{ route('reports.account-report') }}" class="group block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-purple-500 hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                    <span class="iconify text-purple-600 dark:text-purple-400" data-icon="lucide:pie-chart" style="width: 24px; height: 24px;"></span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-purple-600 dark:group-hover:text-purple-400">Rapport par Compte</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Ventilation des recettes et dépenses par compte pour analyser vos postes budgétaires.</p>
                </div>
            </div>
        </a>

        <!-- Solde à Date -->
        <a href="{{ route('reports.balance') }}" class="group block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-emerald-500 hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/40 transition-colors">
                    <span class="iconify text-emerald-600 dark:text-emerald-400" data-icon="lucide:calendar-check" style="width: 24px; height: 24px;"></span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Solde à Date</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">État des soldes de toutes vos caisses et devises à une date précise.</p>
                </div>
            </div>
        </a>

        <!-- Résultat Simplifié -->
        <a href="{{ route('reports.profit-loss') }}" class="group block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-amber-500 hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 transition-colors">
                    <span class="iconify text-amber-600 dark:text-amber-400" data-icon="lucide:calculator" style="width: 24px; height: 24px;"></span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-amber-600 dark:group-hover:text-amber-400">Résultat Simplifié</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Compte de résultat simplifié (Recettes vs Dépenses) sur une période donnée.</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
