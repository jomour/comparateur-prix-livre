<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historique des Recherches') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-indigo-600">{{ $searches->total() }}</div>
                            <div class="text-sm text-gray-600">Total des recherches</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $searches->getCollection()->where('has_all_prices', true)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Recherches complètes</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $searches->getCollection()->where('prices_found', '>', 0)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Avec prix trouvés</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-orange-600">
                                {{ $searches->getCollection()->where('prices_found', 0)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Sans prix</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ISBN
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amazon
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cultura
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fnac
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Meilleur Prix
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estimation Occasion
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
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
                                            <span class="text-sm {{ $search->prix_amazon !== 'Prix non trouvé' ? 'text-green-600 font-semibold' : 'text-red-500' }}">
                                                {{ $search->prix_amazon }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-store text-blue-500 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_cultura !== 'Prix non trouvé' ? 'text-green-600 font-semibold' : 'text-red-500' }}">
                                                {{ $search->prix_cultura }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-shopping-cart text-red-500 mr-2"></i>
                                            <span class="text-sm {{ $search->prix_fnac !== 'Prix non trouvé' ? 'text-green-600 font-semibold' : 'text-red-500' }}">
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
                                            <span class="text-sm text-gray-500">Aucun prix</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($search->estimation_occasion && $search->estimation_occasion !== 'Estimation non disponible')
                                            <div class="flex items-center">
                                                <i class="fas fa-robot text-purple-500 mr-2"></i>
                                                <span class="text-sm font-semibold text-purple-600">
                                                    {{ $search->estimation_occasion }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Non disponible</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('price.show.amazon', $search->id) }}" 
                                               target="_blank"
                                               class="text-orange-600 hover:text-orange-900" 
                                               title="Voir Amazon">
                                                <i class="fab fa-amazon"></i>
                                            </a>
                                            <a href="{{ route('price.show.cultura', $search->id) }}" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-900" 
                                               title="Voir Cultura">
                                                <i class="fas fa-store"></i>
                                            </a>
                                            <a href="{{ route('price.show.fnac', $search->id) }}" 
                                               target="_blank"
                                               class="text-red-600 hover:text-red-900" 
                                               title="Voir Fnac">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            <a href="{{ route('price.search') }}?isbn={{ $search->isbn }}" 
                                               class="text-indigo-600 hover:text-indigo-900" 
                                               title="Nouvelle recherche">
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
                                            <p class="text-lg font-medium">Aucune recherche trouvée</p>
                                            <p class="text-sm">Commencez par faire votre première recherche de prix</p>
                                            <a href="{{ route('price.search') }}" 
                                               class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                <i class="fas fa-search mr-2"></i>
                                                Nouvelle Recherche
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