<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Fonctionnalités & Modules</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Gérez l'activation des modules optionnels pour simplifier ou enrichir l'interface.</p>
    </div>

    <div class="space-y-6">
        <!-- Cash Management Setting -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-lg">
                        <span class="iconify" data-icon="lucide:wallet" style="width: 20px;"></span>
                    </span>
                    Gestion des Caisses
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-xl">
                    Active la sélection explicite des caisses lors des opérations. 
                    <br>
                    <span class="text-xs text-orange-500 font-bold uppercase mt-1 inline-block">Si désactivé :</span> La caisse par défaut sera toujours utilisée automatiquement.
                </p>
            </div>
            
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="enable_cash_management" class="sr-only peer">
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Active</span>
            </label>
        </div>

        <!-- Beneficiaries Setting -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-lg">
                        <span class="iconify" data-icon="lucide:users" style="width: 20px;"></span>
                    </span>
                    Gestion des Bénéficiaires
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-xl">
                    Permet d'associer un tiers (bénéficiaire) à chaque opération.
                    <br>
                    <span class="text-xs text-orange-500 font-bold uppercase mt-1 inline-block">Si désactivé :</span> Le champ bénéficiaire sera masqué et inutilisé.
                </p>
            </div>
            
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="enable_beneficiaries" class="sr-only peer">
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Active</span>
            </label>
        </div>

        <!-- Reference Setting -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg">
                        <span class="iconify" data-icon="lucide:hash" style="width: 20px;"></span>
                    </span>
                    Champ Référence
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-xl">
                    Affiche un champ pour saisir un numéro de facture, reçu ou autre identifiant.
                </p>
            </div>
            
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="enable_reference" class="sr-only peer">
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-slate-300 dark:peer-focus:ring-slate-600 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-slate-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Active</span>
            </label>
        </div>

        <!-- Attachment Setting -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg">
                        <span class="iconify" data-icon="lucide:paperclip" style="width: 20px;"></span>
                    </span>
                    Pièces Jointes
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-xl">
                    Permet de télécharger un fichier (image, PDF) pour justifier l'opération.
                </p>
            </div>
            
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="enable_attachment" class="sr-only peer">
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-slate-300 dark:peer-focus:ring-slate-600 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-slate-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Active</span>
            </label>
        </div>
    </div>
</div>
