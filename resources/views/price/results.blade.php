<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats - Comparateur de Prix Manga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-line text-indigo-600 mr-3"></i>
                {{ __('messages.search_results') }}
            </h1>
            <div class="bg-white rounded-lg shadow-md p-4 inline-block">
                <p class="text-gray-600">
                    <strong>{{ __('messages.isbn') }}:</strong> {{ $isbn }} | 
                    <strong>{{ __('messages.title') }}:</strong> {{ $title }}
                    @if(isset($historique_id))
                        | <strong>{{ __('messages.search_id') }}:</strong> #{{ $historique_id }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Price Comparison -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                <i class="fas fa-euro-sign mr-2"></i>
                {{ __('messages.price_comparison') }}
            </h2>
            
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Amazon Price -->
                <div class="text-center">
                    <div class="bg-orange-100 rounded-lg p-6 border-2 border-orange-200">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fab fa-amazon text-3xl text-orange-600 mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-800">{{ __('messages.amazon') }}</h3>
                        </div>
                        <div class="text-4xl font-bold {{ $prices['amazon'] !== 'Prix non trouvé' ? 'text-green-600' : 'text-red-500' }} mb-2">
                            {{ $prices['amazon'] }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ __('messages.isbn_direct_search') }}
                        </div>
                    </div>
                </div>

                <!-- Cultura Price -->
                <div class="text-center">
                    <div class="bg-blue-100 rounded-lg p-6 border-2 border-blue-200">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-store text-3xl text-blue-600 mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-800">{{ __('messages.cultura') }}</h3>
                        </div>
                        <div class="text-4xl font-bold {{ $prices['cultura'] !== 'Prix non trouvé' ? 'text-green-600' : 'text-red-500' }} mb-2">
                            {{ $prices['cultura'] }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ __('messages.isbn_search') }}
                        </div>
                    </div>
                </div>

                <!-- Fnac Price -->
                <div class="text-center">
                    <div class="bg-red-100 rounded-lg p-6 border-2 border-red-200">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-shopping-cart text-3xl text-red-600 mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-800">{{ __('messages.fnac') }}</h3>
                        </div>
                        <div class="text-4xl font-bold {{ $prices['fnac'] !== 'Prix non trouvé' ? 'text-green-600' : 'text-red-500' }} mb-2">
                            {{ $prices['fnac'] }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ __('messages.title_search') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Best Price Indicator -->
            @php
                $validPrices = [];
                $bestProvider = null;
                $bestPrice = null;
                
                // Filtrer les prix valides et calculer le meilleur
                foreach ($prices as $provider => $price) {
                    if ($price !== 'Prix non trouvé') {
                        $numericPrice = (float) str_replace(['€', ' ', ','], ['', '', '.'], $price);
                        if ($numericPrice > 0) {
                            $validPrices[$provider] = $numericPrice;
                        }
                    }
                }
                
                if (!empty($validPrices)) {
                    $bestPrice = min($validPrices);
                    $bestProvider = array_search($bestPrice, $validPrices);
                }
            @endphp

            @if($bestProvider !== null)
                <div class="mt-8 bg-gradient-to-r from-green-400 to-green-600 rounded-lg p-6 text-white text-center">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-trophy text-3xl mr-3"></i>
                        <div>
                            <h3 class="text-xl font-bold">{{ __('messages.best_price_section') }}</h3>
                            <p class="text-2xl font-bold">{{ $prices[$bestProvider] }} sur {{ ucfirst($bestProvider) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Estimation Prix d'Occasion -->
        @if(isset($occasion_price) && $occasion_price !== 'Estimation non disponible')
            <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                    <i class="fas fa-handshake text-purple-600 mr-2"></i>
                    {{ __('messages.used_estimation_section') }}
                </h2>
                
                <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg p-8 border-2 border-purple-300">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-robot text-4xl text-purple-600 mr-4"></i>
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-800">{{ __('messages.ai_estimation') }}</h3>
                                <p class="text-sm text-gray-600">{{ __('messages.estimated_price_good_condition') }}</p>
                            </div>
                        </div>
                        <div class="text-5xl font-bold text-purple-700 mb-4">
                            {{ $occasion_price }}
                        </div>
                        <div class="text-sm text-gray-600 bg-white rounded-lg p-3 inline-block">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ __('messages.estimation_based_on_isbn') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Results Grid -->
        <div class="grid lg:grid-cols-3 gap-6 mb-8">
            <!-- Amazon -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-orange-500 text-white p-4">
                    <div class="flex items-center">
                        <i class="fab fa-amazon text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">{{ __('messages.amazon') }}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">{{ __('messages.isbn_direct_search') }}</p>
                    <div class="space-y-2">
                                                    <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.amazon', $historique_id ?? null) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            {{ __('messages.view_result') }}
                        </a>
                        <p class="text-sm text-gray-500">URL: amazon.fr/dp/{{ $isbn }}</p>
                    </div>
                </div>
            </div>

            <!-- Cultura -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-blue-500 text-white p-4">
                    <div class="flex items-center">
                        <i class="fas fa-store text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">{{ __('messages.cultura') }}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">{{ __('messages.isbn_search') }}</p>
                    <div class="space-y-2">
                                                    <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.cultura', $historique_id ?? null) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            {{ __('messages.view_result') }}
                        </a>
                        <p class="text-sm text-gray-500">Recherche: {{ $isbn }}</p>
                    </div>
                </div>
            </div>

            <!-- Fnac -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-red-500 text-white p-4">
                    <div class="flex items-center">
                        <i class="fas fa-shopping-cart text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">{{ __('messages.fnac') }}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">{{ __('messages.title_search') }}</p>
                    <div class="space-y-2">
                                                    <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.fnac', $historique_id ?? null) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            {{ __('messages.view_result') }}
                        </a>
                        <p class="text-sm text-gray-500">Recherche: {{ $title }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-x-4">
                                    <a href="{{ \App\Helpers\LocalizedRoute::localized('price.search') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                {{ __('messages.new_search_button') }}
            </a>
            
            <a href="/" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-home mr-2"></i>
                {{ __('messages.home') }}
            </a>
        </div>
    </div>
</body>
</html> 