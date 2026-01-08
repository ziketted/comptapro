@extends('layouts.app')

@section('title', 'Abonnement Expiré')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="max-w-lg w-full bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8 text-center">
        
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="iconify" data-icon="lucide:lock" style="width: 32px; height: 32px;"></span>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Abonnement Expiré</h1>
        
        <p class="text-slate-600 dark:text-slate-300 mb-8">
            Votre période d'essai ou votre abonnement est arrivé à échéance. 
            L'accès aux fonctionnalités est restreint.
            <br>
            <span class="font-medium">Veuillez activer une nouvelle licence pour continuer.</span>
        </p>

        <form action="{{ route('subscription.activate') }}" method="POST" class="text-left">
            @csrf
            
            <div class="mb-4">
                <label for="license_key" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Clé de licence
                </label>
                <input type="text" 
                       name="license_key" 
                       id="license_key" 
                       class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-red-500 focus:border-red-500 font-mono text-center text-lg"
                       placeholder="XXXX-XXXX-XXXX-XXXX"
                       required>
                @error('license_key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Débloquer mon compte
            </button>
        </form>
        
        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Si vous pensez qu'il s'agit d'une erreur, contactez le support.
            </p>
        </div>
    </div>
</div>
@endsection
