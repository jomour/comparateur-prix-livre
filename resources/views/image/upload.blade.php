<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analyse de lot en image') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <div>
                                    <strong>Succès !</strong> {{ session('success') }}
                                    <div class="text-sm mt-1">
                                        <i class="fas fa-search mr-1"></i>Recherche automatique des ISBN effectuée
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('image'))
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-4">Image analysée :</h3>
                            <img src="{{ route('image.show', session('image')) }}" alt="Image uploadée" class="max-w-full h-auto rounded-lg shadow-md">
                        </div>
                    @endif

                    @if(session('mangas'))
                        <div class="mt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Mangas détectés :</h3>
                                <button onclick="searchAllPrices()" id="searchAllButton" class="bg-gray-400 text-white font-bold py-3 px-6 rounded-lg flex items-center cursor-not-allowed" disabled>
                                    <i class="fas fa-lock mr-2" id="searchAllIcon"></i>
                                    <span id="searchAllText">Validez tous les mangas d'abord</span>
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Titre</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">ISBN</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Prix estimé</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach(session('mangas') as $manga)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $manga['title'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $manga['isbn'] }}
                                                    @if($manga['isDuplicate'])
                                                        <span class="inline-block ml-2 text-red-500 cursor-help" title="Attention : ISBN en doublon sur plusieurs mangas différents">⚠️</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="price-{{ $loop->index }}">
                                                    <span class="text-gray-400">-</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($manga['isbn'] === 'Non trouvé')
                                                        <button onclick="searchIsbn('{{ $manga['title'] }}')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                                            Rechercher
                                                        </button>
                                                    @else
                                                        <button onclick="verifyMangaIsbn('{{ $manga['isbn'] }}', '{{ $manga['title'] }}')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2" title="Vérifier l'ISBN et valider le manga">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Vérifier l'ISBN
                                                        </button>
                                                    @endif
                                                    <button onclick="editIsbn('{{ $manga['title'] }}', '{{ $manga['isbn'] }}')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2" title="Modifier l'ISBN">
                                                        ✏️
                                                    </button>
                                                    <button onclick="removeManga('{{ $manga['title'] }}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" title="Supprimer de la liste">
                                                        🗑️
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Résumé global -->
                            <div id="globalSummary" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                                <h4 class="text-lg font-semibold text-blue-800 mb-3">Estimation globale du lot</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white p-3 rounded-lg border">
                                        <div class="text-sm text-gray-600">Nombre de mangas</div>
                                        <div id="totalMangas" class="text-2xl font-bold text-blue-600">0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border">
                                        <div class="text-sm text-gray-600">Prix total estimé</div>
                                        <div id="totalPrice" class="text-2xl font-bold text-green-600">0,00 €</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border">
                                        <div class="text-sm text-gray-600">Prix moyen</div>
                                        <div id="averagePrice" class="text-2xl font-bold text-purple-600">0,00 €</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('image.upload.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="uploadForm">
                        @csrf
                        
                        <div>
                            <x-input-label for="image" :value="__('Sélectionner une image de lot de mangas')" />
                            <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button id="uploadButton">
                                <i class="fas fa-upload mr-2" id="uploadIcon"></i>
                                <span id="buttonText">{{ __('Analyser l\'image') }}</span>
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Loading Overlay (caché par défaut) -->
                    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Analyse en cours...</h3>
                            <p class="text-gray-600 mb-4">Nous analysons votre image avec l'IA pour détecter les mangas</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-eye text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Détection des mangas</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Analyse IA en cours</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-search text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Recherche des ISBN</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Temps estimé : 30-60 secondes</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium mb-2">Informations :</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Formats acceptés : JPEG, PNG, JPG, GIF</li>
                            <li>• Taille maximale : 2 MB</li>
                            <li>• L'image sera analysée par IA pour détecter les mangas</li>
                            <li>• <strong>Recherche automatique des ISBN</strong> pour chaque manga détecté</li>
                            <li>• Les mangas avec ISBN trouvé peuvent être recherchés directement</li>
                            <li>• Temps d'analyse : 30-60 secondes selon le nombre de mangas</li>
                        </ul>
                    </div>

                    <!-- Modal d'édition ISBN -->
                    <div id="editIsbnModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-edit text-yellow-500 text-2xl mr-3"></i>
                                <h3 class="text-lg font-semibold text-gray-800">Modifier l'ISBN</h3>
                            </div>
                            
                            <div class="mb-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre du manga :</label>
                                    <p id="editMangaTitle" class="text-gray-900 font-medium bg-gray-50 p-2 rounded"></p>
                                </div>
                                <div class="mb-4">
                                    <label for="editIsbnInput" class="block text-sm font-medium text-gray-700 mb-1">ISBN :</label>
                                    <input type="text" 
                                           id="editIsbnInput" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ex: 9782505000000">
                                </div>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        id="saveIsbn"
                                        class="flex-1 bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    Enregistrer
                                </button>
                                <button type="button" 
                                        id="cancelEdit"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de confirmation suppression -->
                    <div id="deleteMangaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                                <h3 class="text-lg font-semibold text-gray-800">Confirmer la suppression</h3>
                            </div>
                            
                            <div class="mb-6">
                                <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer ce manga de la liste ?</p>
                                <p id="deleteMangaTitle" class="text-gray-900 font-medium mt-2 bg-gray-50 p-2 rounded"></p>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        id="confirmDelete"
                                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer
                                </button>
                                <button type="button" 
                                        id="cancelDelete"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Overlay pour la recherche globale -->
                    <div id="searchAllOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-green-600 mx-auto mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Recherche des prix en cours...</h3>
                            <p class="text-gray-600 mb-4">Nous récupérons les prix pour tous vos mangas</p>
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
                                    <i class="fas fa-clock text-green-500 mr-2"></i>
                                    <span id="searchProgress" class="text-sm text-gray-600">Préparation...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal des résultats de recherche -->
                    <div id="searchResultsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-4xl mx-4 w-full max-h-[80vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-chart-bar text-green-500 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Résultats de la recherche</h3>
                                </div>
                                <button onclick="closeSearchResults()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            
                            <div class="mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="bg-blue-50 p-3 rounded-lg border">
                                        <div class="text-sm text-gray-600">Nombre de mangas</div>
                                        <div id="modalTotalMangas" class="text-2xl font-bold text-blue-600">0</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded-lg border">
                                        <div class="text-sm text-gray-600">Prix total estimé</div>
                                        <div id="modalTotalPrice" class="text-2xl font-bold text-green-600">0,00 €</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded-lg border">
                                        <div class="text-sm text-gray-600">Prix moyen</div>
                                        <div id="modalAveragePrice" class="text-2xl font-bold text-purple-600">0,00 €</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Titre</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">ISBN</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Prix trouvé</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody id="searchResultsBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Les résultats seront ajoutés ici dynamiquement -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal des détails du manga -->
                    <div id="mangaDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-[80vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-book text-blue-500 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Détails du manga</h3>
                                </div>
                                <button onclick="closeMangaDetails()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            
                            <div id="mangaDetailsContent" class="mb-6">
                                <!-- Le contenu sera chargé dynamiquement -->
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        id="confirmManga"
                                        class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Confirmer
                                </button>
                                <button type="button" 
                                        onclick="closeMangaDetails()"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Annuler
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
            const form = document.getElementById('uploadForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const uploadButton = document.getElementById('uploadButton');
            const uploadIcon = document.getElementById('uploadIcon');
            const buttonText = document.getElementById('buttonText');
            
            // Gestion du formulaire avec affichage du loading
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Désactiver le bouton
                    if (uploadButton) {
                        uploadButton.disabled = true;
                        uploadButton.classList.remove('hover:scale-105', 'hover:bg-blue-700');
                        uploadButton.classList.add('opacity-75', 'cursor-not-allowed');
                    }
                    
                    // Changer l'icône
                    if (uploadIcon) {
                        uploadIcon.className = 'fas fa-spinner fa-spin mr-2';
                    }
                    
                    // Changer le texte
                    if (buttonText) {
                        buttonText.textContent = 'Analyse en cours...';
                    }
                    
                    // Afficher l'overlay
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                    }
                    
                    // Soumettre le formulaire après un court délai
                    setTimeout(() => {
                        form.submit();
                    }, 100);
                });
            }
        });

        function searchIsbn(title) {
            fetch('{{ route("image.search.isbn") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: title
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    alert(`ISBN trouvé pour "${title}": ${data.isbn}`);
                } else {
                    alert(`Aucun ISBN trouvé pour "${title}"`);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la recherche d\'ISBN');
            });
        }

        function editIsbn(title, currentIsbn) {
            document.getElementById('editMangaTitle').textContent = title;
            document.getElementById('editIsbnInput').value = currentIsbn;
            document.getElementById('editIsbnModal').classList.remove('hidden');
        }

        function removeManga(title) {
            document.getElementById('deleteMangaTitle').textContent = title;
            document.getElementById('deleteMangaModal').classList.remove('hidden');
        }

        // Gestion des modales
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editIsbnModal');
            const deleteModal = document.getElementById('deleteMangaModal');
            
            // Fermer les modales en cliquant à l'extérieur
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) {
                    editModal.classList.add('hidden');
                }
            });
            
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    deleteModal.classList.add('hidden');
                }
            });
            
            // Boutons de fermeture
            document.getElementById('cancelEdit').addEventListener('click', function() {
                editModal.classList.add('hidden');
            });
            
            document.getElementById('cancelDelete').addEventListener('click', function() {
                deleteModal.classList.add('hidden');
            });
            
            // Sauvegarder l'ISBN modifié
            document.getElementById('saveIsbn').addEventListener('click', function() {
                const title = document.getElementById('editMangaTitle').textContent;
                const newIsbn = document.getElementById('editIsbnInput').value.trim();
                
                if (newIsbn) {
                    // Mettre à jour l'ISBN dans le tableau
                    updateMangaInTable(title, newIsbn);
                    editModal.classList.add('hidden');
                } else {
                    alert('Veuillez entrer un ISBN valide');
                }
            });
            
            // Confirmer la suppression
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const title = document.getElementById('deleteMangaTitle').textContent;
                
                // Supprimer le manga du tableau
                removeMangaFromTable(title);
                deleteModal.classList.add('hidden');
            });
        });

        function updateMangaInTable(title, newIsbn) {
            // Trouver la ligne du tableau correspondante
            const rows = document.querySelectorAll('tbody tr');
            let updated = false;
            
            rows.forEach(row => {
                const titleCell = row.querySelector('td:first-child');
                if (titleCell && titleCell.textContent.trim() === title) {
                    const isbnCell = row.querySelector('td:nth-child(2)');
                    if (isbnCell) {
                        // Mettre à jour l'ISBN
                        isbnCell.innerHTML = newIsbn;
                        
                        // Vérifier s'il y a des doublons
                        checkForDuplicates();
                    }
                    updated = true;
                }
            });
            
            if (updated) {
                // Afficher un message de succès temporaire
                showTemporaryMessage('ISBN mis à jour avec succès', 'success');
            }
        }

        function removeMangaFromTable(title) {
            // Trouver et supprimer la ligne du tableau
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const titleCell = row.querySelector('td:first-child');
                if (titleCell && titleCell.textContent.trim() === title) {
                    row.remove();
                    return;
                }
            });
            
            // Vérifier s'il y a des doublons après suppression
            checkForDuplicates();
            
            // Afficher un message de succès temporaire
            showTemporaryMessage('Manga supprimé de la liste', 'success');
        }

        function checkForDuplicates() {
            const isbnCells = document.querySelectorAll('tbody td:nth-child(2)');
            const isbnCounts = {};
            
            // Compter les occurrences de chaque ISBN
            isbnCells.forEach(cell => {
                const isbn = cell.textContent.trim();
                if (isbn && isbn !== 'Non trouvé' && isbn !== 'Erreur de recherche') {
                    if (!isbnCounts[isbn]) {
                        isbnCounts[isbn] = [];
                    }
                    isbnCounts[isbn].push(cell);
                }
            });
            
            // Supprimer tous les indicateurs de doublon existants
            document.querySelectorAll('.duplicate-indicator').forEach(indicator => {
                indicator.remove();
            });
            
            // Ajouter les indicateurs de doublon pour les ISBN qui apparaissent plus d'une fois
            Object.keys(isbnCounts).forEach(isbn => {
                if (isbnCounts[isbn].length > 1) {
                    isbnCounts[isbn].forEach(cell => {
                        const indicator = document.createElement('span');
                        indicator.className = 'inline-block ml-2 text-red-500 cursor-help duplicate-indicator';
                        indicator.title = 'Attention : ISBN en doublon sur plusieurs mangas différents';
                        indicator.textContent = '⚠️';
                        cell.appendChild(indicator);
                    });
                }
            });
        }

        function showTemporaryMessage(message, type = 'info') {
            // Créer un message temporaire
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
            }`;
            messageDiv.textContent = message;
            
            document.body.appendChild(messageDiv);
            
            // Supprimer le message après 3 secondes
            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }

        async function searchAllPrices() {
            // Vérification de sécurité : s'assurer que tous les mangas sont validés
            const rows = document.querySelectorAll('tbody tr');
            let totalMangas = 0;
            let validatedMangas = 0;
            
            rows.forEach(row => {
                const isbnCell = row.querySelector('td:nth-child(2)');
                const isbn = isbnCell.textContent.trim().replace(/⚠️/g, '').trim();
                
                if (isbn && isbn !== 'Non trouvé' && isbn !== 'Erreur de recherche') {
                    totalMangas++;
                    if (row.classList.contains('bg-green-50')) {
                        validatedMangas++;
                    }
                }
            });
            
            if (validatedMangas !== totalMangas) {
                showTemporaryMessage('Tous les mangas doivent être validés avant la recherche globale', 'error');
                return;
            }
            
            const searchAllButton = document.getElementById('searchAllButton');
            const searchAllIcon = document.getElementById('searchAllIcon');
            const searchAllText = document.getElementById('searchAllText');
            const searchAllOverlay = document.getElementById('searchAllOverlay');
            const searchProgress = document.getElementById('searchProgress');
            
            // Désactiver le bouton et afficher la popup
            searchAllButton.disabled = true;
            searchAllButton.classList.add('opacity-75', 'cursor-not-allowed');
            searchAllIcon.className = 'fas fa-spinner fa-spin mr-2';
            searchAllText.textContent = 'Recherche en cours...';
            searchAllOverlay.style.display = 'flex';
            
            // Récupérer tous les mangas validés
            const validMangas = [];
            
            rows.forEach((row, index) => {
                const isbnCell = row.querySelector('td:nth-child(2)');
                const isbn = isbnCell.textContent.trim().replace(/⚠️/g, '').trim();
                
                if (isbn && isbn !== 'Non trouvé' && isbn !== 'Erreur de recherche' && row.classList.contains('bg-green-50')) {
                    validMangas.push({
                        title: row.querySelector('td:first-child').textContent.trim(),
                        isbn: isbn
                    });
                }
            });
            
            if (validMangas.length === 0) {
                showTemporaryMessage('Aucun manga validé trouvé pour la recherche', 'error');
                resetSearchButton();
                searchAllOverlay.style.display = 'none';
                return;
            }
            
            // Mettre à jour le progrès
            searchProgress.textContent = `Envoi de ${validMangas.length} mangas au serveur...`;
            
            try {
                // Envoyer les données au backend
                const response = await fetch('{{ route("image.search.all.prices") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        mangas: validMangas
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        // Rediriger vers la page de résultats
                        window.location.href = data.redirect_url;
                    } else {
                        showTemporaryMessage('Erreur lors de la recherche: ' + (data.error || 'Erreur inconnue'), 'error');
                        resetSearchButton();
                        searchAllOverlay.style.display = 'none';
                    }
                } else {
                    showTemporaryMessage('Erreur lors de la communication avec le serveur', 'error');
                    resetSearchButton();
                    searchAllOverlay.style.display = 'none';
                }
                
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
                showTemporaryMessage('Erreur de connexion lors de la recherche', 'error');
                resetSearchButton();
                searchAllOverlay.style.display = 'none';
            }
        }

        function showSearchResults(results, totalPrice, foundPrices) {
            const modal = document.getElementById('searchResultsModal');
            const tbody = document.getElementById('searchResultsBody');
            const totalMangasEl = document.getElementById('modalTotalMangas');
            const totalPriceEl = document.getElementById('modalTotalPrice');
            const averagePriceEl = document.getElementById('modalAveragePrice');
            
            // Vider le tableau
            tbody.innerHTML = '';
            
            // Ajouter les résultats
            results.forEach(result => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                const priceText = result.price ? `${result.price.toFixed(2)} €` : '-';
                const priceClass = result.price ? 'text-green-600 font-semibold' : 'text-gray-400';
                
                let statusText, statusClass;
                switch(result.status) {
                    case 'success':
                        statusText = 'Prix trouvé';
                        statusClass = 'text-green-600';
                        break;
                    case 'not_found':
                        statusText = 'Non trouvé';
                        statusClass = 'text-red-500';
                        break;
                    case 'error':
                        statusText = 'Erreur';
                        statusClass = 'text-red-500';
                        break;
                }
                
                row.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-900">${result.title}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 font-mono">${result.isbn}</td>
                    <td class="px-4 py-3 text-sm ${priceClass}">${priceText}</td>
                    <td class="px-4 py-3 text-sm ${statusClass}">${statusText}</td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Mettre à jour les statistiques
            totalMangasEl.textContent = results.length;
            totalPriceEl.textContent = `${totalPrice.toFixed(2)} €`;
            
            const averagePrice = foundPrices > 0 ? totalPrice / foundPrices : 0;
            averagePriceEl.textContent = `${averagePrice.toFixed(2)} €`;
            
            // Afficher la modal
            modal.classList.remove('hidden');
        }

        function closeSearchResults() {
            const modal = document.getElementById('searchResultsModal');
            modal.classList.add('hidden');
        }

        function resetSearchButton() {
            const searchAllButton = document.getElementById('searchAllButton');
            const searchAllIcon = document.getElementById('searchAllIcon');
            const searchAllText = document.getElementById('searchAllText');
            
            searchAllButton.disabled = false;
            searchAllButton.classList.remove('opacity-75', 'cursor-not-allowed');
            searchAllIcon.className = 'fas fa-search mr-2';
            searchAllText.textContent = 'Rechercher tous les prix';
        }

        async function searchPriceForIsbn(isbn) {
            try {
                const response = await fetch('{{ route("image.search.price") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        isbn: isbn
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.price) {
                        return data.price;
                    }
                }
                
                return null;
            } catch (error) {
                console.error('Erreur lors de la recherche de prix:', error);
                return null;
            }
        }

        function showMangaDetails(isbn, title) {
            // Vérifier l'ISBN d'abord comme dans la recherche simple
            verifyMangaIsbn(isbn, title);
        }

        async function verifyMangaIsbn(isbn, title) {
            try {
                const response = await fetch('{{ route("price.verify.isbn") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        isbn: isbn
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.valid) {
                        // Afficher la modal de confirmation avec les détails du livre
                        showMangaConfirmationModal(data);
                    } else {
                        // Afficher l'erreur
                        showTemporaryMessage(`Erreur: ${data.message}`, 'error');
                    }
                } else {
                    showTemporaryMessage('Erreur lors de la vérification de l\'ISBN', 'error');
                }
            } catch (error) {
                console.error('Erreur lors de la vérification:', error);
                showTemporaryMessage('Erreur de connexion lors de la vérification', 'error');
            }
        }

        function showMangaConfirmationModal(data) {
            // Remplir le contenu de la modal avec les détails du livre
            document.getElementById('mangaDetailsContent').innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">Informations du livre</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Titre</label>
                                <p class="text-gray-900 font-medium">${data.title}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Auteur</label>
                                <p class="text-gray-900">${data.author || 'Auteur inconnu'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Éditeur</label>
                                <p class="text-gray-900">${data.publisher || 'Éditeur inconnu'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ISBN</label>
                                <p class="text-gray-900 font-mono">${data.isbn}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Afficher la modal
            document.getElementById('mangaDetailsModal').classList.remove('hidden');
        }

        function closeMangaDetails() {
            // Masquer la modal
            document.getElementById('mangaDetailsModal').classList.add('hidden');
        }

        async function loadMangaPrices(isbn) {
            try {
                const response = await fetch('{{ route("image.search.price") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        isbn: isbn
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    displayMangaPrices(data);
                } else {
                    displayMangaPrices({ success: false, error: 'Erreur lors du chargement' });
                }
            } catch (error) {
                console.error('Erreur lors du chargement des prix:', error);
                displayMangaPrices({ success: false, error: 'Erreur de connexion' });
            }
        }

        function displayMangaPrices(data) {
            const pricesContainer = document.getElementById('mangaPrices');
            
            if (data.success && data.prices) {
                let pricesHtml = '';
                let hasValidPrice = false;
                
                Object.entries(data.prices).forEach(([site, price]) => {
                    if (price !== null) {
                        hasValidPrice = true;
                        pricesHtml += `
                            <div class="flex justify-between items-center p-2 bg-white rounded border">
                                <span class="font-medium text-gray-700">${site}</span>
                                <span class="font-bold text-green-600">${price.toFixed(2)} €</span>
                            </div>
                        `;
                    }
                });
                
                if (data.occasion_price) {
                    hasValidPrice = true;
                    pricesHtml += `
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded border border-orange-200">
                            <span class="font-medium text-gray-700">Occasion</span>
                            <span class="font-bold text-orange-600">${data.occasion_price.toFixed(2)} €</span>
                        </div>
                    `;
                }
                
                if (!hasValidPrice) {
                    pricesHtml = `
                        <div class="text-center text-gray-500 py-4">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mb-2"></i>
                            <p>Aucun prix trouvé pour ce manga</p>
                        </div>
                    `;
                }
                
                pricesContainer.innerHTML = pricesHtml;
            } else {
                pricesContainer.innerHTML = `
                    <div class="text-center text-red-500 py-4">
                        <i class="fas fa-times-circle text-xl mb-2"></i>
                        <p>Erreur lors du chargement des prix</p>
                    </div>
                `;
            }
        }

        // Gestion de la validation du manga
        document.addEventListener('DOMContentLoaded', function() {
            const confirmButton = document.getElementById('confirmManga');
            const mangaDetailsModal = document.getElementById('mangaDetailsModal');
            
            // Fermer la modal en cliquant à l'extérieur
            mangaDetailsModal.addEventListener('click', function(e) {
                if (e.target === mangaDetailsModal) {
                    closeMangaDetails();
                }
            });
            
            // Gérer la confirmation
            confirmButton.addEventListener('click', function() {
                const titleElement = document.querySelector('#mangaDetailsContent .text-gray-900.font-medium');
                const isbnElement = document.querySelector('#mangaDetailsContent .text-gray-900.font-mono');
                
                if (titleElement && isbnElement) {
                    const title = titleElement.textContent;
                    const isbn = isbnElement.textContent;
                    
                    // Marquer le manga comme validé dans le tableau
                    markMangaAsValidated(title, isbn);
                    
                    // Fermer la modal
                    closeMangaDetails();
                    
                    // Afficher un message de succès
                    showTemporaryMessage('Manga validé avec succès !', 'success');
                }
            });
        });

        function markMangaAsValidated(title, isbn) {
            console.log('Tentative de validation pour:', title, isbn);
            
            // Trouver la ligne du tableau correspondante
            const rows = document.querySelectorAll('tbody tr');
            let found = false;
            
            // Vérifier s'il y a des lignes (pas encore de résultats d'analyse)
            if (rows.length === 0) {
                console.log('Aucune ligne trouvée dans le tableau, pas encore de résultats d\'analyse');
                return;
            }
            
            rows.forEach((row, index) => {
                const isbnCell = row.querySelector('td:nth-child(2)');
                
                if (isbnCell) {
                    // Nettoyer l'ISBN de la ligne (enlever les indicateurs de doublon)
                    const rowIsbn = isbnCell.textContent.trim().replace(/⚠️/g, '').trim();
                    
                    console.log(`Ligne ${index}: ISBN: "${rowIsbn}" vs "${isbn}"`);
                    
                    // Vérifier si c'est la bonne ligne (par ISBN seulement)
                    if (rowIsbn === isbn) {
                        console.log('Ligne trouvée par ISBN, validation en cours...');
                        found = true;
                        
                        // Ajouter une classe pour indiquer que le manga est validé
                        row.classList.add('bg-green-50', 'border-l-4', 'border-green-500');
                        
                        // Ajouter un indicateur visuel
                        const actionsCell = row.querySelector('td:last-child');
                        if (actionsCell) {
                            // Vérifier si l'indicateur existe déjà
                            if (!actionsCell.querySelector('.validation-indicator')) {
                                const indicator = document.createElement('span');
                                indicator.className = 'inline-block ml-2 text-green-500 validation-indicator';
                                indicator.title = 'Manga validé';
                                indicator.innerHTML = '<i class="fas fa-check-circle"></i>';
                                actionsCell.appendChild(indicator);
                            }
                        }
                        
                        // Désactiver le bouton "Vérifier l'ISBN" et le remplacer par un indicateur
                        const verifyButton = row.querySelector('button[onclick*="verifyMangaIsbn"]');
                        if (verifyButton) {
                            verifyButton.disabled = true;
                            verifyButton.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                            verifyButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                            verifyButton.innerHTML = '<i class="fas fa-check mr-1"></i>Validé';
                            verifyButton.onclick = null;
                        }
                        
                        // Supprimer les boutons Éditer et Supprimer
                        const editButton = row.querySelector('button[onclick*="editIsbn"]');
                        if (editButton) {
                            editButton.remove();
                        }
                        
                        const deleteButton = row.querySelector('button[onclick*="removeManga"]');
                        if (deleteButton) {
                            deleteButton.remove();
                        }
                    }
                }
            });
            
            if (!found) {
                console.error('Aucune ligne trouvée pour la validation avec ISBN:', isbn);
            } else {
                console.log('Validation terminée, vérification du compteur...');
                // Vérifier si tous les mangas sont maintenant validés
                checkAllMangasValidated();
            }
        }

        function checkAllMangasValidated() {
            console.log('Vérification du statut de validation...');
            const rows = document.querySelectorAll('tbody tr');
            const searchAllButton = document.getElementById('searchAllButton');
            const searchAllIcon = document.getElementById('searchAllIcon');
            const searchAllText = document.getElementById('searchAllText');
            
            // Vérifier si les éléments existent (pas encore de résultats d'analyse)
            if (!searchAllButton || !searchAllIcon || !searchAllText) {
                console.log('Éléments de recherche non trouvés, pas encore de résultats d\'analyse');
                return;
            }
            
            let totalMangas = 0;
            let validatedMangas = 0;
            
            rows.forEach((row, index) => {
                const isbnCell = row.querySelector('td:nth-child(2)');
                const isbn = isbnCell.textContent.trim().replace(/⚠️/g, '').trim();
                
                // Compter seulement les mangas avec ISBN valide (pas "Non trouvé")
                if (isbn && isbn !== 'Non trouvé' && isbn !== 'Erreur de recherche') {
                    totalMangas++;
                    
                    // Vérifier si le manga est validé (a la classe bg-green-50)
                    if (row.classList.contains('bg-green-50')) {
                        validatedMangas++;
                        console.log(`Manga ${index} validé: ${row.querySelector('td:first-child').textContent.trim()}`);
                    } else {
                        console.log(`Manga ${index} non validé: ${row.querySelector('td:first-child').textContent.trim()}`);
                    }
                }
            });
            
            console.log(`Total: ${totalMangas}, Validés: ${validatedMangas}`);
            
            // Si tous les mangas sont validés, activer le bouton
            if (totalMangas > 0 && validatedMangas === totalMangas) {
                console.log('Tous les mangas sont validés, activation du bouton...');
                searchAllButton.disabled = false;
                searchAllButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                searchAllButton.classList.add('bg-green-600', 'hover:bg-green-700');
                searchAllIcon.className = 'fas fa-search mr-2';
                searchAllText.textContent = 'Rechercher tous les prix';
                
                // Afficher un message de succès
                showTemporaryMessage('Tous les mangas sont validés ! Recherche globale disponible.', 'success');
            } else {
                // Garder le bouton désactivé
                searchAllButton.disabled = true;
                searchAllButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                searchAllButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                searchAllIcon.className = 'fas fa-lock mr-2';
                searchAllText.textContent = `Validez tous les mangas d'abord (${validatedMangas}/${totalMangas})`;
            }
        }

        // Vérifier l'état initial au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier l'état des mangas au chargement
            setTimeout(() => {
                checkAllMangasValidated();
            }, 100);
        });
    </script>
</x-app-layout> 