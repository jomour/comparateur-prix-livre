<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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
            <div class="bg-white/10 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20 mb-6">
                <div class="p-6 text-white">
                    <div class="grid md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-indigo-600">{{ $searches->total() }}</div>
                            <div class="text-sm text-gray-600">{{ __('messages.total_searches') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $searches->getCollection()->where('has_all_prices', true)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">{{ __('messages.complete_searches') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $searches->getCollection()->where('prices_found', '>', 0)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">{{ __('messages.with_prices') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-orange-600">
                                {{ $searches->getCollection()->where('prices_found', 0)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">{{ __('messages.without_prices') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Table -->
            <div class="bg-white/10 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.isbn') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.amazon') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.cultura') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.fnac') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.best_price') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.used_estimation') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($searches as $search)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $search->created_at->format('d/m/Y') }}</span>
                                            <span class="text-gray-500">{{ $search->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $search->isbn }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fab fa-amazon text-orange-500 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_amazon !== __('messages.price_not_found') ? 'text-green-600 font-semibold' : 'text-red-500' }}">
                                                {{ $search->prix_amazon }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-store text-blue-500 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_cultura !== __('messages.price_not_found') ? 'text-green-600 font-semibold' : 'text-red-500' }}">
                                                {{ $search->prix_cultura }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-shopping-cart text-red-500 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_fnac !== __('messages.price_not_found') ? 'text-green-600 font-semibold' : 'text-red-500' }}">
                                                {{ $search->prix_fnac }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($search->best_price)
                                            <div class="flex items-center">
                                                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                                <span class="text-sm font-semibold text-green-600">
                                                    {{ $search->best_price['price'] }}
                                                </span>
                                                <span class="text-xs text-gray-500 ml-1">
                                                    ({{ ucfirst($search->best_price['provider']) }})
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">{{ __('messages.no_price') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($search->estimation_occasion && $search->estimation_occasion !== __('messages.estimation_not_available'))
                                            <div class="flex items-center">
                                                <i class="fas fa-robot text-purple-500 mr-2"></i>
                                                <span class="text-sm font-semibold text-purple-600">
                                                    {{ $search->estimation_occasion }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">{{ __('messages.not_available') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.amazon', $search->id) }}" 
                                               target="_blank"
                                               class="text-orange-600 hover:text-orange-900" 
                                               title="{{ __('messages.view_amazon') }}">
                                                <i class="fab fa-amazon"></i>
                                            </a>
                                            <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.cultura', $search->id) }}" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-900" 
                                               title="{{ __('messages.view_cultura') }}">
                                                <i class="fas fa-store"></i>
                                            </a>
                                            <a href="{{ \App\Helpers\LocalizedRoute::localized('price.show.fnac', $search->id) }}" 
                                               target="_blank"
                                               class="text-red-600 hover:text-red-900" 
                                               title="{{ __('messages.view_fnac') }}">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}?isbn={{ $search->isbn }}" 
                                               class="text-indigo-600 hover:text-indigo-900" 
                                               title="{{ __('messages.new_search') }}">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-search text-4xl mb-4 text-gray-300"></i>
                                            <p class="text-lg font-medium">{{ __('messages.no_searches_found') }}</p>
                                            <p class="text-sm">{{ __('messages.start_first_search') }}</p>
                                            <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
                                               class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
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
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $searches->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 