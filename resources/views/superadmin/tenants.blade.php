@extends('layouts.superadmin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Gestion des Organisations (Tenants)</h1>
    <p class="text-slate-500 dark:text-slate-400">Liste de toutes les organisations enregistrées sur la plateforme.</p>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3">Organisation</th>
                    <th class="px-6 py-3">Statut</th>
                    <th class="px-6 py-3">Utilisateurs</th>
                    <th class="px-6 py-3">Créée le</th>
                    <th class="px-6 py-3">Fin d'essai</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($tenants as $tenant)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $tenant->name }}</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500">{{ $tenant->slug }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($tenant->status === 'TRIAL')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">Essai</span>
                        @elseif($tenant->status === 'ACTIVE')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Actif</span>
                        @elseif($tenant->status === 'SUSPENDED')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">Suspendu</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Expiré</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                        {{ $tenant->users_count }}
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        {{ $tenant->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">
                        {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('superadmin.tenants.toggle-status', $tenant) }}" method="POST" class="inline">
                            @csrf
                            @if($tenant->status === 'SUSPENDED')
                                <button type="submit" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 font-medium text-xs">
                                    Réactiver
                                </button>
                            @else
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-medium text-xs">
                                    Désactiver
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
