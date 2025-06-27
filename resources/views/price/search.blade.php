<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comparateur de Prix Manga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">
                            <i class="fas fa-search text-indigo-600 mr-3"></i>
                            Comparateur de Prix Manga
                        </h1>
                        <p class="text-gray-600">Trouvez le meilleur prix pour vos mangas préférés</p>
                    </div>

                    <!-- Search Form -->
                    <div class="max-w-2xl mx-auto">
                        <form method="POST" action="{{ route('price.search.submit') }}" class="space-y-6" id="searchForm">
                            @csrf
                            
                            <div>
                                <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-barcode mr-2"></i>
                                    ISBN du Manga
                                </label>
                                <input type="text" 
                                       name="isbn" 
                                       id="isbn" 
                                       value="{{ request('isbn') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                       placeholder="Ex: 9782505000000"
                                       required>
                                <p class="mt-2 text-sm text-gray-500">
                                    Entrez l'ISBN à 10 ou 13 chiffres du manga
                                </p>
                            </div>

                            <div class="text-center">
                                <button type="submit" 
                                        id="searchButton"
                                        class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition-all transform hover:scale-105">
                                    <i class="fas fa-search mr-2" id="searchIcon"></i>
                                    <span id="buttonText">Comparer les Prix</span>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Info sur le temps d'attente -->
                        <div class="mt-4 text-center">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center justify-center text-blue-700">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span class="text-sm font-medium">Temps d'attente estimé : 30-60 secondes</span>
                                </div>
                                <p class="text-xs text-blue-600 mt-1">
                                    Nous récupérons les prix depuis 3 sites + estimation IA
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Overlay (caché par défaut) -->
                    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Recherche en cours...</h3>
                            <p class="text-gray-600 mb-4">Nous récupérons les prix depuis Amazon, Cultura et Fnac</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center">
                                    <i class="fab fa-amazon text-orange-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Amazon</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-store text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Cultura</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-red-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Fnac</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Estimation IA en cours...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mt-12 grid md:grid-cols-3 gap-8">
                        <!-- Amazon Card -->
                        <div class="group relative overflow-hidden bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/20 to-transparent"></div>
                            <div class="relative p-8 text-center text-white">
                                <div class="mb-6">
                                    <i class="fab fa-amazon text-6xl mb-4 block group-hover:scale-110 transition-transform duration-300"></i>
                                    <div class="w-16 h-1 bg-white/30 mx-auto rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 group-hover:text-orange-100 transition-colors">Amazon</h3>
                                <p class="text-orange-100 text-sm opacity-90">Leader du e-commerce</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-300 to-orange-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                        </div>
                        
                        <!-- Cultura Card -->
                        <div class="group relative overflow-hidden bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                            <div class="relative p-8 text-center text-white">
                                <div class="mb-6">
                                    <i class="fas fa-store text-6xl mb-4 block group-hover:scale-110 transition-transform duration-300"></i>
                                    <div class="w-16 h-1 bg-white/30 mx-auto rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 group-hover:text-blue-100 transition-colors">Cultura</h3>
                                <p class="text-blue-100 text-sm opacity-90">Culture & Loisirs</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-300 to-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                        </div>
                        
                        <!-- Fnac Card -->
                        <div class="group relative overflow-hidden bg-gradient-to-br from-red-400 via-red-500 to-red-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
                            <div class="relative p-8 text-center text-white">
                                <div class="mb-6">
                                    <i class="fas fa-shopping-cart text-6xl mb-4 block group-hover:scale-110 transition-transform duration-300"></i>
                                    <div class="w-16 h-1 bg-white/30 mx-auto rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 group-hover:text-red-100 transition-colors">Fnac</h3>
                                <p class="text-red-100 text-sm opacity-90">Multimédia & Culture</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-300 to-red-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
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
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
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
                });
            } else {
                console.log('Formulaire non trouvé');
            }
        });
    </script>
</x-app-layout> 