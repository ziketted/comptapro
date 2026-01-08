@extends('layouts.superadmin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Administration</h1>
    <p class="text-slate-500 dark:text-slate-400">Vue d'ensemble de la plateforme SaaS.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Tenants -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Écoles</h3>
            <span class="iconify text-indigo-500" data-icon="lucide:building-2" style="width: 24px; height: 24px;"></span>
        </div>
        <div class="flex items-baseline">
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['tenants_total'] }}</p>
            <span class="ml-2 text-sm text-green-600 dark:text-green-400 font-medium">{{ $stats['tenants_active'] }} Actives</span>
        </div>
    </div>

    <!-- Users -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Utilisateurs</h3>
            <span class="iconify text-blue-500" data-icon="lucide:users" style="width: 24px; height: 24px;"></span>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['users_total'] }}</p>
    </div>

    <!-- Active Licenses -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Licences Actives</h3>
            <span class="iconify text-emerald-500" data-icon="lucide:key" style="width: 24px; height: 24px;"></span>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['licenses_active'] }}</p>
    </div>

    <!-- Revenue Estimation (Mock) -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Revenu Est. (Mois)</h3>
            <span class="iconify text-green-500" data-icon="lucide:dollar-sign" style="width: 24px; height: 24px;"></span>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($stats['licenses_active'] * 4.90, 2) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Quick Actions -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Actions Rapides</h3>
        <div class="space-y-3">
            <a href="{{ route('superadmin.licenses') }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Générer des licences
            </a>
            <a href="{{ route('superadmin.tenants') }}" class="block w-full text-center px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700">
                Gérer une organisation
            </a>
        </div>
    </div>

    <!-- Recent Activity Placeholder -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
         <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Activité Système</h3>
         <p class="text-slate-500 dark:text-slate-400 text-sm">Les logs d'activité apparaîtront ici.</p>
    </div>
</div>
@endsection
