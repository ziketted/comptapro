<div class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-12">
    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <span class="iconify text-blue-600 dark:text-blue-400" data-icon="lucide:users" style="width: 24px; height: 24px;"></span>
                        </span>
                        Gestion des Bénéficiaires
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gérez vos fournisseurs, employés et tiers pour vos opérations financières.</p>
                </div>
                <button wire:click="create" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-blue-200 dark:shadow-none">
                    <span class="iconify mr-2" data-icon="lucide:plus"></span>
                    Nouveau Bénéficiaire
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters & Search -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 mb-6">
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="iconify text-slate-400" data-icon="lucide:search"></span>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-600 dark:text-white"
                       placeholder="Rechercher par nom...">
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Bénéficiaire</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Contact</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Description</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($beneficiaries as $beneficiary)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xs uppercase">
                                            {{ substr($beneficiary->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold text-slate-900 dark:text-white">{{ $beneficiary->name }}</span>
                                            <span class="text-xs text-slate-400">ID: #{{ str_pad($beneficiary->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ $beneficiary->phone ?: '---' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $beneficiary->description ?: '---' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:click="toggleStatus({{ $beneficiary->id }})" class="sr-only peer" {{ $beneficiary->is_active ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $beneficiary->id }})" class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <span class="iconify" data-icon="lucide:edit-2" style="width: 18px;"></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="iconify text-slate-300 mb-4" data-icon="lucide:users" style="width: 48px; height: 48px;"></span>
                                        <p class="text-slate-500">Aucun bénéficiaire trouvé.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                {{ $beneficiaries->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold dark:text-white">{{ $editingBeneficiaryId ? 'Modifier' : 'Nouveau' }} Bénéficiaire</h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <span class="iconify" data-icon="lucide:x" style="width: 24px;"></span>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nom complet</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                            @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Téléphone</label>
                            <input type="text" wire:model="phone" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white font-medium">
                            @error('phone') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Description / Note</label>
                            <textarea wire:model="description" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-blue-600 dark:text-white text-sm"></textarea>
                            @error('description') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3 py-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Compte actif</span>
                        </div>

                        <div class="pt-6 flex gap-3">
                            <button type="button" wire:click="closeModal" class="flex-1 px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">ANNULER</button>
                            <button type="submit" class="flex-2 px-10 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 dark:shadow-none transition-all">
                                {{ $editingBeneficiaryId ? 'METTRE À JOUR' : 'CRÉER LE BÉNÉFICIAIRE' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
