<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.manga_lot_estimation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs page="image" />
            </div>
            <div class="bg-white/10 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-6 text-white">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <div>
                                    <strong>{{ __('messages.success') }} !</strong> {{ session('success') }}
                                    <div class="text-sm mt-1">
                                        <i class="fas fa-search mr-1"></i>{{ __('messages.auto_isbn_search') }}
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
                            <h3 class="text-lg font-semibold mb-4">{{ __('messages.analyzed_image') }} :</h3>
                            <img src="{{ \App\Helpers\LocalizedRoute::localized('image.show', session('image')) }}" alt="Image uploadée" class="max-w-full h-auto rounded-lg shadow-md">
                        </div>
                    @endif

                    @if(session('mangas'))
                        @if(count(session('mangas')) > 0)
                            <div class="mt-6">
                                <div class="flex justify-between items-center mb-6">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-purple-600/30 to-pink-600/30 rounded-full p-3 mr-4 border border-purple-500/30">
                                            <i class="fas fa-books text-purple-300 text-xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white">{{ __('messages.detected_mangas') }} :</h3>
                                    </div>
                                    <button onclick="searchAllPrices()" id="searchAllButton" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 px-6 rounded-xl flex items-center cursor-not-allowed border border-purple-500/30 shadow-lg transform hover:scale-105 transition-all duration-300" disabled>
                                        <div class="bg-purple-600/30 rounded-full p-2 mr-3 border border-purple-500/30">
                                            <i class="fas fa-lock mr-2" id="searchAllIcon"></i>
                                        </div>
                                        <span id="searchAllText">{{ __('messages.validate_all_mangas') }}</span>
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border-2 border-purple-400/40 rounded-lg shadow-2xl">
                                        <thead class="bg-gradient-to-r from-purple-700/70 to-pink-700/70">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b-2 border-purple-300/50">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-book mr-2 text-purple-200"></i>
                                                        {{ __('messages.title') }}
                                                    </div>
                                                </th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b-2 border-purple-300/50">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-barcode mr-2 text-purple-200"></i>
                                                        {{ __('messages.isbn') }}
                                                    </div>
                                                </th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b-2 border-purple-300/50">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-euro-sign mr-2 text-purple-200"></i>
                                                        {{ __('messages.estimated_price') }}
                                                    </div>
                                                </th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b-2 border-purple-300/50">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-cogs mr-2 text-purple-200"></i>
                                                        {{ __('messages.actions') }}
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y-2 divide-purple-400/30">
                                            @foreach(session('mangas') as $manga)
                                                <tr class="hover:bg-gradient-to-r hover:from-purple-600/20 hover:to-pink-600/20 transition-all duration-300 group">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                        <div class="bg-purple-700/50 px-4 py-2 rounded-lg border-2 border-purple-400/50 shadow-md">
                                                            <span class="font-semibold">{{ $manga['title'] }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                        <div class="flex items-center">
                                                            <div class="bg-purple-700/50 px-4 py-2 rounded-lg border-2 border-purple-400/50 shadow-md">
                                                                <span class="font-mono font-semibold">{{ $manga['isbn'] }}</span>
                                                            </div>
                                                            @if($manga['isDuplicate'])
                                                                <span class="inline-block ml-3 text-red-200 cursor-help bg-red-700/60 px-3 py-2 rounded-lg border-2 border-red-400/50 shadow-md font-bold" title="{{ __('messages.duplicate_isbn_warning') }}">⚠️</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white" id="price-{{ $loop->index }}">
                                                        <span class="text-purple-200 bg-purple-700/50 px-3 py-2 rounded-lg border-2 border-purple-400/50 shadow-md font-medium">-</span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                        <div class="flex space-x-3">
                                                            @if($manga['isbn'] === __('messages.not_found_isbn'))
                                                                <button onclick="searchIsbn('{{ addslashes($manga['title']) }}')" data-row-index="{{ $loop->index }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 border-2 border-blue-400/50 shadow-lg">
                                                                    <i class="fas fa-search mr-1"></i>
                                                                    {{ __('messages.search') }}
                                                                </button>
                                                            @else
                                                                <button onclick="verifyMangaIsbn('{{ addslashes($manga['isbn']) }}', '{{ addslashes($manga['title']) }}')" data-row-index="{{ $loop->index }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 border-2 border-blue-400/50 shadow-lg" title="{{ __('messages.verify_isbn_tooltip') }}">
                                                                    <i class="fas fa-check-circle mr-1"></i>
                                                                    {{ __('messages.verify_isbn') }}
                                                                </button>
                                                            @endif
                                                            <button onclick="editIsbn('{{ addslashes($manga['title']) }}', '{{ addslashes($manga['isbn']) }}')" data-row-index="{{ $loop->index }}" class="bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 border-2 border-yellow-400/50 shadow-lg" title="{{ __('messages.edit_isbn_tooltip') }}">
                                                                <i class="fas fa-edit mr-1"></i>
                                                                ✏️
                                                            </button>
                                                            <button onclick="removeManga('{{ addslashes($manga['title']) }}')" data-row-index="{{ $loop->index }}" class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 border-2 border-red-400/50 shadow-lg" title="{{ __('messages.remove_manga_tooltip') }}">
                                                                <i class="fas fa-trash mr-1"></i>
                                                                🗑️
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Résumé global -->
                                <div id="globalSummary" class="mt-6 bg-gradient-to-br from-purple-900/60 via-pink-900/60 to-yellow-900/60 backdrop-blur-lg rounded-xl p-6 border-2 border-purple-400/40 shadow-2xl hidden">
                                    <div class="flex items-center mb-6">
                                        <div class="bg-gradient-to-br from-purple-500/50 to-pink-500/50 rounded-full p-4 mr-4 border-2 border-purple-300/50 shadow-lg">
                                            <i class="fas fa-chart-bar text-white text-xl"></i>
                                        </div>
                                        <h4 class="text-xl font-bold text-white drop-shadow-lg">{{ __('messages.global_lot_estimation') }}</h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="group transform hover:scale-105 transition-all duration-300">
                                            <div class="bg-gradient-to-br from-blue-700/50 to-cyan-700/50 rounded-xl p-5 border-2 border-blue-400/50 backdrop-blur-sm shadow-xl">
                                                <div class="flex items-center">
                                                    <div class="bg-gradient-to-br from-blue-500/70 to-cyan-500/70 rounded-full p-3 mr-4 border-2 border-blue-300/50 shadow-lg">
                                                        <i class="fas fa-books text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm text-blue-100 font-medium">{{ __('messages.manga_count') }}</div>
                                                        <div id="totalMangas" class="text-3xl font-bold text-white drop-shadow-lg">0</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="group transform hover:scale-105 transition-all duration-300">
                                            <div class="bg-gradient-to-br from-green-700/50 to-emerald-700/50 rounded-xl p-5 border-2 border-green-400/50 backdrop-blur-sm shadow-xl">
                                                <div class="flex items-center">
                                                    <div class="bg-gradient-to-br from-green-500/70 to-emerald-500/70 rounded-full p-3 mr-4 border-2 border-green-300/50 shadow-lg">
                                                        <i class="fas fa-euro-sign text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm text-green-100 font-medium">{{ __('messages.total_estimated_price') }}</div>
                                                        <div id="totalPrice" class="text-3xl font-bold text-white drop-shadow-lg">0,00 €</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="group transform hover:scale-105 transition-all duration-300">
                                            <div class="bg-gradient-to-br from-purple-700/50 to-pink-700/50 rounded-xl p-5 border-2 border-purple-400/50 backdrop-blur-sm shadow-xl">
                                                <div class="flex items-center">
                                                    <div class="bg-gradient-to-br from-purple-500/70 to-pink-500/70 rounded-full p-3 mr-4 border-2 border-purple-300/50 shadow-lg">
                                                        <i class="fas fa-calculator text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm text-purple-100 font-medium">{{ __('messages.average_price') }}</div>
                                                        <div id="averagePrice" class="text-3xl font-bold text-white drop-shadow-lg">0,00 €</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Message quand aucun manga n'est détecté -->
                            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-yellow-800">{{ __('messages.no_manga_detected') }}</h3>
                                </div>
                                <p class="text-yellow-700 mb-4">
                                    {{ __('messages.no_manga_detected_explanation') }}
                                </p>
                                <ul class="text-yellow-700 space-y-2 mb-6">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-yellow-500 mr-2 mt-1"></i>
                                        <span>{{ __('messages.image_quality_issue') }}</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-yellow-500 mr-2 mt-1"></i>
                                        <span>{{ __('messages.manga_not_visible') }}</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-yellow-500 mr-2 mt-1"></i>
                                        <span>{{ __('messages.no_manga_in_image') }}</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-yellow-500 mr-2 mt-1"></i>
                                        <span>{{ __('messages.covers_partially_hidden') }}</span>
                                    </li>
                                </ul>
                                <div class="bg-white p-4 rounded-lg border border-yellow-200">
                                    <h4 class="font-semibold text-yellow-800 mb-2">{{ __('messages.detection_improvement_suggestions') }} :</h4>
                                    <ul class="text-yellow-700 space-y-1 text-sm">
                                        <li>• {{ __('messages.ensure_good_lighting') }}</li>
                                        <li>• {{ __('messages.photograph_front_covers') }}</li>
                                        <li>• {{ __('messages.avoid_reflections_shadows') }}</li>
                                        <li>• {{ __('messages.take_in_well_lit_environment') }}</li>
                                        <li>• {{ __('messages.verify_manga_content') }}</li>
                                    </ul>
                                </div>
                                <div class="mt-4 flex justify-center">
                                    <button onclick="document.getElementById('uploadForm').scrollIntoView({behavior: 'smooth'})" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                        <i class="fas fa-upload mr-2"></i>
                                        {{ __('messages.try_another_image') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif

                    <form action="{{ \App\Helpers\LocalizedRoute::localized('image.upload.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="uploadForm">
                        @csrf
                        
                        <div>
                            <x-input-label for="image" :value="__('messages.select_lot_image')" />
                            <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button id="uploadButton">
                                <i class="fas fa-upload mr-2" id="uploadIcon"></i>
                                <span id="buttonText">{{ __('messages.analyze_image') }}</span>
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Loading Overlay (caché par défaut) -->
                    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('messages.loading') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('messages.ai_analysis_description') }}</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-eye text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.auto_isbn_search') }}</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.ai_analysis_description') }}</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-search text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.auto_isbn_search') }}</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.estimated_wait_time') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-gradient-to-br from-purple-900/60 via-pink-900/60 to-yellow-900/60 backdrop-blur-lg rounded-xl p-6 border-2 border-purple-400/40 shadow-2xl">
                        <div class="flex items-center mb-6">
                            <div class="bg-gradient-to-br from-purple-500/50 to-pink-500/50 rounded-full p-4 mr-4 border-2 border-purple-300/50 shadow-lg">
                                <i class="fas fa-info-circle text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ __('messages.information') }} :</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center text-white bg-purple-800/30 rounded-lg p-3 border border-purple-400/50 shadow-md">
                                    <div class="bg-purple-500/60 rounded-full p-3 mr-4 border-2 border-purple-300/50 shadow-md">
                                        <i class="fas fa-image text-white"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ __('messages.accepted_formats') }}</span>
                                </div>
                                <div class="flex items-center text-white bg-purple-800/30 rounded-lg p-3 border border-purple-400/50 shadow-md">
                                    <div class="bg-purple-500/60 rounded-full p-3 mr-4 border-2 border-purple-300/50 shadow-md">
                                        <i class="fas fa-weight-hanging text-white"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ __('messages.max_size') }}</span>
                                </div>
                                <div class="flex items-center text-white bg-purple-800/30 rounded-lg p-3 border border-purple-400/50 shadow-md">
                                    <div class="bg-purple-500/60 rounded-full p-3 mr-4 border-2 border-purple-300/50 shadow-md">
                                        <i class="fas fa-robot text-white"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ __('messages.ai_analysis_description') }}</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center text-white bg-green-800/30 rounded-lg p-3 border border-green-400/50 shadow-md">
                                    <div class="bg-green-500/60 rounded-full p-3 mr-4 border-2 border-green-300/50 shadow-md">
                                        <i class="fas fa-search text-white"></i>
                                    </div>
                                    <span class="text-sm font-bold">{{ __('messages.auto_isbn_search') }}</span>
                                </div>
                                <div class="flex items-center text-white bg-blue-800/30 rounded-lg p-3 border border-blue-400/50 shadow-md">
                                    <div class="bg-blue-500/60 rounded-full p-3 mr-4 border-2 border-blue-300/50 shadow-md">
                                        <i class="fas fa-bolt text-white"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ __('messages.isbn_found_search') }}</span>
                                </div>
                                <div class="flex items-center text-white bg-orange-800/30 rounded-lg p-3 border border-orange-400/50 shadow-md">
                                    <div class="bg-orange-500/60 rounded-full p-3 mr-4 border-2 border-orange-300/50 shadow-md">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ __('messages.analysis_time') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal d'édition ISBN -->
                    <div id="editIsbnModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-edit text-yellow-500 text-2xl mr-3"></i>
                                <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.edit_isbn_modal') }}</h3>
                            </div>
                            
                            <div class="mb-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.manga_title') }} :</label>
                                    <p id="editMangaTitle" class="text-gray-900 font-medium bg-gray-50 p-2 rounded"></p>
                                </div>
                                <div class="mb-4">
                                    <label for="editIsbnInput" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.isbn') }} :</label>
                                    <input type="text" 
                                           id="editIsbnInput" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="{{ __('messages.isbn_placeholder') }}">
                                </div>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        id="saveIsbn"
                                        class="flex-1 bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('messages.save') }}
                                </button>
                                <button type="button" 
                                        id="cancelEdit"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('messages.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de confirmation suppression -->
                    <div id="deleteMangaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                                <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.confirm_deletion') }}</h3>
                            </div>
                            
                            <div class="mb-6">
                                <p class="text-gray-600">{{ __('messages.confirm_delete_manga') }}</p>
                                <p id="deleteMangaTitle" class="text-gray-900 font-medium mt-2 bg-gray-50 p-2 rounded"></p>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        id="confirmDelete"
                                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    {{ __('messages.delete_manga') }}
                                </button>
                                <button type="button" 
                                        id="cancelDelete"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('messages.cancel_delete') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Overlay pour la recherche globale -->
                    <div id="searchAllOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-green-600 mx-auto mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('messages.searching_prices') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('messages.retrieving_prices_for_all_mangas') }}</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center">
                                    <i class="fab fa-amazon text-orange-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.amazon') }}</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-store text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.cultura') }}</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-red-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ __('messages.fnac') }}</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-clock text-green-500 mr-2"></i>
                                    <span id="searchProgress" class="text-sm text-gray-600">{{ __('messages.preparing') }}</span>
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
                                    <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.search_results') }}</h3>
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
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">{{ __('messages.title') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">{{ __('messages.isbn') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">{{ __('messages.found_price') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">{{ __('messages.status') }}</th>
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
                                    <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.manga_details') }}</h3>
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
                                    {{ __('messages.confirm') }}
                                </button>
                                <button type="button" 
                                        onclick="closeMangaDetails()"
                                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('messages.cancel') }}
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
            
            // Fonction pour réinitialiser le bouton
            function resetUploadButton() {
                if (uploadButton) {
                    uploadButton.disabled = false;
                    uploadButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    uploadButton.classList.add('hover:scale-105', 'hover:bg-blue-700');
                }
                if (uploadIcon) {
                    uploadIcon.className = 'fas fa-upload mr-2';
                }
                if (buttonText) {
                    buttonText.textContent = '{{ __('messages.analyze_image') }}';
                }
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }
            
            // Gestion du formulaire avec upload AJAX
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const fileInput = document.getElementById('image');
                    const file = fileInput.files[0];
                    
                    if (!file) {
                        showTemporaryMessage('{{ __('messages.please_select_image') }}', 'error');
                        return;
                    }
                    
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
                        buttonText.textContent = '{{ __('messages.analysis_in_progress') }}';
                    }
                    
                    // Afficher l'overlay
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                    }
                    
                    // Créer FormData pour l'upload
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    // Upload AJAX
                    fetch('{{ \App\Helpers\LocalizedRoute::localized("image.upload.ajax") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            // Erreur détectée
                            resetUploadButton();
                            showTemporaryMessage(data.error, 'error');
                        } else if (data.success) {
                            // Succès - rediriger vers la page d'upload pour afficher les résultats
                            window.location.href = data.redirect;
                        } else {
                            // Réponse inattendue
                            resetUploadButton();
                            showTemporaryMessage('{{ __('messages.unexpected_server_response') }}', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'upload:', error);
                        resetUploadButton();
                        showTemporaryMessage('{{ __('messages.connection_error_analysis') }}', 'error');
                    });
                });
            }
            
            // Vérifier s'il y a des erreurs au chargement de la page
            const errorMessages = document.querySelectorAll('.bg-red-100, .text-red-700');
            if (errorMessages.length > 0) {
                // S'il y a des erreurs, masquer la popup de chargement
                resetUploadButton();
            }
            
            // Gestion des erreurs de navigation
            window.addEventListener('beforeunload', function() {
                // Masquer la popup si l'utilisateur quitte la page
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            });
        });

        function searchIsbn(title) {
            fetch('{{ \App\Helpers\LocalizedRoute::localized("image.search.isbn") }}', {
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
                    alert(`{{ __('messages.isbn_found_for') }} "${title}": ${data.isbn}`);
                } else {
                    alert(`{{ __('messages.no_isbn_found_for') }} "${title}"`);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('{{ __('messages.error_searching_isbn') }}');
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
                    alert('{{ __('messages.please_enter_valid_isbn') }}');
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
            
            rows.forEach((row, index) => {
                const titleCell = row.querySelector('td:first-child');
                if (titleCell && titleCell.textContent.trim() === title) {
                    const isbnCell = row.querySelector('td:nth-child(2)');
                    if (isbnCell) {
                        // Mettre à jour l'ISBN
                        isbnCell.innerHTML = newIsbn;
                        
                        // Trouver la cellule des boutons
                        const buttonCell = row.querySelector('td:nth-child(4)');
                        if (buttonCell) {
                            // Vider complètement la cellule des boutons
                            buttonCell.innerHTML = '';
                            
                            // Échapper les caractères spéciaux
                            const escapedTitle = title.replace(/'/g, "\\'");
                            const escapedIsbn = newIsbn.replace(/'/g, "\\'");
                            
                            // Créer le bon bouton selon l'ISBN
                            if (newIsbn === '{{ __('messages.not_found_isbn') }}') {
                                // Bouton "Rechercher"
                                const searchButton = document.createElement('button');
                                searchButton.className = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2';
                                searchButton.innerHTML = '{{ __('messages.search') }}';
                                searchButton.setAttribute('data-row-index', index);
                                searchButton.addEventListener('click', function() {
                                    searchIsbn(title);
                                });
                                buttonCell.appendChild(searchButton);
                            } else {
                                // Bouton "Vérifier l'ISBN"
                                const verifyButton = document.createElement('button');
                                verifyButton.className = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2';
                                verifyButton.title = '{{ __('messages.verify_isbn_tooltip') }}';
                                verifyButton.innerHTML = '<i class="fas fa-check-circle mr-1"></i>{{ __('messages.verify_isbn') }}';
                                verifyButton.setAttribute('data-row-index', index);
                                verifyButton.addEventListener('click', function() {
                                    verifyMangaIsbn(newIsbn, title);
                                });
                                buttonCell.appendChild(verifyButton);
                            }
                            
                            // Ajouter le bouton d'édition
                            const editButton = document.createElement('button');
                            editButton.className = 'bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2';
                            editButton.title = '{{ __('messages.edit_isbn_tooltip') }}';
                            editButton.innerHTML = '✏️';
                            editButton.setAttribute('data-row-index', index);
                            editButton.addEventListener('click', function() {
                                editIsbn(title, newIsbn);
                            });
                            buttonCell.appendChild(editButton);
                            
                            // Ajouter le bouton de suppression
                            const deleteButton = document.createElement('button');
                            deleteButton.className = 'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded';
                            deleteButton.title = '{{ __('messages.remove_manga_tooltip') }}';
                            deleteButton.innerHTML = '🗑️';
                            deleteButton.setAttribute('data-row-index', index);
                            deleteButton.addEventListener('click', function() {
                                removeManga(title);
                            });
                            buttonCell.appendChild(deleteButton);
                        }
                        
                        // Vérifier s'il y a des doublons
                        checkForDuplicates();
                    }
                    updated = true;
                }
            });
            
            if (updated) {
                // Afficher un message de succès temporaire
                showTemporaryMessage('{{ __('messages.isbn_updated_successfully') }}', 'success');
            }
        }

        function removeMangaFromTable(title) {
            // Trouver et supprimer la ligne du tableau
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const titleCell = row.querySelector('td:first-child');
                if (titleCell && titleCell.textContent.trim() === title) {
                    row.remove();
                    
                    // Mettre à jour tous les data-row-index après suppression
                    updateRowIndices();
                    
                    // Vérifier s'il y a des doublons après suppression
                    checkForDuplicates();
                    
                    // Mettre à jour le compteur du bouton de recherche globale
                    checkAllMangasValidated();
                    
                    // Afficher un message de succès temporaire
                    showTemporaryMessage('{{ __('messages.manga_removed_from_list') }}', 'success');
                    return;
                }
            });
        }

        function updateRowIndices() {
            // Mettre à jour tous les data-row-index après suppression
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach((row, newIndex) => {
                // Mettre à jour tous les boutons dans cette ligne
                const buttons = row.querySelectorAll('button[data-row-index]');
                buttons.forEach(button => {
                    button.setAttribute('data-row-index', newIndex);
                });
            });
        }

        function checkForDuplicates() {
            const isbnCells = document.querySelectorAll('tbody td:nth-child(2)');
            const isbnCounts = {};
            
            // Compter les occurrences de chaque ISBN
            isbnCells.forEach(cell => {
                const isbn = cell.textContent.trim();
                if (isbn && isbn !== '{{ __('messages.not_found_isbn') }}' && isbn !== '{{ __('messages.search_error') }}') {
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
                        indicator.title = '{{ __('messages.duplicate_isbn_warning') }}';
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
                showTemporaryMessage('{{ __('messages.all_mangas_must_be_validated') }}', 'error');
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
            searchAllText.textContent = '{{ __('messages.search_in_progress') }}';
            searchAllOverlay.style.display = 'flex';
            
            // Récupérer tous les mangas validés
            const validMangas = [];
            
            rows.forEach((row, index) => {
                const isbnCell = row.querySelector('td:nth-child(2)');
                const isbn = isbnCell.textContent.trim().replace(/⚠️/g, '').trim();
                
                if (isbn && isbn !== '{{ __('messages.not_found_isbn') }}' && isbn !== '{{ __('messages.search_error') }}' && row.classList.contains('bg-green-50')) {
                    validMangas.push({
                        title: row.querySelector('td:first-child').textContent.trim(),
                        isbn: isbn
                    });
                }
            });
            
            if (validMangas.length === 0) {
                showTemporaryMessage('{{ __('messages.no_validated_manga_found') }}', 'error');
                resetSearchButton();
                searchAllOverlay.style.display = 'none';
                return;
            }
            
            // Mettre à jour le progrès
            searchProgress.textContent = `{{ __('messages.sending_mangas_to_server') }} ${validMangas.length} {{ __('messages.mangas') }}...`;
            
            try {
                // Envoyer les données au backend
                const response = await fetch('{{ \App\Helpers\LocalizedRoute::localized("image.search.all.prices") }}', {
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
                        showTemporaryMessage('{{ __('messages.search_error') }}: ' + (data.error || '{{ __('messages.unknown_error') }}'), 'error');
                        resetSearchButton();
                        searchAllOverlay.style.display = 'none';
                    }
                } else {
                    showTemporaryMessage('{{ __('messages.server_communication_error') }}', 'error');
                    resetSearchButton();
                    searchAllOverlay.style.display = 'none';
                }
                
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
                showTemporaryMessage('{{ __('messages.connection_error_search') }}', 'error');
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
                        statusText = '{{ __('messages.price_found') }}';
                        statusClass = 'text-green-600';
                        break;
                    case 'not_found':
                        statusText = '{{ __('messages.not_found') }}';
                        statusClass = 'text-red-500';
                        break;
                    case 'error':
                        statusText = '{{ __('messages.error') }}';
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
            searchAllText.textContent = '{{ __('messages.search_all_prices') }}';
        }

        async function searchPriceForIsbn(isbn) {
            try {
                const response = await fetch('{{ \App\Helpers\LocalizedRoute::localized("image.search.price") }}', {
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
                // Trouver l'index de la ligne du bouton cliqué via l'événement
                let rowIndex = -1;
                
                // Récupérer l'index depuis l'attribut data-row-index du bouton cliqué
                const event = window.event || arguments.callee.caller.arguments[0];
                if (event && event.target && event.target.hasAttribute('data-row-index')) {
                    rowIndex = parseInt(event.target.getAttribute('data-row-index'));
                } else {
                    // Fallback: chercher par onclick (pour les boutons originaux)
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach((row, index) => {
                        const verifyButton = row.querySelector('button[onclick*="verifyMangaIsbn"]');
                        if (verifyButton && verifyButton.onclick && verifyButton.onclick.toString().includes(isbn)) {
                            rowIndex = index;
                        }
                    });
                }
                
                console.log('Index de la ligne trouvé:', rowIndex);
                
                const response = await fetch('{{ \App\Helpers\LocalizedRoute::localized("price.verify.isbn") }}', {
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
                        // Afficher la modal de confirmation avec les détails du livre et l'index
                        showMangaConfirmationModal(data, rowIndex);
                    } else {
                        // Afficher l'erreur
                        showTemporaryMessage(`{{ __('messages.error') }}: ${data.message}`, 'error');
                    }
                } else {
                    showTemporaryMessage('{{ __('messages.error_verifying_isbn') }}', 'error');
                }
            } catch (error) {
                console.error('Erreur lors de la vérification:', error);
                showTemporaryMessage('{{ __('messages.connection_error_verification') }}', 'error');
            }
        }

        function showMangaConfirmationModal(data, rowIndex) {
            // Stocker l'index de la ligne dans la modal pour la validation
            document.getElementById('mangaDetailsModal').setAttribute('data-row-index', rowIndex);
            
            // Remplir le contenu de la modal avec les détails du livre
            document.getElementById('mangaDetailsContent').innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">{{ __('messages.book_information') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.title') }}</label>
                                <p class="text-gray-900 font-medium">${data.title}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.author') }}</label>
                                <p class="text-gray-900">${data.author || '{{ __('messages.unknown_author') }}'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.publisher') }}</label>
                                <p class="text-gray-900">${data.publisher || '{{ __('messages.unknown_publisher') }}'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.isbn') }}</label>
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
                const response = await fetch('{{ \App\Helpers\LocalizedRoute::localized("image.search.price") }}', {
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
                    displayMangaPrices({ success: false, error: '{{ __('messages.error_loading') }}' });
                }
            } catch (error) {
                console.error('Erreur lors du chargement des prix:', error);
                displayMangaPrices({ success: false, error: '{{ __('messages.connection_error') }}' });
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
                            <span class="font-medium text-gray-700">{{ __('messages.used') }}</span>
                            <span class="font-bold text-orange-600">${data.occasion_price.toFixed(2)} €</span>
                        </div>
                    `;
                }
                
                if (!hasValidPrice) {
                    pricesHtml = `
                        <div class="text-center text-gray-500 py-4">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mb-2"></i>
                            <p>{{ __('messages.no_price_found_for_manga') }}</p>
                        </div>
                    `;
                }
                
                pricesContainer.innerHTML = pricesHtml;
            } else {
                pricesContainer.innerHTML = `
                    <div class="text-center text-red-500 py-4">
                        <i class="fas fa-times-circle text-xl mb-2"></i>
                        <p>{{ __('messages.error_loading_prices') }}</p>
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
                const rowIndex = document.getElementById('mangaDetailsModal').getAttribute('data-row-index');
                
                if (titleElement && isbnElement && rowIndex !== null) {
                    const title = titleElement.textContent;
                    const isbn = isbnElement.textContent;
                    
                    // Marquer le manga comme validé dans le tableau en utilisant l'index
                    markMangaAsValidatedByIndex(parseInt(rowIndex), title, isbn);
                    
                    // Fermer la modal
                    closeMangaDetails();
                    
                    // Afficher un message de succès
                    showTemporaryMessage('{{ __('messages.manga_validated_successfully') }}', 'success');
                }
            });
        });

        function markMangaAsValidatedByIndex(index, title, isbn) {
            console.log('Tentative de validation pour l\'index:', index, 'Titre:', title, 'ISBN:', isbn);
            
            // Trouver la ligne du tableau par index
            const rows = document.querySelectorAll('tbody tr');
            
            // Vérifier s'il y a des lignes et si l'index est valide
            if (rows.length === 0) {
                console.log('Aucune ligne trouvée dans le tableau, pas encore de résultats d\'analyse');
                return;
            }
            
            if (index < 0 || index >= rows.length) {
                console.error('Index invalide:', index, 'Nombre de lignes:', rows.length);
                showTemporaryMessage('{{ __('messages.invalid_row_index_error') }}', 'error');
                return;
            }
            
            // Récupérer la ligne par index
            const row = rows[index];
            const titleCell = row.querySelector('td:first-child');
            const rowTitle = titleCell ? titleCell.textContent.trim() : '';
            
            console.log(`Validation de la ligne ${index}: "${rowTitle}" avec ISBN "${isbn}"`);
            
            // Ajouter une classe pour indiquer que le manga est validé
            row.classList.add('bg-green-50', 'border-l-4', 'border-green-500');
            
            // Ajouter un indicateur visuel
            const actionsCell = row.querySelector('td:nth-child(4)');
            if (actionsCell) {
                // Vérifier si l'indicateur existe déjà
                if (!actionsCell.querySelector('.validation-indicator')) {
                    const indicator = document.createElement('span');
                    indicator.className = 'inline-block ml-2 text-green-500 validation-indicator';
                    indicator.title = '{{ __('messages.manga_validated') }}';
                    indicator.innerHTML = '<i class="fas fa-check-circle"></i>';
                    actionsCell.appendChild(indicator);
                }
            }
            
            // Désactiver le bouton "Vérifier l'ISBN" et le remplacer par un indicateur
            const verifyButton = row.querySelector('button[onclick*="verifyMangaIsbn"]') || 
                                row.querySelector('button[title*="Vérifier l\'ISBN"]');
            if (verifyButton) {
                verifyButton.disabled = true;
                verifyButton.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                verifyButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                verifyButton.innerHTML = '<i class="fas fa-check mr-1"></i>{{ __('messages.validated') }}';
                verifyButton.onclick = null;
                // Supprimer tous les event listeners
                const newButton = verifyButton.cloneNode(true);
                verifyButton.parentNode.replaceChild(newButton, verifyButton);
            }
            
            // Supprimer les boutons Éditer et Supprimer
            const editButton = row.querySelector('button[onclick*="editIsbn"]') || 
                              row.querySelector('button[title*="Modifier l\'ISBN"]');
            if (editButton) {
                editButton.remove();
            }
            
            const deleteButton = row.querySelector('button[onclick*="removeManga"]') || 
                                row.querySelector('button[title*="Supprimer de la liste"]');
            if (deleteButton) {
                deleteButton.remove();
            }
            
            console.log('Validation terminée, vérification du compteur...');
            // Vérifier si tous les mangas sont maintenant validés
            checkAllMangasValidated();
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
                if (isbn && isbn !== '{{ __('messages.not_found_isbn') }}' && isbn !== '{{ __('messages.search_error') }}') {
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
                searchAllText.textContent = '{{ __('messages.search_all_prices') }}';
                
                // Afficher un message de succès
                showTemporaryMessage('{{ __('messages.all_mangas_validated_global_search_available') }}', 'success');
            } else {
                // Garder le bouton désactivé
                searchAllButton.disabled = true;
                searchAllButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                searchAllButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                searchAllIcon.className = 'fas fa-lock mr-2';
                searchAllText.textContent = `{{ __('messages.validate_all_mangas_first') }} (${validatedMangas}/${totalMangas})`;
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