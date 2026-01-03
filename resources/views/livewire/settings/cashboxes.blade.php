<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Gestion des Caisses</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Configurez et gérez vos points d'encaissement et de décaissement.</p>
        </div>
        <button wire:click="create" class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black shadow-xl shadow-blue-500/20 transition-all transform hover:-translate-y-1">
            <span class="iconify" data-icon="lucide:plus" style="width: 20px;"></span>
            Nouvelle Caisse
        </button>
    </div>

    <!-- Cashboxes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($cashboxes as $cb)
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden hover:shadow-xl transition-all group">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                        <span class="iconify" data-icon="lucide:wallet" style="width: 28px; height: 28px;"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $cb->id }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all">
                            <span class="iconify" data-icon="lucide:edit-3" style="width: 18px;"></span>
                        </button>
                        <button wire:click="toggleStatus({{ $cb->id }})" class="p-2 {{ $cb->is_active ? 'text-emerald-500 hover:text-red-500 hover:bg-red-50' : 'text-slate-300 hover:text-emerald-500 hover:bg-emerald-50' }} rounded-lg transition-all">
                            <span class="iconify" data-icon="lucide:{{ $cb->is_active ? 'power' : 'power-off' }}" style="width: 18px;"></span>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ $cb->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 min-h-[40px]">{{ $cb->description ?: 'Aucune description fournie.' }}</p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-700">
                    <span class="px-3 py-1 {{ $cb->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500' }} text-[10px] font-black rounded-full uppercase tracking-wider">
                        {{ $cb->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        {{ $cb->operations_count ?? 0 }} Opérations
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($cashboxes->isEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700 p-12 text-center">
        <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="iconify text-slate-300" data-icon="lucide:inbox" style="width: 40px; height: 40px;"></span>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Aucune caisse configurée</h3>
        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-8">Commencez par créer votre première caisse pour enregistrer des opérations.</p>
        <button wire:click="create" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold">Créer ma première caisse</button>
    </div>
    @endif

    <!-- Modal Form -->
    <div x-data="{ show: @entangle('showModal') }" x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $editingCashboxId ? 'Modifier la Caisse' : 'Nouvelle Caisse' }}</h2>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="iconify" data-icon="lucide:x" style="width: 24px;"></span>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Nom de la caisse</label>
                            <input type="text" wire:model="name" placeholder="Ex: Caisse Centrale, Bureau A..."
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-bold">
                            @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Description (Optionnel)</label>
                            <textarea wire:model="description" rows="3" placeholder="À quoi sert cette caisse ?"
                                      class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white"></textarea>
                            @error('description') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl">
                            <input type="checkbox" wire:model="is_active" id="is_active_check" class="w-5 h-5 text-blue-600 rounded-lg border-none focus:ring-transparent">
                            <label for="is_active_check" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Caisse active</label>
                        </div>

                        <div class="flex items-center gap-3 pt-4">
                            <button type="button" @click="show = false" class="flex-1 px-6 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold rounded-xl hover:bg-slate-50 transition-all">Annuler</button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-black rounded-xl shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
