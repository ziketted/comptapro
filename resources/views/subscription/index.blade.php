@extends('layouts.app')

@section('title', 'Mon Abonnement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mon Abonnement</h1>
        <p class="text-slate-500 dark:text-slate-400">Gérez votre abonnement et vos licences.</p>
    </div>

    <!-- Status Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Statut actuel</h2>
            
            @if($tenant->status === 'ACTIVE')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Actif
                </span>
            @elseif($tenant->status === 'TRIAL')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    Période d'essai
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Expiré
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Date d'expiration</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white">
                    @if($tenant->status === 'TRIAL')
                        {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d/m/Y') : '-' }}
                        <span class="text-xs font-normal text-orange-600 ml-2">
                            (Reste {{ $tenant->getTrialDaysRemaining() }} jours)
                        </span>
                    @else
                        {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d/m/Y') : '-' }}
                    @endif
                </p>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Plan</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ $tenant->business_type ?? 'Standard' }}
                </p>
            </div>

            @if($activeLicense)
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 md:col-span-2">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Ma licence actuelle</p>
                <div class="flex items-center justify-between">
                    <p class="text-lg font-mono font-bold text-slate-900 dark:text-white tracking-wider">
                        {{ $activeLicense->key }}
                    </p>
                    <span class="text-xs text-slate-500 px-2 py-1 bg-white dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700">
                        Activée le {{ $activeLicense->activated_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Activation Form -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Activer une licence</h2>
        
        <form action="{{ route('subscription.activate') }}" method="POST" class="max-w-xl">
            @csrf
            
            <div class="mb-4">
                <label for="license_key" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Clé de licence
                </label>
                <div class="relative">
                    <input type="text" 
                           name="license_key" 
                           id="license_key" 
                           class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 font-mono"
                           placeholder="XXXX-XXXX-XXXX-XXXX"
                           required>
                </div>
                @error('license_key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Activer l'abonnement
            </button>
        </form>
    </div>
</div>
@endsection
