<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.search_history') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs page="history" />
            </div>
            <!-- Stats -->
            <div class="bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-yellow-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20 mb-6 relative">
                <!-- Effet de brillance manga -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent animate-pulse"></div>
                
                <div class="p-6 text-white relative z-10">
                    <div class="grid md:grid-cols-4 gap-6 text-center">
                        <!-- Total Searches -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-indigo-500/20 to-purple-600/20 rounded-xl p-4 border border-indigo-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-3xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-2">
                                        {{ $searches->total() }}
                                    </div>
                                    <div class="flex items-center text-sm text-indigo-200">
                                        <i class="fas fa-search-plus mr-2 text-indigo-300"></i>
                                        {{ __('messages.total_searches') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Searches -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-xl p-4 border border-green-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-3xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-2">
                                        {{ $searches->getCollection()->where('has_all_prices', true)->count() }}
                                    </div>
                                    <div class="flex items-center text-sm text-green-200">
                                        <i class="fas fa-check-circle mr-2 text-green-300"></i>
                                        {{ __('messages.complete_searches') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- With Prices -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-xl p-4 border border-blue-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent mb-2">
                                        {{ $searches->getCollection()->where('prices_found', '>', 0)->count() }}
                                    </div>
                                    <div class="flex items-center text-sm text-blue-200">
                                        <i class="fas fa-coins mr-2 text-blue-300"></i>
                                        {{ __('messages.with_prices') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Without Prices -->
                        <div class="group transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 rounded-xl p-4 border border-orange-400/30 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <div class="text-3xl font-bold bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent mb-2">
                                        {{ $searches->getCollection()->where('prices_found', 0)->count() }}
                                    </div>
                                    <div class="flex items-center text-sm text-orange-200">
                                        <i class="fas fa-exclamation-triangle mr-2 text-orange-300"></i>
                                        {{ __('messages.without_prices') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Table -->
            <div class="bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-yellow-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-purple-800/50 to-pink-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-purple-300"></i>
                                        {{ __('messages.date') }}
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
                                        {{ __('messages.amazon') }}
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-store mr-2 text-blue-400"></i>
                                        {{ __('messages.cultura') }}
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-shopping-cart mr-2 text-red-400"></i>
                                        {{ __('messages.fnac') }}
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-trophy mr-2 text-yellow-400"></i>
                                        {{ __('messages.best_price') }}
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-robot mr-2 text-purple-400"></i>
                                        {{ __('messages.used_estimation') }}
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-purple-200 uppercase tracking-wider border-b border-purple-600/30">
                                    <div class="flex items-center">
                                        <i class="fas fa-cogs mr-2 text-purple-300"></i>
                                        {{ __('messages.actions') }}
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-600/20">
                            @forelse($searches as $search)
                                <tr class="hover:bg-gradient-to-r hover:from-purple-500/10 hover:to-pink-500/10 transition-all duration-300 group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-purple-200">{{ $search->created_at->format('d/m/Y') }}</span>
                                            <span class="text-purple-300 text-xs">{{ $search->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                        <div class="bg-purple-600/20 px-3 py-1 rounded-lg border border-purple-500/30 inline-block">
                                            {{ $search->isbn }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fab fa-amazon text-orange-400 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_amazon !== __('messages.price_not_found') ? 'text-green-300 font-semibold bg-green-600/20 px-2 py-1 rounded border border-green-500/30' : 'text-red-300 bg-red-600/20 px-2 py-1 rounded border border-red-500/30' }}">
                                                {{ $search->prix_amazon }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-store text-blue-400 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_cultura !== __('messages.price_not_found') ? 'text-green-300 font-semibold bg-green-600/20 px-2 py-1 rounded border border-green-500/30' : 'text-red-300 bg-red-600/20 px-2 py-1 rounded border border-red-500/30' }}">
                                                {{ $search->prix_cultura }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-shopping-cart text-red-400 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_fnac !== __('messages.price_not_found') ? 'text-green-300 font-semibold bg-green-600/20 px-2 py-1 rounded border border-green-500/30' : 'text-red-300 bg-red-600/20 px-2 py-1 rounded border border-red-500/30' }}">
                                                {{ $search->prix_fnac }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($search->best_price)
                                            <div class="flex items-center">
                                                <i class="fas fa-trophy text-yellow-400 mr-2"></i>
                                                <span class="text-sm font-semibold text-yellow-300 bg-yellow-600/20 px-2 py-1 rounded border border-yellow-500/30">
                                                    {{ $search->best_price['price'] }}
                                                </span>
                                                <span class="text-xs text-purple-300 ml-2 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">
                                                    ({{ ucfirst($search->best_price['provider']) }})
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">{{ __('messages.no_price') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($search->estimation_occasion && $search->estimation_occasion !== __('messages.estimation_not_available'))
                                            <div class="flex items-center">
                                                <i class="fas fa-robot text-purple-400 mr-2"></i>
                                                <span class="text-sm font-semibold text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">
                                                    {{ $search->estimation_occasion }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-purple-300 bg-purple-600/20 px-2 py-1 rounded border border-purple-500/30">{{ __('messages.not_available') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.amazon', $search->id) }}" 
                                               target="_blank"
                                               class="text-orange-400 hover:text-orange-300 hover:scale-110 transition-all duration-200 bg-orange-600/20 p-2 rounded-lg border border-orange-500/30" 
                                               title="{{ __('messages.view_amazon') }}">
                                                <i class="fab fa-amazon"></i>
                                            </a>
                                            <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.cultura', $search->id) }}" 
                                               target="_blank"
                                               class="text-blue-400 hover:text-blue-300 hover:scale-110 transition-all duration-200 bg-blue-600/20 p-2 rounded-lg border border-blue-500/30" 
                                               title="{{ __('messages.view_cultura') }}">
                                                <i class="fas fa-store"></i>
                                            </a>
                                            <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.fnac', $search->id) }}" 
                                               target="_blank"
                                               class="text-red-400 hover:text-red-300 hover:scale-110 transition-all duration-200 bg-red-600/20 p-2 rounded-lg border border-red-500/30" 
                                               title="{{ __('messages.view_fnac') }}">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}?isbn={{ $search->isbn }}" 
                                               class="text-purple-400 hover:text-purple-300 hover:scale-110 transition-all duration-200 bg-purple-600/20 p-2 rounded-lg border border-purple-500/30" 
                                               title="{{ __('messages.new_search') }}">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-purple-200">
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gradient-to-br from-purple-600/20 to-pink-600/20 rounded-full p-6 mb-4 border border-purple-500/30">
                                                <i class="fas fa-search text-4xl text-purple-300"></i>
                                            </div>
                                            <p class="text-lg font-medium text-white mb-2">{{ __('messages.no_searches_found') }}</p>
                                            <p class="text-sm text-purple-300 mb-4">{{ __('messages.start_first_search') }}</p>
                                            <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
                                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 border border-purple-500/30">
                                                <i class="fas fa-search mr-2"></i>
                                                {{ __('messages.new_search_button') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($searches->hasPages())
                    <div class="bg-gradient-to-r from-purple-800/50 to-pink-800/50 px-4 py-3 border-t border-purple-600/30 sm:px-6">
                        {{ $searches->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 