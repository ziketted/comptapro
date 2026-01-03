<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center p-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    
    <!-- Loading Overlay - Fixed Position (Outside relative container) -->
    <div wire:loading wire:target="submitForm, setupOrganization" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-slate-900/90 backdrop-blur-sm">
        <div class="animate-spin rounded-full h-20 w-20 border-t-4 border-b-4 border-blue-500 mb-6"></div>
        <h3 class="text-2xl font-bold text-white">Création de votre organisation...</h3>
        <p class="text-blue-200 text-base mt-2">Veuillez patienter quelques instants</p>
    </div>

    <div class="w-full max-w-2xl relative">
        <!-- Main Card - Compact -->
        <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 rounded-3xl p-6 shadow-2xl" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            
            <!-- Header with Logo - Compact -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-5 py-2 rounded-2xl mb-4">
                    <div class="bg-blue-500 text-white font-bold text-lg px-3 py-1.5 rounded-lg">C+</div>
                    <div class="text-xl font-bold text-white">Compta+ C+</div>
                </div>
                
                <h1 class="text-xl font-bold text-white mb-1">Configuration de votre organisation</h1>
                <p class="text-blue-100 text-sm">Étape {{ $currentStep }} sur {{ $totalSteps }}</p>
            </div>

            <!-- Progress Steps - Compact -->
            <div class="mb-6">
                <div class="flex items-center justify-center gap-3">
                    @for ($i = 1; $i <= $totalSteps; $i++)
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 mb-1.5
                                {{ $currentStep >= $i ? 'bg-blue-500 text-white shadow-lg' : 'bg-blue-900/50 text-blue-300' }}">
                                {{ $i }}
                            </div>
                            <span class="text-[10px] uppercase tracking-wider {{ $currentStep >= $i ? 'text-white font-medium' : 'text-blue-300' }}">
                                @if($i == 1) Nom
                                @elseif($i == 2) Type
                                @elseif($i == 3) Taux
                                @else Valid.
                                @endif
                            </span>
                        </div>
                        @if($i < $totalSteps)
                            <div class="w-12 h-0.5 mb-5 transition-all duration-300 {{ $currentStep > $i ? 'bg-blue-400' : 'bg-blue-900/50' }}"></div>
                        @endif
                    @endfor
                </div>
            </div>

            <!-- Form Container - Compact -->
            <div class="bg-blue-900/30 backdrop-blur-sm rounded-2xl p-6 border border-blue-500/20">
                <!-- Error/Success Messages -->
                @if (session()->has('error'))
                    <div class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-xl text-red-200 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit="submitForm">
                    
                    <!-- Step 1: Organization Name -->
                    @if($currentStep == 1)
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <h2 class="text-lg font-bold text-white mb-1">Nom de l'organisation</h2>
                                <p class="text-sm text-blue-200">Comment s'appelle votre structure ?</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-blue-100 mb-1.5">Nom de l'organisation *</label>
                                <input wire:model.live="name" type="text" placeholder="Ex: Ma Société SARL" autofocus
                                    class="w-full px-4 py-2.5 bg-blue-950/50 border border-blue-500/30 rounded-xl text-sm text-white placeholder-blue-300/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                                @error('name') <span class="text-red-300 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Business Type -->
                    @if($currentStep == 2)
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <h2 class="text-lg font-bold text-white mb-1">Type d'activité</h2>
                                <p class="text-sm text-blue-200">Quel est votre secteur d'activité ?</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-blue-100 mb-1.5">Type d'activité *</label>
                                <select wire:model.live="business_type" 
                                    class="w-full px-4 py-2.5 bg-blue-950/50 border border-blue-500/30 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                                    <option value="">Sélectionnez un type d'activité</option>
                                    <option value="Commerce">Commerce</option>
                                    <option value="Boutique">Boutique</option>
                                    <option value="Hôpital">Hôpital</option>
                                    <option value="École">École</option>
                                    <option value="Magasin">Magasin</option>
                                    <option value="Bar">Bar</option>
                                    <option value="Restaurant">Restaurant</option>
                                    <option value="Véhicule">Véhicule</option>
                                    <option value="Entreprise">Entreprise</option>
                                    <option value="Pharmacie">Pharmacie</option>
                                    <option value="Salon de coiffure">Salon de coiffure</option>
                                    <option value="Garage / Mécanique">Garage / Mécanique</option>
                                    <option value="Supermarché">Supermarché</option>
                                    <option value="Hôtel">Hôtel</option>
                                    <option value="Bureau administratif">Bureau administratif</option>
                                    <option value="Eglise">Eglise</option>
                                    <option value="Autres">Autres (précisez)</option>
                                </select>
                                @error('business_type') <span class="text-red-300 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($business_type === 'Autres')
                                <div>
                                    <label class="block text-sm font-medium text-blue-100 mb-1.5">Précisez votre activité *</label>
                                    <input wire:model.live="business_type_other" type="text" placeholder="Ex: Agence de voyage, Studio photo..." 
                                        class="w-full px-4 py-2.5 bg-blue-950/50 border border-blue-500/30 rounded-xl text-sm text-white placeholder-blue-300/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                                    @error('business_type_other') <span class="text-red-300 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Step 3: Exchange Rates -->
                    @if($currentStep == 3)
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <h2 class="text-lg font-bold text-white mb-1">Taux de Change</h2>
                                <p class="text-sm text-blue-200">Configuration du taux du jour</p>
                            </div>

                            <div class="flex justify-center">
                                <div class="w-full max-w-xs">
                                    <label class="block text-sm font-medium text-blue-100 mb-1.5 text-center">1 USD = ??? CDF</label>
                                    <div class="relative">
                                        <input wire:model.live="usd_to_cdf" type="number" step="0.01" placeholder="Ex: 2850"
                                            class="w-full px-4 py-3 bg-blue-950/50 border border-blue-500/30 rounded-xl text-lg text-white text-center focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-blue-300 text-sm font-medium">CDF</div>
                                    </div>
                                    @error('usd_to_cdf') <span class="text-red-300 text-xs mt-1 block text-center">{{ $message }}</span> @enderror
                                    <p class="text-xs text-blue-300/70 text-center mt-2">Entrez le taux de change actuel pour 1 Dollar</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 4: Validation -->
                    @if($currentStep == 4)
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <h2 class="text-lg font-bold text-white mb-1">Validation</h2>
                                <p class="text-sm text-blue-200">Vérifiez et confirmez la création</p>
                            </div>

                            <div class="bg-blue-950/50 rounded-xl p-4 space-y-3 border border-blue-500/30">
                                <div class="flex justify-between items-center border-b border-blue-500/20 pb-2">
                                    <span class="text-blue-200 text-sm">Organisation</span>
                                    <span class="text-white font-medium">{{ $name }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-blue-500/20 pb-2">
                                    <span class="text-blue-200 text-sm">Activité</span>
                                    <span class="text-white font-medium">{{ $business_type === 'Autres' ? $business_type_other : $business_type }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-200 text-sm">Taux (USD → CDF)</span>
                                    <span class="text-white font-medium">{{ number_format((float)$usd_to_cdf, 0, ',', ' ') }} CDF</span>
                                </div>
                            </div>

                            <div class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                                <p class="text-sm text-blue-100 font-medium mb-1.5 text-center">Configuration automatique incluse :</p>
                                <ul class="list-disc list-inside space-y-0.5 text-sm text-blue-200 text-center">
                                    <li>Caisses USD, EUR, CDF</li>
                                    <li>Période d'essai de 7 jours</li>
                                    <li>Rôle Manager</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Navigation Buttons -->
                    <div class="flex items-center gap-3 mt-6">
                        @if($currentStep > 1)
                            <button type="button" wire:click="previousStep" 
                                class="flex-1 bg-blue-800/50 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border border-blue-500/30">
                                Précédent
                            </button>
                        @endif

                        @php
                            $isDisabled = false;
                            if ($currentStep == 1 && empty($name)) $isDisabled = true;
                            if ($currentStep == 2 && (empty($business_type) || ($business_type == 'Autres' && empty($business_type_other)))) $isDisabled = true;
                            if ($currentStep == 3 && empty($usd_to_cdf)) $isDisabled = true;
                        @endphp

                        <button type="submit" 
                            @if($isDisabled) disabled @endif
                            class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg flex items-center justify-center gap-2
                            {{ $isDisabled 
                                ? 'bg-slate-700 text-slate-400 cursor-not-allowed shadow-none' 
                                : 'bg-blue-500 hover:bg-blue-600 text-white hover:shadow-xl' }}">
                            @if($currentStep == $totalSteps)
                                <span>Créer mon organisation</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5l7 7-7 7"></path>
                                </svg>
                            @else
                                <span>Suivant</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5l7 7-7 7"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
