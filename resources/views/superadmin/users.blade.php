@extends('layouts.superadmin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Gestion des Utilisateurs</h1>
    <p class="text-slate-500 dark:text-slate-400">Liste de tous les utilisateurs inscrits sur la plateforme.</p>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3">Utilisateur</th>
                    <th class="px-6 py-3">Rôle</th>
                    <th class="px-6 py-3">Organisation (Tenant)</th>
                    <th class="px-6 py-3">Dernière Connexion</th>
                    <th class="px-6 py-3">Inscrit le</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-xs font-semibold">
                        @if($user->role === 'superadmin')
                            <span class="px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">SuperAdmin</span>
                        @elseif($user->role === 'manager')
                            <span class="px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">Manager</span>
                        @else
                            <span class="px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">{{ strtoupper($user->role) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                        {{ $user->tenant ? $user->tenant->name : '-' }}
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
        {{ $users->links() }}
    </div>
</div>
@endsection
