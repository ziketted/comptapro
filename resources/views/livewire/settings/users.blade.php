<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center px-6 lg:px-0">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gestion des utilisateurs</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les accès à votre organisation</p>
            </div>
            <button wire:click="$toggle('showCreateForm')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-lg shadow-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Nouvel utilisateur
            </button>
        </div>

        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Create/Edit Form -->
        @if($showCreateForm)
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700/50">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $isEditing ? 'Modifier l\'utilisateur' : 'Ajouter un collaborateur' }}
                </h3>
                <form wire:submit.prevent="{{ $isEditing ? 'updateUser' : 'createUser' }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                            <input wire:model="name" type="text" placeholder="Ex: Jean Dupont" class="w-full rounded-xl border-gray-300 dark:border-slate-600 dark:bg-slate-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input wire:model="email" type="email" placeholder="jean@exemple.com" class="w-full rounded-xl border-gray-300 dark:border-slate-600 dark:bg-slate-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rôle</label>
                            <select wire:model="role" class="w-full rounded-xl border-gray-300 dark:border-slate-600 dark:bg-slate-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="cashier">Caissier (Opérations uniquement)</option>
                                <option value="manager">Manager (Accès complet)</option>
                            </select>
                            @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Mot de passe 
                                @if($isEditing) <span class="text-gray-400 font-normal text-xs">(Laisser vide pour ne pas changer)</span> @endif
                            </label>
                            <input wire:model="password" type="text" placeholder="{{ $isEditing ? 'Nouveau mot de passe (optionnel)' : 'Minimum 8 caractères' }}" class="w-full rounded-xl border-gray-300 dark:border-slate-600 dark:bg-slate-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="cancelEdit" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Annuler</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/20">
                            {{ $isEditing ? 'Mettre à jour' : 'Créer l\'utilisateur' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Users List -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Rôle</th>
                            <th class="px-6 py-4">Date d'ajout</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            <div class="text-xs">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->role === 'manager')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                            Manager
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            Caissier
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($user->id !== auth()->id())
                                        <button 
                                            wire:click="editUser({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-800 font-medium text-xs hover:underline mr-3"
                                        >
                                            Modifier
                                        </button>
                                        <button 
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cet utilisateur ?" 
                                            class="text-red-500 hover:text-red-700 font-medium text-xs hover:underline"
                                        >
                                            Supprimer
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Moi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700/50">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
