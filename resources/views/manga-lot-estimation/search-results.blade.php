<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.global_search_results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <div class="mb-6">
                <x-breadcrumbs page="search_results" />
            </div>

            <div class="bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-yellow-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-6 text-white">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                            <i class="fas fa-chart-bar text-yellow-400 mr-3"></i>
                            {{ __('messages.global_search_results') }}
                        </h1>
                        <p class="text-purple-200">{{ __('messages.prices_found_for_mangas_count', ['count' => count($mangas)]) }}</p>
                    </div>

                    <!-- Statistiques globales -->
                    @php
                        $totalMinValue = 0;
                        $totalAverageValue = 0;
                        $successCount = 0;
                        $errorCount = 0;
                        $validMangas = [];
                        
                        foreach($estimationResults as $result) {
                            if (isset($result['prices']) && is_array($result['prices'])) {
                                $prices = $result['prices'];
                                $mangaMinPrice = null;
                                $mangaLowestAverage = null;
                                
                                // Parcourir tous les providers (amazon, cultura, fnac)
                                foreach($prices as $providerName => $providerData) {
                                    if (is_array($providerData) && isset($providerData['min']) && $providerData['min'] > 0) {
                                        $price = (float)$providerData['min'];
                                        $average = (float)$providerData['average'];
                                        
                                        if ($mangaMinPrice === null || $price < $mangaMinPrice) {
                                            $mangaMinPrice = $price;
                                        }
                                        
                                        if ($mangaLowestAverage === null || $average < $mangaLowestAverage) {
                                            $mangaLowestAverage = $average;
                                        }
                                    }
                                }
                                
                                if ($mangaMinPrice !== null && $mangaLowestAverage !== null) {
                                    $totalMinValue += $mangaMinPrice;
                                    $totalAverageValue += $mangaLowestAverage;
                                    $validMangas[] = ['min' => $mangaMinPrice, 'lowest_average' => $mangaLowestAverage];
                                    $successCount++;
                                } else {
                                    $errorCount++;
                                }
                            } else {
                                $errorCount++;
                            }
                        }
                        
                        // Calculer la somme des prix moyens les plus bas
                        $maxValue = 0;
                        if (count($validMangas) > 0) {
                            $maxValue = $totalAverageValue;
                        }
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <!-- Nombre de mangas -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-xl p-6 border border-blue-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-blue-600/30 to-cyan-600/30 rounded-full p-3 mr-4 border border-blue-500/30">
                                        <i class="fas fa-books text-blue-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-blue-200">{{ __('messages.manga_count_simple') }}</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">{{ count($mangas) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Valeur totale estimée -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-xl p-6 border border-green-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-green-600/30 to-emerald-600/30 rounded-full p-3 mr-4 border border-green-500/30">
                                        <i class="fas fa-euro-sign text-green-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-green-200">Valeur totale estimée</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                                            {{ number_format($totalMinValue, 2) }} - {{ number_format($maxValue, 2) }} €
                                        </div>
                                        <div class="text-xs text-green-200 mt-1">Min - Max</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Succès -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-emerald-500/20 to-green-600/20 rounded-xl p-6 border border-emerald-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-emerald-600/30 to-green-600/30 rounded-full p-3 mr-4 border border-emerald-500/30">
                                        <i class="fas fa-check text-emerald-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-emerald-200">Analyses réussies</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-green-400 bg-clip-text text-transparent">{{ $successCount }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Erreurs -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-red-500/20 to-pink-600/20 rounded-xl p-6 border border-red-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-red-600/30 to-pink-600/30 rounded-full p-3 mr-4 border border-red-500/30">
                                        <i class="fas fa-exclamation-triangle text-red-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-red-200">Analyses échouées</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-red-400 to-pink-400 bg-clip-text text-transparent">{{ $errorCount }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des mangas avec estimations -->
                    <div class="space-y-4">
                        @if(empty($mangas))
                            <div class="bg-red-600/20 border border-red-500/30 rounded-lg p-4">
                                <p class="text-red-300">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Aucun manga trouvé dans la session.
                                </p>
                            </div>
                        @else
                            @foreach($mangas as $index => $manga)
                            @php
                                $result = $estimationResults[$index] ?? null;
                                $hasError = $result && isset($result['error']);
                                $hasRarity = $result && isset($result['rarity']) && !$hasError;
                                $hasPopularity = $result && isset($result['popularity']) && isset($result['popularity']['success']) && $result['popularity']['success'];
                                $rarityScore = $hasRarity ? $result['rarity']['score'] : 0;
                                $valueEstimation = $hasRarity ? $result['rarity']['value_estimation'] : null;
                                $explanation = $hasRarity ? $result['rarity']['explanation'] : '';
                                $popularity = $hasPopularity ? $result['popularity'] : null;
                            @endphp

                            <div class="bg-gradient-to-br from-purple-800/30 to-pink-800/30 rounded-xl border border-purple-600/30 backdrop-blur-sm hover:border-purple-500/50 transition-all duration-300">
                                <div class="p-6">
                                    <!-- En-tête du manga -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-white mb-2">
                                                <i class="fas fa-book text-purple-300 mr-2"></i>
                                                {{ $manga['title'] }}
                                            </h3>
                                            @if(!empty($manga['isbn']))
                                                <p class="text-sm text-purple-200 font-mono">
                                                    <i class="fas fa-barcode text-purple-300 mr-1"></i>
                                                    {{ $manga['isbn'] }}
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <!-- Statut -->
                                        <div class="text-right">
                                            @if($hasError)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-600/20 text-red-300 border border-red-500/30">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Erreur
                                                </span>
                                            @elseif($hasRarity)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-300 border border-green-500/30">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Analysé
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-600/20 text-yellow-300 border border-yellow-500/30">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    En attente
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($hasError)
                                        <!-- Affichage de l'erreur -->
                                        <div class="bg-red-600/20 border border-red-500/30 rounded-lg p-4">
                                            <p class="text-red-300">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                {{ $result['error'] }}
                                            </p>
                                        </div>
                                    @elseif($hasRarity)
                                        <!-- Résultats d'estimation -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <!-- Score de rareté -->
                                            <div class="bg-gradient-to-br from-purple-500/20 to-pink-600/20 rounded-lg p-4 border border-purple-400/30">
                                                <div class="text-center">
                                                    <div class="text-sm text-purple-200 mb-1">Score de rareté</div>
                                                    <div class="text-2xl font-bold text-purple-300">{{ $rarityScore }}/10</div>
                                                    <div class="text-xs text-purple-200 mt-1">
                                                        @if($rarityScore >= 8) Très rare
                                                        @elseif($rarityScore >= 6) Rare
                                                        @elseif($rarityScore >= 4) Peu commun
                                                        @else Commun
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                                                                    <!-- Estimation de valeur -->
                                        @if($valueEstimation)
                                            <div class="bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-lg p-4 border border-green-400/30">
                                                <div class="text-center">
                                                    <div class="text-sm text-green-200 mb-1">Valeur estimée</div>
                                                    <div class="text-lg font-bold text-green-300">{{ $valueEstimation['bon'] ?? 'N/A' }}</div>
                                                    <div class="text-xs text-green-200 mt-1">État bon</div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Popularité AniList -->
                                        @if($hasPopularity)
                                            <div class="bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-lg p-4 border border-blue-400/30">
                                                <div class="text-center">
                                                    <div class="text-sm text-blue-200 mb-1">Popularité AniList</div>
                                                    <div class="text-lg font-bold text-blue-300">{{ $popularity['popularity_score'] ?? 'N/A' }}/100</div>
                                                    <div class="text-xs text-blue-200 mt-1">{{ $popularity['popularity_level'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        @endif

                                            <!-- Actions -->
                                            <div class="bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-lg p-4 border border-blue-400/30">
                                                <div class="text-center">
                                                    @if(isset($historiqueIds[$index]))
                                                        <!-- Debug: {{ $historiqueIds[$index] }} -->
                                                        <a href="{{ \App\Helpers\LocalizedRoute::localized('historique.show', $historiqueIds[$index]) }}" 
                                                           target="_blank"
                                                           class="inline-block bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 px-4 py-2 rounded-lg border border-blue-500/30 transition-all duration-300">
                                                            <i class="fas fa-external-link-alt mr-1"></i>
                                                            Voir détails
                                                        </a>
                                                    @else
                                                        <!-- Debug: Pas d'historique ID pour index {{ $index }} -->
                                                        <button onclick="toggleDetails({{ $index }})" class="bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 px-4 py-2 rounded-lg border border-blue-500/30 transition-all duration-300">
                                                            <i class="fas fa-eye mr-1"></i>
                                                            Voir détails
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if(!isset($historiqueIds[$index]))
                                        <!-- Détails (cachés par défaut) - seulement si pas d'historique -->
                                        <div id="details-{{ $index }}" class="hidden bg-purple-800/20 border border-purple-600/30 rounded-lg p-4 mt-4">
                                            <div class="space-y-4">
                                                <!-- Explication -->
                                                @if(!empty($explanation))
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-purple-200 mb-2">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            Analyse de rareté
                                                        </h4>
                                                        <p class="text-sm text-purple-300 leading-relaxed">{{ $explanation }}</p>
                                                    </div>
                                                @endif

                                                <!-- Facteurs de rareté -->
                                                @if(isset($result['rarity']['factors']) && !empty($result['rarity']['factors']))
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-purple-200 mb-2">
                                                            <i class="fas fa-list mr-1"></i>
                                                            Facteurs de rareté
                                                        </h4>
                                                        <ul class="text-sm text-purple-300 space-y-1">
                                                            @foreach($result['rarity']['factors'] as $factor)
                                                                <li class="flex items-center">
                                                                    <i class="fas fa-chevron-right text-purple-400 mr-2 text-xs"></i>
                                                                    {{ $factor }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                                <!-- Estimations détaillées -->
                                                @if($valueEstimation)
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-purple-200 mb-2">
                                                            <i class="fas fa-euro-sign mr-1"></i>
                                                            Estimations par état
                                                        </h4>
                                                        <div class="grid grid-cols-3 gap-2 text-sm">
                                                            <div class="text-center p-2 bg-orange-600/20 rounded border border-orange-500/30">
                                                                <div class="text-orange-200">Correct</div>
                                                                <div class="font-semibold text-orange-300">{{ $valueEstimation['correct'] ?? 'N/A' }}</div>
                                                            </div>
                                                            <div class="text-center p-2 bg-green-600/20 rounded border border-green-500/30">
                                                                <div class="text-green-200">Bon</div>
                                                                <div class="font-semibold text-green-300">{{ $valueEstimation['bon'] ?? 'N/A' }}</div>
                                                            </div>
                                                            <div class="text-center p-2 bg-blue-600/20 rounded border border-blue-500/30">
                                                                <div class="text-blue-200">Excellent</div>
                                                                <div class="font-semibold text-blue-300">{{ $valueEstimation['excellent'] ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Détails de popularité AniList -->
                                                @if($hasPopularity)
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-purple-200 mb-2">
                                                            <i class="fas fa-chart-line mr-1"></i>
                                                            Détails de popularité AniList
                                                        </h4>
                                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                                            <div class="text-center p-2 bg-blue-600/20 rounded border border-blue-500/30">
                                                                <div class="text-blue-200">Score de popularité</div>
                                                                <div class="font-semibold text-blue-300">{{ $popularity['popularity_score'] ?? 'N/A' }}/100</div>
                                                            </div>
                                                            <div class="text-center p-2 bg-blue-600/20 rounded border border-blue-500/30">
                                                                <div class="text-blue-200">Note moyenne</div>
                                                                <div class="font-semibold text-blue-300">{{ $popularity['rating'] ?? 'N/A' }}/100</div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 text-center p-2 bg-blue-600/20 rounded border border-blue-500/30">
                                                            <div class="text-blue-200">Statut</div>
                                                            <div class="font-semibold text-blue-300">
                                                                @switch($popularity['status'] ?? 'UNKNOWN')
                                                                    @case('FINISHED')
                                                                        <span class="text-green-400">Terminé</span>
                                                                        @break
                                                                    @case('RELEASING')
                                                                        <span class="text-blue-400">En cours</span>
                                                                        @break
                                                                    @case('NOT_YET_RELEASED')
                                                                        <span class="text-yellow-400">À venir</span>
                                                                        @break
                                                                    @case('CANCELLED')
                                                                        <span class="text-red-400">Annulé</span>
                                                                        @break
                                                                    @case('HIATUS')
                                                                        <span class="text-orange-400">En pause</span>
                                                                        @break
                                                                    @default
                                                                        <span class="text-gray-400">{{ $popularity['status'] ?? 'Inconnu' }}</span>
                                                                @endswitch
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-center space-x-6">
                        <a href="{{ \App\Helpers\LocalizedRoute::url('manga.lot.estimation.upload.form') }}" 
                           class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-300 transform hover:scale-105 border border-blue-500/30 shadow-lg">
                            <div class="bg-blue-600/30 rounded-full p-2 mr-3 border border-blue-500/30 group-hover:scale-110 transition-transform">
                                <i class="fas fa-arrow-left text-blue-200"></i>
                            </div>
                            <span class="font-medium">{{ __('messages.new_analysis') }}</span>
                        </a>
                        
                        <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
                           class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 border border-green-500/30 shadow-lg">
                            <div class="bg-green-600/30 rounded-full p-2 mr-3 border border-green-500/30 group-hover:scale-110 transition-transform">
                                <i class="fas fa-search text-green-200"></i>
                            </div>
                            <span class="font-medium">{{ __('messages.simple_search') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDetails(index) {
            const detailsElement = document.getElementById('details-' + index);
            if (detailsElement) {
                detailsElement.classList.toggle('hidden');
            }
        }
    </script>
</x-app-layout> 