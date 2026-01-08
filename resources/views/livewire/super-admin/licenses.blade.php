<div class="space-y-6">
    <!-- Generator Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Générateur de Licences</h2>
        <div class="flex items-end gap-4">
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre de clés</label>
                <input type="number" wire:model="amount" min="1" max="50" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button wire:click="generate" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Générer
            </button>
        </div>
        @if (session()->has('success'))
            <div class="mt-4 text-sm text-green-600 font-medium">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-900 dark:text-white">Historique des Licences</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3">Clé</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3">École (Tenant)</th>
                        <th class="px-6 py-3">Générée le</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($keys as $key)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-3 font-mono font-medium text-slate-700 dark:text-slate-300">{{ $key->key }}</td>
                        <td class="px-6 py-3">
                            @if($key->status === 'UNUSED')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">Dispo</span>
                            @elseif($key->status === 'USED')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Utilisée</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Expirée</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-slate-600 dark:text-slate-400">
                            {{ $key->tenant ? $key->tenant->name : '-' }}
                        </td>
                        <td class="px-6 py-3 text-slate-500 dark:text-slate-500">
                            {{ $key->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-3">
                            @if($key->status === 'UNUSED')
                            <button wire:click="delete({{ $key->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 confirm-delete">
                                Supprimer
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $keys->links() }}
        </div>
    </div>
</div>
