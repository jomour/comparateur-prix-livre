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
                        <p class="text-purple-200">{{ __('messages.prices_found_for_mangas_count', ['count' => $totalMangas]) }}</p>
                    </div>

                    <!-- Statistiques globales -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Nombre de mangas -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-xl p-6 border border-blue-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-blue-600/30 to-cyan-600/30 rounded-full p-3 mr-4 border border-blue-500/30">
                                        <i class="fas fa-books text-blue-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-blue-200">{{ __('messages.manga_count_simple') }}</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">{{ $totalMangas }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Prix total estimé -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-xl p-6 border border-green-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-green-600/30 to-emerald-600/30 rounded-full p-3 mr-4 border border-green-500/30">
                                        <i class="fas fa-euro-sign text-green-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-green-200">{{ __('messages.total_estimated_price') }}</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">{{ number_format($totalPrice, 2) }} €</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Prix moyen -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-purple-500/20 to-pink-600/20 rounded-xl p-6 border border-purple-400/30 backdrop-blur-sm">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-purple-600/30 to-pink-600/30 rounded-full p-3 mr-4 border border-purple-500/30">
                                        <i class="fas fa-calculator text-purple-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-purple-200">{{ __('messages.average_price_simple') }}</div>
                                        <div class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                                            {{ $foundPrices > 0 ? number_format($totalPrice / $foundPrices, 2) : '0.00' }} €
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des résultats -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-purple-600/30 rounded-lg">
                            <thead class="bg-gradient-to-r from-purple-800/50 to-pink-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fas fa-book mr-2 text-purple-300"></i>
                                            {{ __('messages.title') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fas fa-barcode mr-2 text-purple-300"></i>
                                            {{ __('messages.isbn') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fab fa-amazon mr-2 text-orange-400"></i>
                                            {{ __('messages.amazon_price') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fas fa-store mr-2 text-blue-400"></i>
                                            {{ __('messages.cultura_price') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fas fa-shopping-cart mr-2 text-red-400"></i>
                                            {{ __('messages.fnac_price') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fas fa-robot mr-2 text-purple-400"></i>
                                            {{ __('messages.used_price') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle mr-2 text-purple-300"></i>
                                            {{ __('messages.status') }}
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-purple-600/20">
                                @foreach($results as $result)
                                    <tr class="hover:bg-gradient-to-r hover:from-purple-500/10 hover:to-pink-500/10 transition-all duration-300 group">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                                            <div class="bg-purple-600/20 px-3 py-1 rounded-lg border border-purple-500/30 inline-block">
                                                {{ $result['title'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-mono">
                                            <div class="bg-purple-600/20 px-3 py-1 rounded-lg border border-purple-500/30 inline-block">
                                                {{ $result['isbn'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['all_prices']['amazon']) && $result['all_prices']['amazon'])
                                                <span class="text-green-300 font-semibold bg-green-600/20 px-2 py-1 rounded border border-green-500/30">{{ number_format($result['all_prices']['amazon'], 2) }} €</span>
                                            @else
                                                <span class="text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['all_prices']['cultura']) && $result['all_prices']['cultura'])
                                                <span class="text-green-300 font-semibold bg-green-600/20 px-2 py-1 rounded border border-green-500/30">{{ number_format($result['all_prices']['cultura'], 2) }} €</span>
                                            @else
                                                <span class="text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['all_prices']['fnac']) && $result['all_prices']['fnac'])
                                                <span class="text-green-300 font-semibold bg-green-600/20 px-2 py-1 rounded border border-green-500/30">{{ number_format($result['all_prices']['fnac'], 2) }} €</span>
                                            @else
                                                <span class="text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['occasion_price']) && $result['occasion_price'])
                                                <span class="text-orange-300 font-semibold bg-orange-600/20 px-2 py-1 rounded border border-orange-500/30">{{ number_format($result['occasion_price'], 2) }} €</span>
                                            @else
                                                <span class="text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @switch($result['status'])
                                                @case('success')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-300 border border-green-500/30">
                                                        <i class="fas fa-check mr-1"></i>
                                                        {{ __('messages.price_found') }}
                                                    </span>
                                                    @break
                                                @case('not_found')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-600/20 text-red-300 border border-red-500/30">
                                                        <i class="fas fa-times mr-1"></i>
                                                        {{ __('messages.not_found') }}
                                                    </span>
                                                    @break
                                                @case('error')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-600/20 text-yellow-300 border border-yellow-500/30">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        {{ __('messages.error') }}
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-center space-x-6">
                        <a href="{{ \App\Helpers\LocalizedRoute::url('image.upload.form') }}" 
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
</x-app-layout> 