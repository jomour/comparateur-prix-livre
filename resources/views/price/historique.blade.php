<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.search_history') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-8 xl:px-12 2xl:px-16">
            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs page="history" />
            </div>
            
            <!-- Stats -->
            <div class="bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-yellow-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20 mb-6 relative">
                <!-- Effet de brillance manga -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent animate-pulse"></div>
                
                <div class="p-4 sm:p-6 text-white relative z-10">
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 text-center">
                        <!-- Total Items -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-indigo-500/20 to-purple-600/20 rounded-xl p-3 sm:p-4 border border-indigo-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-1 sm:mb-2">
                                        {{ $paginator->total() }}
                                    </div>
                                    <div class="flex items-center text-xs sm:text-sm text-indigo-200">
                                        <i class="fas fa-history mr-1 sm:mr-2 text-indigo-300"></i>
                                        <span class="hidden sm:inline">Total</span>
                                        <span class="sm:hidden">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Searches -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-xl p-3 sm:p-4 border border-green-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-1 sm:mb-2">
                                        {{ $paginator->getCollection()->where('type', 'search')->count() }}
                                    </div>
                                    <div class="flex items-center text-xs sm:text-sm text-green-200">
                                        <i class="fas fa-search mr-1 sm:mr-2 text-green-300"></i>
                                        <span class="hidden sm:inline">Recherches</span>
                                        <span class="sm:hidden">Recherches</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lots -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-yellow-500/20 to-orange-600/20 rounded-xl p-3 sm:p-4 border border-yellow-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent mb-1 sm:mb-2">
                                        {{ $paginator->getCollection()->where('type', 'lot')->count() }}
                                    </div>
                                    <div class="flex items-center text-xs sm:text-sm text-yellow-200">
                                        <i class="fas fa-boxes mr-1 sm:mr-2 text-yellow-300"></i>
                                        <span class="hidden sm:inline">Lots</span>
                                        <span class="sm:hidden">Lots</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Mangas -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-xl p-3 sm:p-4 border border-blue-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent mb-1 sm:mb-2">
                                        {{ $paginator->getCollection()->sum('manga_count') }}
                                    </div>
                                    <div class="flex items-center text-xs sm:text-sm text-blue-200">
                                        <i class="fas fa-book mr-1 sm:mr-2 text-blue-300"></i>
                                        <span class="hidden sm:inline">Mangas</span>
                                        <span class="sm:hidden">Mangas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unified History Table (desktop) -->
            <div class="bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-yellow-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="overflow-x-auto hidden sm:block">
                    <table class="w-full min-w-full">
                        <thead class="bg-gradient-to-r from-purple-800/50 to-pink-800/50">
                            <tr>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-1 sm:mr-2 text-purple-300"></i>
                                        <span class="hidden sm:inline">{{ __('messages.date') }}</span>
                                        <span class="sm:hidden">Date</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-1 sm:mr-2 text-purple-300"></i>
                                        <span class="hidden sm:inline">Type</span>
                                        <span class="sm:hidden">Type</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-book mr-1 sm:mr-2 text-purple-300"></i>
                                        <span class="hidden sm:inline">Nom</span>
                                        <span class="sm:hidden">Nom</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-barcode mr-1 sm:mr-2 text-purple-300"></i>
                                        <span class="hidden sm:inline">{{ __('messages.isbn') }}</span>
                                        <span class="sm:hidden">ISBN</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fab fa-amazon mr-1 sm:mr-2 text-orange-400"></i>
                                        <span class="hidden sm:inline">{{ __('messages.amazon') }}</span>
                                        <span class="sm:hidden">Amazon</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-store mr-1 sm:mr-2 text-blue-400"></i>
                                        <span class="hidden sm:inline">{{ __('messages.cultura') }}</span>
                                        <span class="sm:hidden">Cultura</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-shopping-cart mr-1 sm:mr-2 text-red-400"></i>
                                        <span class="hidden sm:inline">{{ __('messages.fnac') }}</span>
                                        <span class="sm:hidden">Fnac</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-trophy mr-1 sm:mr-2 text-yellow-400"></i>
                                        <span class="hidden sm:inline">{{ __('messages.best_price') }}</span>
                                        <span class="sm:hidden">Meilleur</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-robot mr-1 sm:mr-2 text-purple-400"></i>
                                        <span class="hidden sm:inline">{{ __('messages.used_estimation') }}</span>
                                        <span class="sm:hidden">Estimation</span>
                                    </div>
                                </th>
                                <th class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-cogs mr-1 sm:mr-2 text-purple-300"></i>
                                        <span class="hidden sm:inline">{{ __('messages.actions') }}</span>
                                        <span class="sm:hidden">Actions</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-600/20">
                            @forelse($paginator as $item)
                                <tr class="hover:bg-gradient-to-r hover:from-purple-500/10 hover:to-pink-500/10 transition-all duration-300 group">
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-white">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-purple-200">{{ $item['created_at']->format('d/m/Y') }}</span>
                                            <span class="text-purple-300 text-xs">{{ $item['created_at']->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-white">
                                        @if($item['type'] === 'lot')
                                            <div class="flex items-center">
                                                <i class="fas fa-boxes text-yellow-400 mr-1 sm:mr-2"></i>
                                                <span class="text-yellow-300 bg-yellow-600/20 px-2 py-1 rounded border border-yellow-500/30">
                                                    Lot ({{ $item['manga_count'] }})
                                                </span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <i class="fas fa-search text-green-400 mr-1 sm:mr-2"></i>
                                                <span class="text-green-300 bg-green-600/20 px-2 py-1 rounded border border-green-500/30">
                                                    Recherche
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-white">
                                        <div class="max-w-xs truncate">
                                            <span class="text-white font-medium">
                                                {{ $item['title'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-white">
                                        @if($item['type'] === 'search')
                                            <div class="bg-purple-600/20 px-2 sm:px-3 py-1 rounded-lg border border-purple-500/30 inline-block">
                                                <span class="hidden sm:inline">{{ $item['isbn'] }}</span>
                                                <span class="sm:hidden">{{ substr($item['isbn'], 0, 8) }}...</span>
                                            </div>
                                        @else
                                            <span class="text-purple-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                        @if($item['type'] === 'search')
                                                                                    @php
                                            $search = $item['data'];
                                            $amazonProvider = $search->providers->where('name', 'amazon')->first();
                                            $amazonPrice = $amazonProvider && $amazonProvider->min && is_numeric($amazonProvider->min) && $amazonProvider->min > 0 ? number_format((float)$amazonProvider->min, 2, ',', ' ') . '€' : __('messages.price_not_found');
                                            $amazonFound = $amazonProvider && $amazonProvider->min && is_numeric($amazonProvider->min) && $amazonProvider->min > 0;
                                        @endphp
                                            <div class="flex items-center">
                                                <i class="fab fa-amazon text-orange-400 mr-1 sm:mr-2"></i>
                                                <span class="text-xs sm:text-sm {{ $amazonFound ? 'text-green-300 font-semibold bg-green-600/20 px-1 sm:px-2 py-1 rounded border border-green-500/30' : 'text-red-300 bg-red-600/20 px-1 sm:px-2 py-1 rounded border border-red-500/30' }}">
                                                    {{ $amazonPrice }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-purple-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                        @if($item['type'] === 'search')
                                                                                    @php
                                            $search = $item['data'];
                                            $culturaProvider = $search->providers->where('name', 'cultura')->first();
                                            $culturaPrice = $culturaProvider && $culturaProvider->min && is_numeric($culturaProvider->min) && $culturaProvider->min > 0 ? number_format((float)$culturaProvider->min, 2, ',', ' ') . '€' : __('messages.price_not_found');
                                            $culturaFound = $culturaProvider && $culturaProvider->min && is_numeric($culturaProvider->min) && $culturaProvider->min > 0;
                                        @endphp
                                            <div class="flex items-center">
                                                <i class="fas fa-store text-blue-400 mr-1 sm:mr-2"></i>
                                                <span class="text-xs sm:text-sm {{ $culturaFound ? 'text-green-300 font-semibold bg-green-600/20 px-1 sm:px-2 py-1 rounded border border-green-500/30' : 'text-red-300 bg-red-600/20 px-1 sm:px-2 py-1 rounded border border-red-500/30' }}">
                                                    {{ $culturaPrice }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-purple-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                        @if($item['type'] === 'search')
                                                                                    @php
                                            $search = $item['data'];
                                            $fnacProvider = $search->providers->where('name', 'fnac')->first();
                                            $fnacPrice = $fnacProvider && $fnacProvider->min && is_numeric($fnacProvider->min) && $fnacProvider->min > 0 ? number_format((float)$fnacProvider->min, 2, ',', ' ') . '€' : __('messages.price_not_found');
                                            $fnacFound = $fnacProvider && $fnacProvider->min && is_numeric($fnacProvider->min) && $fnacProvider->min > 0;
                                        @endphp
                                            <div class="flex items-center">
                                                <i class="fas fa-shopping-cart text-red-400 mr-1 sm:mr-2"></i>
                                                <span class="text-xs sm:text-sm {{ $fnacFound ? 'text-green-300 font-semibold bg-green-600/20 px-1 sm:px-2 py-1 rounded border border-green-500/30' : 'text-red-300 bg-red-600/20 px-1 sm:px-2 py-1 rounded border border-red-500/30' }}">
                                                    {{ $fnacPrice }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-purple-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                        @if($item['type'] === 'search')
                                            @php
                                                $search = $item['data'];
                                            @endphp
                                            @if($search->best_price)
                                                <div class="flex items-center">
                                                    <i class="fas fa-trophy text-yellow-400 mr-1 sm:mr-2"></i>
                                                    <span class="text-xs sm:text-sm font-semibold text-yellow-300 bg-yellow-600/20 px-1 sm:px-2 py-1 rounded border border-yellow-500/30">
                                                        {{ $search->best_price['price'] }}
                                                    </span>
                                                    <span class="text-xs text-purple-300 ml-1 sm:ml-2 bg-purple-600/20 px-1 sm:px-2 py-1 rounded border border-purple-500/30 hidden sm:inline">
                                                        ({{ ucfirst($search->best_price['provider']) }})
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs sm:text-sm text-purple-300 bg-purple-600/20 px-1 sm:px-2 py-1 rounded border border-purple-500/30">{{ __('messages.no_price') }}</span>
                                            @endif
                                        @else
                                            <span class="text-purple-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                        @if($item['type'] === 'search')
                                                                                    @php
                                            $search = $item['data'];
                                            $correct = $search->estimation_occasion_correct;
                                            $bon = $search->estimation_occasion_bon;
                                            $excellent = $search->estimation_occasion_excellent;
                                            
                                            // Trouver les valeurs non nulles et numériques
                                            $prices = array_filter([$correct, $bon, $excellent], function($price) {
                                                return $price !== null && is_numeric($price) && $price > 0;
                                            });
                                            
                                            $hasEstimations = !empty($prices);
                                            $minPrice = $hasEstimations ? min($prices) : null;
                                            $maxPrice = $hasEstimations ? max($prices) : null;
                                        @endphp
                                            
                                            @if($hasEstimations)
                                                <div class="flex items-center">
                                                    <i class="fas fa-robot text-purple-400 mr-1 sm:mr-2"></i>
                                                    <span class="text-xs sm:text-sm font-semibold text-purple-300 bg-purple-600/20 px-1 sm:px-2 py-1 rounded border border-purple-500/30">
                                                        @if($minPrice === $maxPrice)
                                                            {{ number_format((float)$minPrice, 2, ',', ' ') }}€
                                                        @else
                                                            {{ number_format((float)$minPrice, 2, ',', ' ') }} - {{ number_format((float)$maxPrice, 2, ',', ' ') }}€
                                                        @endif
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs sm:text-sm text-purple-300 bg-purple-600/20 px-1 sm:px-2 py-1 rounded border border-purple-500/30">{{ __('messages.not_available') }}</span>
                                            @endif
                                        @else
                                            <span class="text-purple-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-1">
                                            @if($item['type'] === 'search')
                                                @php
                                                    $search = $item['data'];
                                                @endphp
                                                <a href="{{ \App\Helpers\LocalizedRoute::localized('historique.show', $search->id) }}" 
                                                   class="text-green-400 hover:text-green-300 hover:scale-110 transition-all duration-200 bg-green-600/20 p-1 rounded border border-green-500/30 flex items-center justify-center space-x-1 text-xs" 
                                                   title="{{ __('messages.view_results') }}">
                                                    <i class="fas fa-eye text-xs sm:text-sm"></i>
                                                    <span class="hidden sm:inline ml-1">👁️</span>
                                                    <span class="hidden lg:inline ml-1 font-bold">Vue</span>
                                                </a>
                                                <a href="https://www.amazon.fr/s?k={{ urlencode($search->isbn) }}" 
                                                   target="_blank"
                                                   class="text-orange-400 hover:text-orange-300 hover:scale-110 transition-all duration-200 bg-orange-600/20 p-1 rounded border border-orange-500/30 flex items-center justify-center space-x-1 text-xs" 
                                                   title="{{ __('messages.view_amazon') }}">
                                                    <i class="fab fa-amazon text-xs sm:text-sm"></i>
                                                    <span class="hidden sm:inline ml-1">🛒</span>
                                                    <span class="hidden lg:inline ml-1 font-bold">Amazon</span>
                                                </a>
                                                <a href="https://www.cultura.com/search/results?search_query={{ urlencode($search->isbn) }}" 
                                                   target="_blank"
                                                   class="text-blue-400 hover:text-blue-300 hover:scale-110 transition-all duration-200 bg-blue-600/20 p-1 rounded border border-blue-500/30 flex items-center justify-center space-x-1 text-xs" 
                                                   title="{{ __('messages.view_cultura') }}">
                                                    <i class="fas fa-store text-xs sm:text-sm"></i>
                                                    <span class="hidden sm:inline ml-1">📚</span>
                                                    <span class="hidden lg:inline ml-1 font-bold">Cultura</span>
                                                </a>
                                                <a href="https://www.fnac.com/SearchResult/ResultList.aspx?SCat=0!1&Search={{ urlencode($search->title ?? $search->isbn) }}&sft=1&sa=0" 
                                                   target="_blank"
                                                   class="text-red-400 hover:text-red-300 hover:scale-110 transition-all duration-200 bg-red-600/20 p-1 rounded border border-red-500/30 flex items-center justify-center space-x-1 text-xs" 
                                                   title="{{ __('messages.view_fnac') }}">
                                                    <i class="fas fa-shopping-cart text-xs sm:text-sm"></i>
                                                    <span class="hidden sm:inline ml-1">⭐</span>
                                                    <span class="hidden lg:inline ml-1 font-bold">Fnac</span>
                                                </a>
                                            @else
                                                @php
                                                    $lot = $item['data'];
                                                @endphp
                                                <a href="{{ \App\Helpers\LocalizedRoute::url('historique.show.lot', $lot->id) }}" 
                                                   class="text-yellow-400 hover:text-yellow-300 hover:scale-110 transition-all duration-200 bg-yellow-600/20 p-1 rounded border border-yellow-500/30 flex items-center justify-center space-x-1 text-xs" 
                                                   title="Voir les résultats du lot">
                                                    <i class="fas fa-eye text-xs sm:text-sm"></i>
                                                    <span class="hidden sm:inline ml-1">👁️</span>
                                                    <span class="hidden lg:inline ml-1 font-bold">Voir lot</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-purple-200 py-8">{{ __('messages.no_history') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Mobile cards version -->
                <div class="flex flex-col gap-4 sm:hidden p-2">
                    @forelse($paginator as $item)
                        <div class="bg-gradient-to-br from-purple-800/80 via-pink-800/80 to-yellow-800/80 rounded-xl shadow-lg border border-white/20 p-4 flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-purple-200 font-semibold">
                                    <i class="fas fa-calendar-alt mr-1 text-purple-300"></i>{{ $item['created_at']->format('d/m/Y H:i') }}
                                </span>
                                <span class="text-xs font-bold">
                                    @if($item['type'] === 'lot')
                                        <span class="text-yellow-300"><i class="fas fa-boxes mr-1"></i>Lot ({{ $item['manga_count'] }})</span>
                                    @else
                                        <span class="text-green-300"><i class="fas fa-search mr-1"></i>Recherche</span>
                                    @endif
                                </span>
                            </div>
                            <div class="font-medium text-white truncate">{{ $item['title'] }}</div>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <span class="bg-purple-900/40 px-2 py-1 rounded"><i class="fas fa-barcode mr-1"></i>{{ $item['isbn'] ?? 'N/A' }}</span>
                                @if($item['type'] === 'search')
                                    @php
                                        $search = $item['data'];
                                        $amazonProvider = $search->providers->where('name', 'amazon')->first();
                                        $culturaProvider = $search->providers->where('name', 'cultura')->first();
                                        $fnacProvider = $search->providers->where('name', 'fnac')->first();
                                        
                                        $amazonPrice = $amazonProvider && $amazonProvider->min && is_numeric($amazonProvider->min) && $amazonProvider->min > 0 ? number_format((float)$amazonProvider->min, 2, ',', ' ') . '€' : '-';
                                        $culturaPrice = $culturaProvider && $culturaProvider->min && is_numeric($culturaProvider->min) && $culturaProvider->min > 0 ? number_format((float)$culturaProvider->min, 2, ',', ' ') . '€' : '-';
                                        $fnacPrice = $fnacProvider && $fnacProvider->min && is_numeric($fnacProvider->min) && $fnacProvider->min > 0 ? number_format((float)$fnacProvider->min, 2, ',', ' ') . '€' : '-';
                                        
                                        $bestPrice = $search->best_price ? $search->best_price['price'] : '-';
                                        
                                        $correct = $search->estimation_occasion_correct;
                                        $bon = $search->estimation_occasion_bon;
                                        $excellent = $search->estimation_occasion_excellent;
                                        
                                        $prices = array_filter([$correct, $bon, $excellent], function($price) {
                                            return $price !== null && is_numeric($price) && $price > 0;
                                        });
                                        
                                        $hasEstimations = !empty($prices);
                                        $minPrice = $hasEstimations ? min($prices) : null;
                                        $maxPrice = $hasEstimations ? max($prices) : null;
                                        
                                        $estimationPrice = $hasEstimations ? 
                                            ($minPrice === $maxPrice ? 
                                                number_format((float)$minPrice, 2, ',', ' ') . '€' : 
                                                number_format((float)$minPrice, 2, ',', ' ') . '-' . number_format((float)$maxPrice, 2, ',', ' ') . '€'
                                            ) : '-';
                                    @endphp
                                    <div class="flex flex-col gap-1 w-full">
                                        <div class="text-xs text-purple-300 font-semibold mb-1">Prix trouvés :</div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="bg-orange-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fab fa-amazon mr-1 text-orange-400"></i>
                                                <span class="text-xs font-medium">Amazon:</span>
                                                <span class="ml-1 font-bold">{{ $amazonPrice }}</span>
                                            </span>
                                            <span class="bg-blue-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fas fa-store mr-1 text-blue-400"></i>
                                                <span class="text-xs font-medium">Cultura:</span>
                                                <span class="ml-1 font-bold">{{ $culturaPrice }}</span>
                                            </span>
                                            <span class="bg-pink-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fas fa-shopping-cart mr-1 text-red-400"></i>
                                                <span class="text-xs font-medium">Fnac:</span>
                                                <span class="ml-1 font-bold">{{ $fnacPrice }}</span>
                                            </span>
                                        </div>
                                        <div class="text-xs text-purple-300 font-semibold mt-2 mb-1">Résumé :</div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="bg-yellow-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fas fa-trophy mr-1 text-yellow-400"></i>
                                                <span class="text-xs font-medium">Meilleur:</span>
                                                <span class="ml-1 font-bold">{{ $bestPrice }}</span>
                                            </span>
                                            <span class="bg-purple-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fas fa-robot mr-1 text-purple-400"></i>
                                                <span class="text-xs font-medium">Occasion:</span>
                                                <span class="ml-1 font-bold">{{ $estimationPrice }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-1 w-full">
                                        <div class="text-xs text-purple-300 font-semibold mb-1">Lot de mangas</div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="bg-orange-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fab fa-amazon mr-1 text-orange-400"></i>
                                                <span class="text-xs font-medium">Amazon:</span>
                                                <span class="ml-1 font-bold">-</span>
                                            </span>
                                            <span class="bg-blue-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fas fa-store mr-1 text-blue-400"></i>
                                                <span class="text-xs font-medium">Cultura:</span>
                                                <span class="ml-1 font-bold">-</span>
                                            </span>
                                            <span class="bg-pink-900/40 px-2 py-1 rounded flex items-center">
                                                <i class="fas fa-shopping-cart mr-1 text-red-400"></i>
                                                <span class="text-xs font-medium">Fnac:</span>
                                                <span class="ml-1 font-bold">-</span>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-2 mt-2">
                                <!-- Actions (voir) -->
                                @if($item['type'] === 'search')
                                    @php
                                        $search = $item['data'];
                                    @endphp
                                    <a href="{{ \App\Helpers\LocalizedRoute::url('historique.show', $item['data']->id) }}" 
                                       class="text-green-400 hover:text-green-300 hover:scale-110 transition-all duration-200 bg-green-600/20 p-1 rounded border border-green-500/30 flex items-center justify-center space-x-1 text-xs" 
                                       title="{{ __('messages.view_results') }}">
                                        <i class="fas fa-eye text-xs"></i>
                                        <span class="ml-1">👁️</span>
                                        <span class="ml-1 font-bold">Vue</span>
                                    </a>
                                    <a href="https://www.amazon.fr/s?k={{ urlencode($search->isbn) }}" 
                                       target="_blank"
                                       class="text-orange-400 hover:text-orange-300 hover:scale-110 transition-all duration-200 bg-orange-600/20 p-1 rounded border border-orange-500/30 flex items-center justify-center space-x-1 text-xs" 
                                       title="{{ __('messages.view_amazon') }}">
                                        <i class="fab fa-amazon text-xs"></i>
                                        <span class="ml-1">🛒</span>
                                        <span class="ml-1 font-bold">Amazon</span>
                                    </a>
                                    <a href="https://www.cultura.com/search/results?search_query={{ urlencode($search->isbn) }}" 
                                       target="_blank"
                                       class="text-blue-400 hover:text-blue-300 hover:scale-110 transition-all duration-200 bg-blue-600/20 p-1 rounded border border-blue-500/30 flex items-center justify-center space-x-1 text-xs" 
                                       title="{{ __('messages.view_cultura') }}">
                                        <i class="fas fa-store text-xs"></i>
                                        <span class="ml-1">📚</span>
                                        <span class="ml-1 font-bold">Cultura</span>
                                    </a>
                                    <a href="https://www.fnac.com/SearchResult/ResultList.aspx?SCat=0!1&Search={{ urlencode($search->title ?? $search->isbn) }}&sft=1&sa=0" 
                                       target="_blank"
                                       class="text-red-400 hover:text-red-300 hover:scale-110 transition-all duration-200 bg-red-600/20 p-1 rounded border border-red-500/30 flex items-center justify-center space-x-1 text-xs" 
                                       title="{{ __('messages.view_fnac') }}">
                                        <i class="fas fa-shopping-cart text-xs"></i>
                                        <span class="ml-1">⭐</span>
                                        <span class="ml-1 font-bold">Fnac</span>
                                    </a>
                                @else
                                    <a href="{{ \App\Helpers\LocalizedRoute::url('historique.show.lot', $item['data']->id) }}" 
                                       class="text-yellow-400 hover:text-yellow-300 hover:scale-110 transition-all duration-200 bg-yellow-600/20 p-1 rounded border border-yellow-500/30 flex items-center justify-center space-x-1 text-xs" 
                                       title="Voir les résultats du lot">
                                        <i class="fas fa-eye text-xs"></i>
                                        <span class="ml-1">👁️</span>
                                        <span class="ml-1 font-bold">Voir lot</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-purple-200 py-8">{{ __('messages.no_history') }}</div>
                    @endforelse
                </div>
            </div>

                <!-- Pagination -->
                @if($paginator->hasPages())
                    <div class="bg-gradient-to-r from-purple-800/50 to-pink-800/50 px-4 py-3 border-t border-purple-600/30 sm:px-6">
                        {{ $paginator->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 