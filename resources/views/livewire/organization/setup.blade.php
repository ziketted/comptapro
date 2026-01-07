<div class="min-h-screen bg-white text-black flex items-center justify-center font-sans">
    
    <!-- Loading Overlay -->
    <div wire:loading wire:target="setupOrganization" class="fixed inset-0 z-50 bg-white/90 backdrop-blur-sm">
        <div class="h-full w-full flex flex-col items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-black mb-4"></div>
            <p class="text-sm text-gray-600 font-medium">Finalisation...</p>
        </div>
    </div>

    <div class="w-full max-w-lg p-6" id="wizard-container">
        
        <!-- Progress Indicator -->
        <div class="mb-10 flex items-center justify-between text-xs font-medium text-gray-400">
            <span id="progress-text">Étape 1 sur 4</span>
            <span id="step-name" class="text-black">Nom de l'organisation</span>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="setupOrganization" id="wizard-form">

            <!-- Step 1: Nom -->
            <section class="step" data-step="1">
                <div class="mb-6 flex justify-center">
                    <div class="p-3 bg-gray-50 rounded-full">
                        <!-- Icon: Building -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                            <path d="M9 22v-4h6v4"></path>
                            <path d="M8 6h.01"></path>
                            <path d="M16 6h.01"></path>
                            <path d="M8 10h.01"></path>
                            <path d="M16 10h.01"></path>
                            <path d="M8 14h.01"></path>
                            <path d="M16 14h.01"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-center mb-2">Quel est le nom de votre structure ?</h1>
                <p class="text-gray-500 text-center mb-8 text-sm">Ce nom sera affiché sur vos factures et rapports.</p>

                <div class="space-y-4">
                    <input type="text" 
                        wire:model="name"
                        id="input-name"
                        placeholder="Ex: Ma Société SARL" 
                        class="w-full text-lg px-0 py-3 bg-transparent border-b-2 border-gray-200 focus:border-black focus:outline-none focus:ring-0 transition-colors placeholder-gray-300 text-center"
                        autofocus
                    >
                    @error('name') <p class="text-red-500 text-xs text-center">{{ $message }}</p> @enderror
                </div>
            </section>

            <!-- Step 2: Type -->
            <section class="step hidden" data-step="2">
                <div class="mb-6 flex justify-center">
                    <div class="p-3 bg-gray-50 rounded-full">
                         <!-- Icon: Tag -->
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"></path>
                            <circle cx="7" cy="7" r=".5"></circle>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-center mb-2">Quel est votre secteur d'activité ?</h1>
                <p class="text-gray-500 text-center mb-8 text-sm">Cela nous permet de personnaliser votre expérience.</p>

                <div class="space-y-6">
                    <select wire:model="business_type" id="input-type" class="w-full text-lg px-4 py-3 bg-gray-50 border-none rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-center appearance-none cursor-pointer">
                        <option value="">Sélectionner une activité</option>
                        <option value="Commerce">Commerce</option>
                        <option value="Boutique">Boutique</option>
                        <option value="Hôpital">Hôpital</option>
                        <option value="École">École</option>
                        <option value="Magasin">Magasin</option>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Pharmacie">Pharmacie</option>
                        <option value="Entreprise">Entreprise</option>
                        <option value="Autres">Autres</option>
                    </select>
                    @error('business_type') <p class="text-red-500 text-xs text-center">{{ $message }}</p> @enderror

                    <div id="other-type-container" class="{{ $business_type === 'Autres' ? 'block' : 'hidden' }}">
                        <input type="text" 
                            wire:model="business_type_other"
                            id="input-type-other"
                            placeholder="Précisez votre activité" 
                            class="w-full text-lg px-0 py-3 bg-transparent border-b-2 border-gray-200 focus:border-black focus:outline-none focus:ring-0 transition-colors placeholder-gray-300 text-center"
                        >
                        @error('business_type_other') <p class="text-red-500 text-xs text-center">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <!-- Step 3: Taux -->
            <section class="step hidden" data-step="3">
                <div class="mb-6 flex justify-center">
                    <div class="p-3 bg-gray-50 rounded-full">
                        <!-- Icon: Percent -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="5" x2="5" y2="19"></line>
                            <circle cx="6.5" cy="6.5" r="2.5"></circle>
                            <circle cx="17.5" cy="17.5" r="2.5"></circle>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-center mb-2">Taux du jour</h1>
                <p class="text-gray-500 text-center mb-8 text-sm">Définissez la valeur de 1 USD en CDF.</p>

                <div class="flex flex-col items-center">
                    <div class="flex items-baseline justify-center gap-3">
                         <span class="text-xl text-gray-400 font-medium">1 USD =</span>
                        <input type="number" 
                            wire:model="usd_to_cdf"
                            id="input-rate"
                            step="0.01"
                            placeholder="2850" 
                            class="w-32 text-4xl font-bold px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-black focus:outline-none focus:ring-0 text-center transition-colors placeholder-gray-200"
                        >
                        <span class="text-xl text-black font-bold">CDF</span>
                    </div>
                     @error('usd_to_cdf') <p class="text-red-500 text-xs text-center mt-2">{{ $message }}</p> @enderror
                </div>
            </section>

             <!-- Step 4: Validation -->
             <section class="step hidden" data-step="4">
                <div class="mb-6 flex justify-center">
                    <div class="p-3 bg-black rounded-full text-white">
                        <!-- Icon: Check -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-center mb-2">Tout est prêt !</h1>
                <p class="text-gray-500 text-center mb-8 text-sm">Vérifiez vos informations avant de commencer.</p>

                <div class="bg-gray-50 rounded-xl p-6 space-y-4 text-sm max-w-sm mx-auto mb-8">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nom</span>
                        <span class="font-semibold" x-text="$wire.name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Activité</span>
                        <span class="font-semibold" x-text="$wire.business_type === 'Autres' ? $wire.business_type_other : $wire.business_type"></span>
                    </div>
                     <div class="flex justify-between">
                        <span class="text-gray-500">Taux Initial</span>
                        <span class="font-semibold">1 USD = <span x-text="$wire.usd_to_cdf"></span> CDF</span>
                    </div>
                </div>
            </section>

            <!-- Navigation Actions -->
            <div class="mt-10 flex flex-col gap-3">
                
                <button type="button" 
                    id="btn-next" 
                    class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3.5 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span>Suivant</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </button>

                 <button type="submit" 
                    id="btn-submit" 
                    class="hidden w-full bg-black hover:bg-gray-800 text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2"
                >
                    <span>Valider et terminer</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </button>

                <button type="button" 
                    id="btn-prev" 
                    class="hidden w-full text-gray-400 hover:text-black font-medium py-2 transition-colors text-sm"
                >
                    Retour
                </button>

            </div>

        </form>
    </div>

    <!-- Minimalist Vanilla JS Logic -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            let currentStep = 1;
            const totalSteps = 4;
            
            // DOM Elements
            const steps = document.querySelectorAll('.step');
            const btnNext = document.getElementById('btn-next');
            const btnPrev = document.getElementById('btn-prev');
            const btnSubmit = document.getElementById('btn-submit');
            const progressText = document.getElementById('progress-text');
            const stepNameLabel = document.getElementById('step-name');

            // Inputs
            const inputName = document.getElementById('input-name');
            const inputType = document.getElementById('input-type');
            const inputTypeOther = document.getElementById('input-type-other');
            const inputRate = document.getElementById('input-rate');
            const otherTypeContainer = document.getElementById('other-type-container');

            // Step Titles
            const stepTitles = {
                1: 'Nom de l\'organisation',
                2: 'Type d\'activité',
                3: 'Taux de change',
                4: 'Vérification'
            };

            // Initial State Check
            validateStep();

            // Event Listeners
            btnNext.addEventListener('click', () => changeStep(1));
            btnPrev.addEventListener('click', () => changeStep(-1));

            // Input Listeners for Validation
            [inputName, inputType, inputTypeOther, inputRate].forEach(input => {
                input.addEventListener('input', validateStep);
                input.addEventListener('change', validateStep); // For select
            });

            // Handle "Other" type visibility
            inputType.addEventListener('change', function() {
                if (this.value === 'Autres') {
                    otherTypeContainer.classList.remove('hidden');
                } else {
                    otherTypeContainer.classList.add('hidden');
                }
                validateStep();
            });

            function changeStep(direction) {
                const newStep = currentStep + direction;
                
                if (newStep >= 1 && newStep <= totalSteps) {
                    // Hide current
                    document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('hidden');
                    
                    // Show new
                    currentStep = newStep;
                    const nextStepEl = document.querySelector(`.step[data-step="${currentStep}"]`);
                    nextStepEl.classList.remove('hidden');

                    // Update UI
                    updateUI();
                    validateStep();

                    // Focus Logic
                    if (currentStep === 1) setTimeout(() => inputName.focus(), 100);
                    if (currentStep === 3) setTimeout(() => inputRate.focus(), 100);
                }
            }

            function updateUI() {
                // Progress
                progressText.innerText = `Étape ${currentStep} sur ${totalSteps}`;
                stepNameLabel.innerText = stepTitles[currentStep];

                // Buttons visibility
                if (currentStep === 1) {
                    btnPrev.classList.add('hidden');
                } else {
                    btnPrev.classList.remove('hidden');
                }

                if (currentStep === totalSteps) {
                    btnNext.classList.add('hidden');
                    btnSubmit.classList.remove('hidden');
                } else {
                    btnNext.classList.remove('hidden');
                    btnSubmit.classList.add('hidden');
                }
            }

            function validateStep() {
                let isValid = false;

                switch(currentStep) {
                    case 1:
                        isValid = inputName.value.trim().length > 0;
                        break;
                    case 2:
                        if (inputType.value === 'Autres') {
                            isValid = inputTypeOther.value.trim().length > 0;
                        } else {
                            isValid = inputType.value !== '';
                        }
                        break;
                    case 3:
                        isValid = inputRate.value > 0;
                        break;
                    case 4:
                        isValid = true;
                        break;
                }

                btnNext.disabled = !isValid;
                
                // Add Enter key listener for convenience
                if (isValid) {
                     // Note: We avoid auto-submission on Enter to prevent accidental skips
                }
            }
        });
    </script>
</div>
