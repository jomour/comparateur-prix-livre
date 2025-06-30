<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Résultats de la recherche globale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">
                            <i class="fas fa-chart-bar text-green-600 mr-3"></i>
                            Résultats de la recherche globale
                        </h1>
                        <p class="text-gray-600">Prix trouvés pour {{ $totalMangas }} mangas</p>
                    </div>

                    <!-- Statistiques globales -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <i class="fas fa-books text-blue-500 text-2xl mr-3"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Nombre de mangas</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $totalMangas }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <i class="fas fa-euro-sign text-green-500 text-2xl mr-3"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Prix total estimé</div>
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($totalPrice, 2) }} €</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                            <div class="flex items-center">
                                <i class="fas fa-calculator text-purple-500 text-2xl mr-3"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Prix moyen</div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ $foundPrices > 0 ? number_format($totalPrice / $foundPrices, 2) : '0.00' }} €
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des résultats -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">ISBN</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Prix Amazon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Prix Cultura</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Prix Fnac</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Prix Occasion</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($results as $result)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            {{ $result['title'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                            {{ $result['isbn'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['all_prices']['amazon']) && $result['all_prices']['amazon'])
                                                <span class="text-green-600 font-semibold">{{ number_format($result['all_prices']['amazon'], 2) }} €</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['all_prices']['cultura']) && $result['all_prices']['cultura'])
                                                <span class="text-green-600 font-semibold">{{ number_format($result['all_prices']['cultura'], 2) }} €</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['all_prices']['fnac']) && $result['all_prices']['fnac'])
                                                <span class="text-green-600 font-semibold">{{ number_format($result['all_prices']['fnac'], 2) }} €</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(isset($result['occasion_price']) && $result['occasion_price'])
                                                <span class="text-orange-600 font-semibold">{{ number_format($result['occasion_price'], 2) }} €</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @switch($result['status'])
                                                @case('success')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Prix trouvé
                                                    </span>
                                                    @break
                                                @case('not_found')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Non trouvé
                                                    </span>
                                                    @break
                                                @case('error')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Erreur
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
                    <div class="mt-8 flex justify-center space-x-4">
                        <a href="{{ route('image.upload.form') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Nouvelle analyse
                        </a>
                        
                        <a href="{{ route('price.search') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Recherche simple
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 