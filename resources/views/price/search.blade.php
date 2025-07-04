<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.comparator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs page="search" />
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-6 text-white">

                    <!-- Search Form -->
                    <div class="max-w-2xl mx-auto">
                        <form method="POST" action="{{ \App\Helpers\LocalizedRoute::url('price.search.submit') }}" class="space-y-6" id="searchForm">
                            @csrf
                            
                            <div>
                                <x-input-label for="isbn" :value="__('messages.isbn')" />
                                <x-text-input id="isbn" class="block mt-1 w-full" type="text" name="isbn" :value="old('isbn', $isbn)" required autofocus />
                                <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ml-3">
                                    {{ __('messages.search') }}
                                </x-primary-button>
                            </div>
                        </form>
                        
                        <!-- Info sur le temps d'attente -->
                        <div class="mt-4 text-center">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center justify-center text-blue-700">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span class="text-sm font-medium">{{ __('messages.estimated_wait_time') }}</span>
                                </div>
                                <p class="text-xs text-blue-600 mt-1">
                                    {{ __('messages.search_description_detailed') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Overlay (caché par défaut) -->
                    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('messages.search_in_progress') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('messages.search_description') }}</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center">
                                    <i class="fab fa-amazon text-orange-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.amazon') }}</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-store text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.cultura') }}</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-red-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.fnac') }}</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.ai_analysis_in_progress') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mt-12 grid md:grid-cols-3 gap-8">
                        <!-- Amazon Card -->
                        <div class="group relative overflow-hidden bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-orange-300/30">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/20 to-transparent"></div>
                            <div class="relative p-8 text-center text-white">
                                <div class="mb-6">
                                    <i class="fab fa-amazon text-6xl mb-4 block group-hover:scale-110 transition-transform duration-300"></i>
                                    <div class="w-16 h-1 bg-white/30 mx-auto rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 group-hover:text-orange-100 transition-colors">{{ __('messages.amazon') }}</h3>
                                <p class="text-orange-100 text-sm opacity-90">{{ __('messages.amazon_description') }}</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-300 to-orange-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                        </div>
                        
                        <!-- Cultura Card -->
                        <div class="group relative overflow-hidden bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-blue-300/30">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                            <div class="relative p-8 text-center text-white">
                                <div class="mb-6">
                                    <i class="fas fa-store text-6xl mb-4 block group-hover:scale-110 transition-transform duration-300"></i>
                                    <div class="w-16 h-1 bg-white/30 mx-auto rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 group-hover:text-blue-100 transition-colors">{{ __('messages.cultura') }}</h3>
                                <p class="text-blue-100 text-sm opacity-90">{{ __('messages.cultura_description') }}</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-300 to-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                        </div>
                        
                        <!-- Fnac Card -->
                        <div class="group relative overflow-hidden bg-gradient-to-br from-red-400 via-red-500 to-red-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-red-300/30">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
                            <div class="relative p-8 text-center text-white">
                                <div class="mb-6">
                                    <i class="fas fa-shopping-cart text-6xl mb-4 block group-hover:scale-110 transition-transform duration-300"></i>
                                    <div class="w-16 h-1 bg-white/30 mx-auto rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 group-hover:text-red-100 transition-colors">{{ __('messages.fnac') }}</h3>
                                <p class="text-red-100 text-sm opacity-90">{{ __('messages.fnac_description') }}</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-300 to-red-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                        </div>
                    </div>

                    <!-- Section explicative -->
                    <div class="mt-12 bg-gradient-to-r from-purple-900/20 to-pink-900/20 rounded-2xl p-8 border border-purple-300/20">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-white mb-4">{{ __('messages.comparator_estimation_analysis') }}</h3>
                            <p class="text-purple-200 text-lg leading-relaxed">
                                {{ __('messages.comparator_description') }}
                            </p>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                                <h4 class="text-xl font-semibold text-yellow-300 mb-3">{{ __('messages.price_estimation_title') }}</h4>
                                <p class="text-purple-200 text-sm leading-relaxed">
                                    {{ __('messages.price_estimation_description') }}
                                </p>
                            </div>
                            
                            <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                                <h4 class="text-xl font-semibold text-pink-300 mb-3">{{ __('messages.used_price_comparison_title') }}</h4>
                                <p class="text-purple-200 text-sm leading-relaxed">
                                    {{ __('messages.used_price_comparison_description') }}
                                </p>
                            </div>
                            
                            <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                                <h4 class="text-xl font-semibold text-blue-300 mb-3">{{ __('messages.estimation_history_title') }}</h4>
                                <p class="text-purple-200 text-sm leading-relaxed">
                                    {{ __('messages.estimation_history_description') }}
                                </p>
                            </div>
                            
                            <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                                <h4 class="text-xl font-semibold text-green-300 mb-3">{{ __('messages.lot_estimation_title') }}</h4>
                                <p class="text-purple-200 text-sm leading-relaxed">
                                    {{ __('messages.lot_estimation_description') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <p class="text-purple-200 text-sm">
                                {{ __('messages.seo_footer_text') }}
                            </p>
                        </div>
                    </div>

                    <!-- Modal de confirmation ISBN -->
                    <div id="isbnModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                                <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.confirm_book') }}</h3>
                            </div>
                            
                            <div id="bookInfo" class="mb-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.title') }} :</label>
                                        <p id="bookTitle" class="text-gray-900 font-medium"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.author') }} :</label>
                                        <p id="bookAuthor" class="text-gray-900"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.publisher') }} :</label>
                                        <p id="bookPublisher" class="text-gray-900"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.isbn') }} :</label>
                                        <p id="bookIsbn" class="text-gray-900 font-mono"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        id="confirmIsbn"
                                        class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    {{ __('messages.confirm') }}
                                </button>
                                <button type="button" 
                                        id="cancelIsbn"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('messages.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('searchForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const searchButton = document.getElementById('searchButton');
            const searchIcon = document.getElementById('searchIcon');
            const buttonText = document.getElementById('buttonText');
            const isbnInput = document.getElementById('isbn');
            const isbnStatus = document.getElementById('isbnStatus');
            const isbnModal = document.getElementById('isbnModal');
            const confirmIsbn = document.getElementById('confirmIsbn');
            const cancelIsbn = document.getElementById('cancelIsbn');
            
            // Gestion du formulaire avec vérification au clic
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const isbn = isbnInput.value.trim();
                    
                    if (isbn.length >= 10) {
                        // Vérifier l'ISBN au moment du clic
                        verifyIsbnOnClick(isbn);
                    } else {
                        // Si l'ISBN est trop court, procéder directement
                        submitForm();
                    }
                });
            }
            
            function verifyIsbnOnClick(isbn) {
                // Désactiver le bouton pendant la vérification
                if (searchButton) {
                    searchButton.disabled = true;
                    searchButton.classList.add('opacity-75', 'cursor-not-allowed');
                }
                
                // Changer l'icône et le texte
                if (searchIcon) {
                    searchIcon.className = 'fas fa-spinner fa-spin mr-2';
                }
                if (buttonText) {
                    buttonText.textContent = '{{ __("messages.verification") }}';
                }
                
                fetch('{{ \App\Helpers\LocalizedRoute::localized("price.verify.isbn") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ isbn: isbn })
                })
                .then(response => response.json())
                .then(data => {
                    // Réactiver le bouton
                    if (searchButton) {
                        searchButton.disabled = false;
                        searchButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                    
                    // Restaurer l'icône et le texte
                    if (searchIcon) {
                        searchIcon.className = 'fas fa-search mr-2';
                    }
                    if (buttonText) {
                        buttonText.textContent = 'Comparer les Prix';
                    }
                    
                    if (data.valid) {
                        // Afficher la popup de confirmation
                        showIsbnModal(data);
                    } else {
                        // Afficher l'erreur et arrêter
                        showErrorAndStop(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification:', error);
                    
                    // Réactiver le bouton
                    if (searchButton) {
                        searchButton.disabled = false;
                        searchButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                    
                    // Restaurer l'icône et le texte
                    if (searchIcon) {
                        searchIcon.className = 'fas fa-search mr-2';
                    }
                    if (buttonText) {
                        buttonText.textContent = 'Comparer les Prix';
                    }
                    
                    // En cas d'erreur réseau, permettre de continuer
                    showNetworkErrorAndProceed('Erreur de connexion. Voulez-vous continuer quand même ?');
                });
            }
            
            function showErrorAndStop(message) {
                // Afficher l'erreur et ne pas procéder
                isbnStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>' + message;
                isbnStatus.className = 'mt-2 text-sm text-red-600';
                isbnStatus.classList.remove('hidden');
                
                // Ne pas lancer la recherche automatiquement
                // L'utilisateur devra corriger l'ISBN ou cliquer à nouveau
            }
            
            function showNetworkErrorAndProceed(message) {
                // Afficher l'erreur réseau avec option de continuer
                isbnStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>' + message + 
                    ' <button id="continueButton" class="ml-2 text-blue-600 hover:text-blue-800 underline">Continuer</button>';
                isbnStatus.className = 'mt-2 text-sm text-orange-600';
                isbnStatus.classList.remove('hidden');
                
                // Ajouter l'événement au bouton continuer
                document.getElementById('continueButton').addEventListener('click', function() {
                    isbnStatus.classList.add('hidden');
                    submitForm();
                });
            }
            
            function showIsbnModal(data) {
                document.getElementById('bookTitle').textContent = data.title;
                document.getElementById('bookAuthor').textContent = data.author;
                document.getElementById('bookPublisher').textContent = data.publisher;
                document.getElementById('bookIsbn').textContent = data.isbn;
                
                isbnModal.classList.remove('hidden');
            }
            
            function hideIsbnModal() {
                isbnModal.classList.add('hidden');
            }
            
            function submitForm() {
                console.log('Formulaire soumis - Affichage du loading');
                
                // Désactiver le bouton
                if (searchButton) {
                    searchButton.disabled = true;
                    searchButton.classList.remove('hover:scale-105', 'hover:bg-indigo-700');
                    searchButton.classList.add('opacity-75', 'cursor-not-allowed');
                }
                
                // Changer l'icône
                if (searchIcon) {
                    searchIcon.className = 'fas fa-spinner fa-spin mr-2';
                }
                
                // Changer le texte
                if (buttonText) {
                    buttonText.textContent = 'Recherche en cours...';
                }
                
                // Afficher l'overlay
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                    console.log('Overlay affiché');
                } else {
                    console.log('Overlay non trouvé');
                }
                
                // Soumettre le formulaire après un court délai
                setTimeout(() => {
                    form.submit();
                }, 100);
            }
            
            // Gestion des boutons de la modal
            if (confirmIsbn) {
                confirmIsbn.addEventListener('click', function() {
                    hideIsbnModal();
                    submitForm();
                });
            }
            
            if (cancelIsbn) {
                cancelIsbn.addEventListener('click', function() {
                    hideIsbnModal();
                });
            }
            
            // Fermer la modal en cliquant à l'extérieur
            isbnModal.addEventListener('click', function(e) {
                if (e.target === isbnModal) {
                    hideIsbnModal();
                }
            });
        });
    </script>
</x-app-layout> 