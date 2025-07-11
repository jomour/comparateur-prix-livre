<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-chart-line text-yellow-400 mr-3"></i>
            {{ __('messages.search_results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs page="results" />
            </div>

            <!-- Search Info Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-6 mb-8">
                <div class="text-center text-white">
                    <div class="flex items-center justify-center mb-4">
                        <i class="fas fa-book text-3xl text-yellow-400 mr-3"></i>
                        <h3 class="text-2xl font-bold">{{ __('messages.search_info') }}</h3>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div class="bg-white/5 rounded-lg p-3">
                            <span class="font-semibold text-yellow-300">{{ __('messages.isbn') }}:</span> 
                            <span class="font-mono">{{ $isbn }}</span>
                        </div>
                        <div class="bg-white/5 rounded-lg p-3">
                            <span class="font-semibold text-yellow-300">{{ __('messages.title') }}:</span> 
                            <span>{{ $title }}</span>
                        </div>
                        @if(isset($historique_id))
                            <div class="bg-white/5 rounded-lg p-3 md:col-span-2">
                                <span class="font-semibold text-yellow-300">{{ __('messages.search_id') }}:</span> 
                                <span class="font-mono">#{{ $historique_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Price Comparison -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 mb-8">
                <h2 class="text-3xl font-bold text-white mb-8 text-center">
                    <i class="fas fa-euro-sign text-green-400 mr-3"></i>
                    {{ __('messages.price_comparison') }}
                </h2>
                
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Amazon Price -->
                    <div class="group relative overflow-hidden bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-orange-300/30">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-600/20 to-transparent"></div>
                        <div class="relative p-6 text-center text-white">
                            <div class="flex items-center justify-center mb-4">
                                <i class="fab fa-amazon text-4xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                                <h3 class="text-xl font-semibold">{{ __('messages.amazon') }}</h3>
                            </div>
                            
                            @if(isset($prices['amazon']) && is_array($prices['amazon']) && isset($prices['amazon']['formatted_min']) && !empty($prices['amazon']['formatted_min']))
                                <div class="text-5xl font-bold text-green-300 mb-3 group-hover:scale-105 transition-transform duration-300">
                                    {{ $prices['amazon']['formatted_min'] }}€
                                </div>
                                <div class="text-sm space-y-2 opacity-90">
                                    <div><strong>{{ $prices['amazon']['count'] }}</strong> offres d'occasion analysées</div>
                                    <div>Moyenne: <strong>{{ $prices['amazon']['formatted_average'] }}€</strong></div>
                                    <div>Max: <strong>{{ $prices['amazon']['formatted_max'] }}€</strong></div>
                                    <div>Amplitude: <strong>{{ $prices['amazon']['formatted_amplitude'] }}€</strong></div>
                                </div>
                            @elseif(isset($prices['amazon']) && is_string($prices['amazon']) && $prices['amazon'] !== __('messages.price_not_found'))
                                <div class="text-5xl font-bold text-green-300 mb-3 group-hover:scale-105 transition-transform duration-300">
                                    {{ $prices['amazon'] }}
                                </div>
                            @else
                                <div class="text-5xl font-bold text-red-400 mb-3">
                                    {{ __('messages.price_not_found') }}
                                </div>
                            @endif
                            
                            <div class="text-xs opacity-75 mt-3">
                                {{ __('messages.isbn_direct_search') }}
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-300 to-orange-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>

                    <!-- Cultura Price -->
                    <div class="group relative overflow-hidden bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-blue-300/30">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                        <div class="relative p-6 text-center text-white">
                            <div class="flex items-center justify-center mb-4">
                                <i class="fas fa-store text-4xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                                <h3 class="text-xl font-semibold">{{ __('messages.cultura') }}</h3>
                            </div>
                            @if(isset($prices['cultura']) && is_array($prices['cultura']) && isset($prices['cultura']['formatted_min']) && !empty($prices['cultura']['formatted_min']))
                                <div class="text-5xl font-bold text-green-300 mb-3 group-hover:scale-105 transition-transform duration-300">
                                    {{ $prices['cultura']['formatted_min'] }}€
                                </div>
                                <div class="text-sm space-y-2 opacity-90">
                                    <div><strong>{{ $prices['cultura']['count'] }}</strong> offres analysées</div>
                                    <div>Moyenne: <strong>{{ $prices['cultura']['formatted_average'] }}€</strong></div>
                                    <div>Max: <strong>{{ $prices['cultura']['formatted_max'] }}€</strong></div>
                                    <div>Amplitude: <strong>{{ $prices['cultura']['formatted_amplitude'] }}€</strong></div>
                                </div>
                            @elseif(isset($prices['cultura']) && is_string($prices['cultura']) && $prices['cultura'] !== __('messages.price_not_found'))
                                <div class="text-5xl font-bold text-green-300 mb-3 group-hover:scale-105 transition-transform duration-300">
                                    {{ $prices['cultura'] }}
                                </div>
                            @else
                                <div class="text-5xl font-bold text-red-400 mb-3">
                                    {{ __('messages.price_not_found') }}
                                </div>
                            @endif
                            <div class="text-xs opacity-75 mt-3">
                                {{ __('messages.isbn_search') }}
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-300 to-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>

                    <!-- Fnac Price -->
                    <div class="group relative overflow-hidden bg-gradient-to-br from-red-400 via-red-500 to-red-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-red-300/30">
                        <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
                        <div class="relative p-6 text-center text-white">
                            <div class="flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-4xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                                <h3 class="text-xl font-semibold">{{ __('messages.fnac') }}</h3>
                            </div>
                            @if(isset($prices['fnac']) && is_array($prices['fnac']) && isset($prices['fnac']['formatted_min']) && !empty($prices['fnac']['formatted_min']))
                                <div class="text-5xl font-bold text-green-300 mb-3 group-hover:scale-105 transition-transform duration-300">
                                    {{ $prices['fnac']['formatted_min'] }}€
                                </div>
                                <div class="text-sm space-y-2 opacity-90">
                                    <div><strong>{{ $prices['fnac']['count'] }}</strong> offres analysées</div>
                                    <div>Moyenne: <strong>{{ $prices['fnac']['formatted_average'] }}€</strong></div>
                                    <div>Max: <strong>{{ $prices['fnac']['formatted_max'] }}€</strong></div>
                                    <div>Amplitude: <strong>{{ $prices['fnac']['formatted_amplitude'] }}€</strong></div>
                                </div>
                            @elseif(isset($prices['fnac']) && is_string($prices['fnac']) && $prices['fnac'] !== __('messages.price_not_found'))
                                <div class="text-5xl font-bold text-green-300 mb-3 group-hover:scale-105 transition-transform duration-300">
                                    {{ $prices['fnac'] }}
                                </div>
                            @else
                                <div class="text-5xl font-bold text-red-400 mb-3">
                                    {{ __('messages.price_not_found') }}
                                </div>
                            @endif
                            <div class="text-xs opacity-75 mt-3">
                                {{ __('messages.title_search') }}
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-300 to-red-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- Best Price Indicator -->
                @php
                    $validPrices = [];
                    $bestProvider = null;
                    $bestPrice = null;
                    
                    foreach ($prices as $provider => $price) {
                        if (($provider === 'amazon' || $provider === 'fnac' || $provider === 'cultura') && is_array($price) && isset($price['min']) && $price['min'] > 0) {
                            $validPrices[$provider] = $price['min'];
                        } elseif (is_string($price) && $price !== __('messages.price_not_found') && $price !== __('messages.price_not_found')) {
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
                    <div class="mt-8 bg-gradient-to-r from-green-400 via-green-500 to-green-600 rounded-2xl p-6 text-white text-center shadow-2xl border border-green-300/30">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-trophy text-4xl mr-4 text-yellow-300"></i>
                            <div>
                                <h3 class="text-2xl font-bold mb-2">{{ __('messages.best_price_section') }}</h3>
                                @if(($bestProvider === 'amazon' || $bestProvider === 'fnac' || $bestProvider === 'cultura') && is_array($prices[$bestProvider]) && isset($prices[$bestProvider]['formatted_min']))
                                    <p class="text-3xl font-bold text-yellow-300">{{ $prices[$bestProvider]['formatted_min'] }}€ sur {{ ucfirst($bestProvider) }}</p>
                                    <p class="text-sm opacity-90">Prix d'occasion minimum</p>
                                @else
                                    <p class="text-3xl font-bold text-yellow-300">{{ $prices[$bestProvider] }} sur {{ ucfirst($bestProvider) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estimation Prix d'Occasion -->
            @if(isset($occasion_price) && $occasion_price !== __('messages.estimation_not_available'))
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 mb-8">
                    <h2 class="text-3xl font-bold text-white mb-8 text-center">
                        <i class="fas fa-handshake text-purple-400 mr-3"></i>
                        {{ __('messages.used_estimation_section') }}
                    </h2>
                    
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-purple-400 via-purple-500 to-purple-600 rounded-2xl p-8 border border-purple-300/30 shadow-2xl">
                            <div class="flex items-center justify-center mb-6">
                                <i class="fas fa-robot text-5xl text-purple-200 mr-4"></i>
                                <div>
                                    <h3 class="text-2xl font-semibold text-white">{{ __('messages.ai_estimation') }}</h3>
                                    <p class="text-sm text-purple-200">{{ __('messages.estimated_price_good_condition') }}</p>
                                </div>
                            </div>
                            <div class="text-6xl font-bold text-yellow-300 mb-6">
                                {{ $occasion_price }}
                            </div>
                            <div class="text-sm text-purple-200 bg-white/10 rounded-lg p-3 inline-block">
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
                <div class="group relative overflow-hidden bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-orange-300/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-600/20 to-transparent"></div>
                    <div class="relative p-6 text-white">
                        <div class="flex items-center mb-4">
                            <i class="fab fa-amazon text-3xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                            <h3 class="font-semibold text-xl">{{ __('messages.amazon') }}</h3>
                        </div>
                        <p class="text-orange-100 mb-4 opacity-90">{{ __('messages.isbn_direct_search') }}</p>
                        <div class="space-y-3">
                            <a href="https://www.amazon.fr/s?k={{ urlencode($isbn) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-orange-700 text-white rounded-lg hover:bg-orange-800 transition-colors group-hover:scale-105">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                {{ __('messages.view_result') }}
                            </a>
                            <p class="text-xs opacity-75">URL: amazon.fr/dp/{{ $isbn }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-300 to-orange-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Cultura -->
                <div class="group relative overflow-hidden bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-blue-300/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                    <div class="relative p-6 text-white">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-store text-3xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                            <h3 class="font-semibold text-xl">{{ __('messages.cultura') }}</h3>
                        </div>
                        <p class="text-blue-100 mb-4 opacity-90">{{ __('messages.isbn_search') }}</p>
                        <div class="space-y-3">
                            <a href="https://www.cultura.com/search/results?search_query={{ urlencode($isbn) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors group-hover:scale-105">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                {{ __('messages.view_result') }}
                            </a>
                            <p class="text-xs opacity-75">Recherche: {{ $isbn }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-300 to-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Fnac -->
                <div class="group relative overflow-hidden bg-gradient-to-br from-red-400 via-red-500 to-red-600 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 border border-red-300/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
                    <div class="relative p-6 text-white">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-shopping-cart text-3xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                            <h3 class="font-semibold text-xl">{{ __('messages.fnac') }}</h3>
                        </div>
                        <p class="text-red-100 mb-4 opacity-90">{{ __('messages.title_search') }}</p>
                        <div class="space-y-3">
                            <a href="https://www.fnac.com/SearchResult/ResultList.aspx?SCat=0!1&Search={{ urlencode($title) }}&sft=1&sa=0" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 transition-colors group-hover:scale-105">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                {{ __('messages.view_result') }}
                            </a>
                            <p class="text-xs opacity-75">Recherche: {{ $title }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-300 to-red-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center space-x-4">
                <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-2xl border border-indigo-300/30">
                    <i class="fas fa-search mr-3 text-xl"></i>
                    {{ __('messages.new_search_button') }}
                </a>
                
                <a href="/" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-2xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:scale-105 shadow-2xl border border-gray-300/30">
                    <i class="fas fa-home mr-3 text-xl"></i>
                    {{ __('messages.home') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout> 