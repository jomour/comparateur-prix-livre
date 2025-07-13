<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('messages.comparator_de_prix_manga') }} - {{ __('messages.landing_title') }} | MangaValueCheck</title>
        <meta name="description" content="{{ __('messages.landing_description') }}">
        <meta name="keywords" content="{{ __('messages.landing_keywords') }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ __('messages.comparator_de_prix_manga') }} - {{ __('messages.landing_title') }} | MangaValueCheck">
        <meta property="og:description" content="{{ __('messages.landing_description') }}">
        <meta property="og:image" content="{{ asset('images/mangavaluecheck_logo.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="{{ __('messages.comparator_de_prix_manga') }} - {{ __('messages.landing_title') }} | MangaValueCheck">
        <meta property="twitter:description" content="{{ __('messages.landing_description') }}">
        <meta property="twitter:image" content="{{ asset('images/mangavaluecheck_logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Favicon rond jaune -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23fbbf24' stroke='%23f59e0b' stroke-width='2'/><text x='50' y='60' font-family='Arial, sans-serif' font-size='35' font-weight='bold' text-anchor='middle' fill='white'>M</text></svg>">
    </head>
    <body class="font-sans antialiased">
        <!-- Background avec effet manga -->
        <div class="fixed inset-0 bg-gradient-to-br from-purple-900 via-pink-800 to-indigo-900 overflow-hidden">
            <!-- Éléments décoratifs manga -->
            <div class="absolute top-10 left-10 w-32 h-32 bg-yellow-400 rounded-full opacity-20 animate-pulse"></div>
            <div class="absolute top-20 right-20 w-24 h-24 bg-pink-400 rounded-full opacity-30 animate-bounce"></div>
            <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-blue-400 rounded-full opacity-25 animate-ping"></div>
            <div class="absolute bottom-10 right-1/3 w-20 h-20 bg-green-400 rounded-full opacity-20 animate-pulse"></div>
            
            <!-- Motifs manga -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-1/4 left-1/4 text-6xl">⚡</div>
                <div class="absolute top-1/3 right-1/4 text-5xl">💫</div>
                <div class="absolute bottom-1/4 left-1/3 text-4xl">🌟</div>
                <div class="absolute bottom-1/3 right-1/3 text-5xl">✨</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="relative z-10 bg-white/10 backdrop-blur-lg border-b border-white/20 shadow-lg" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-book-open text-white text-lg"></i>
                            </div>
                            <span class="text-xl font-bold bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                                MangaValueCheck
                            </span>
                        </div>
                    </div>
                    <!-- Desktop links -->
                    <div class="hidden sm:flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" class="text-white hover:text-yellow-300 transition-colors duration-200">
                                    {{ __('messages.dashboard') }}
                                </a>
                            @else
                                <a href="{{ \App\Helpers\LocalizedRoute::localized('login') }}" class="text-white hover:text-yellow-300 transition-colors duration-200">
                                    {{ __('messages.login') }}
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ \App\Helpers\LocalizedRoute::localized('register') }}" class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        {{ __('messages.register') }}
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                    <!-- Mobile burger -->
                    <div class="flex items-center sm:hidden">
                        <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-yellow-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-yellow-400" aria-controls="mobile-menu" aria-expanded="false">
                            <svg class="h-6 w-6" x-show="!open" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg class="h-6 w-6" x-show="open" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile menu -->
            <div class="sm:hidden" id="mobile-menu" x-show="open" x-transition>
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white/90 backdrop-blur-lg border-b border-white/20 shadow-lg">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" class="block text-gray-900 font-semibold px-3 py-2 rounded hover:bg-yellow-100">
                                {{ __('messages.dashboard') }}
                            </a>
                        @else
                            <a href="{{ \App\Helpers\LocalizedRoute::localized('login') }}" class="block text-gray-900 font-semibold px-3 py-2 rounded hover:bg-yellow-100">
                                {{ __('messages.login') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ \App\Helpers\LocalizedRoute::localized('register') }}" class="block bg-gradient-to-r from-green-600 to-blue-600 text-white px-3 py-2 rounded-xl font-semibold text-center mt-1">
                                    {{ __('messages.register') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative z-10 min-h-screen flex items-center justify-center px-4">
            <div class="max-w-7xl mx-auto text-center">
                <!-- Logo et titre principal -->
                <div class="mb-8">
                    <div class="w-24 h-24 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <i class="fas fa-book-open text-white text-4xl"></i>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 bg-gradient-to-r from-yellow-400 via-pink-400 to-purple-400 bg-clip-text text-transparent">
                        MangaValueCheck
                    </h1>
                    <p class="text-2xl md:text-3xl text-purple-200 mb-8 max-w-4xl mx-auto">
                        {{ __('messages.landing_hero_subtitle') }}
                    </p>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center mb-12">
                    <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
                       class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white px-8 py-4 rounded-xl text-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl flex items-center justify-center space-x-3">
                        <i class="fas fa-search text-2xl"></i>
                        <span>{{ __('messages.start_searching') }}</span>
                    </a>
                    <a href="{{ \App\Helpers\LocalizedRoute::url('manga.lot.estimation.upload.form') }}" 
                       class="bg-white/10 backdrop-blur-lg border border-white/20 text-white px-8 py-4 rounded-xl text-xl font-bold hover:bg-white/20 transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center justify-center space-x-3">
                        <i class="fas fa-camera text-2xl"></i>
                        <span>{{ __('messages.analyze_photos') }}</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Features Section -->
        <div class="relative z-10 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                        {{ __('messages.why_choose_us') }}
                    </h2>
                    <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                        {{ __('messages.landing_features_description') }}
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1: Sources de prix d'occasion -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-tags text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4 text-center">{{ __('messages.major_stores') }}</h3>
                        <p class="text-purple-200 text-center">
                            {{ __('messages.stores_description') }}
                        </p>
                    </div>

                    <!-- Feature 2: Reconnaissance IA -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-brain text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4 text-center">{{ __('messages.ai_recognition') }}</h3>
                        <p class="text-purple-200 text-center">
                            {{ __('messages.ai_recognition_description') }}
                        </p>
                    </div>

                    <!-- Feature 3: 24/7 -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4 text-center">{{ __('messages.available_24_7') }}</h3>
                        <p class="text-purple-200 text-center">
                            {{ __('messages.available_24_7_description') }}
                        </p>
                    </div>

                    <!-- Feature 4: Comparaison intelligente -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4 text-center">{{ __('messages.smart_comparison') }}</h3>
                        <p class="text-purple-200 text-center">
                            {{ __('messages.smart_comparison_description') }}
                        </p>
                    </div>

                    <!-- Feature 5: Historique détaillé -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-history text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4 text-center">{{ __('messages.detailed_history') }}</h3>
                        <p class="text-purple-200 text-center">
                            {{ __('messages.detailed_history_description') }}
                        </p>
                    </div>

                    <!-- Feature 6: Estimation de lots -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-camera text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4 text-center">{{ __('messages.lot_estimation') }}</h3>
                        <p class="text-purple-200 text-center">
                            {{ __('messages.lot_estimation_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stores Section -->
        <div class="relative z-10 py-12 bg-white/5 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                        {{ __('messages.trusted_stores') }}
                    </h2>
                    <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                        {{ __('messages.stores_description') }}
                    </p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Amazon -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fab fa-amazon text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Amazon</h3>
                        <p class="text-purple-200 mb-6">
                            {{ __('messages.amazon_description') }}
                        </p>
                        <div class="text-yellow-400 text-sm">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>

                    <!-- Cultura -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-store text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Cultura</h3>
                        <p class="text-purple-200 mb-6">
                            {{ __('messages.cultura_description') }}
                        </p>
                        <div class="text-yellow-400 text-sm">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>

                    <!-- Fnac -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shopping-cart text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Fnac</h3>
                        <p class="text-purple-200 mb-6">
                            {{ __('messages.fnac_description') }}
                        </p>
                        <div class="text-yellow-400 text-sm">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="relative z-10 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-8">
                    {{ __('messages.ready_to_start') }}
                </h2>

                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
                       class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white px-8 py-4 rounded-xl text-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl flex items-center justify-center space-x-3">
                        <i class="fas fa-rocket text-2xl"></i>
                        <span>{{ __('messages.get_started_now') }}</span>
                    </a>
                    @guest
                    <a href="{{ \App\Helpers\LocalizedRoute::localized('register') }}" 
                       class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-8 py-4 rounded-xl text-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl flex items-center justify-center space-x-3">
                        <i class="fas fa-user-plus text-2xl"></i>
                        <span>{{ __('messages.create_free_account') }}</span>
                    </a>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="relative z-10 bg-white/5 backdrop-blur-sm border-t border-white/20 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-2 mb-6">
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                            MangaValueCheck
                        </span>
                    </div>
                    <p class="text-purple-200 mb-6">
                        {{ __('messages.footer_description') }}
                    </p>
                    <div class="flex justify-center space-x-6 text-white/60">
                        <div class="flex items-center space-x-2">
                            <i class="fab fa-amazon text-orange-400"></i>
                            <span class="text-sm">Amazon</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-store text-blue-400"></i>
                            <span class="text-sm">Cultura</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-shopping-cart text-red-400"></i>
                            <span class="text-sm">Fnac</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html> 