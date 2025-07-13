<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('messages.manga_price_comparator') }} - {{ __('messages.manga_price_estimation') }} | MangaValueCheck</title>
        <meta name="description" content="{{ __('messages.manga_price_search') }} {{ __('messages.manga_price_history') }} {{ __('messages.manga_used_price') }} {{ __('messages.manga_new_price') }}. {{ __('messages.manga_price_analysis') }} {{ __('messages.manga_price_tracker') }} {{ __('messages.manga_price_monitoring') }}.">
        <meta name="keywords" content="{{ __('messages.manga_price_comparator') }}, {{ __('messages.manga_price_estimation') }}, {{ __('messages.manga_used_price') }}, {{ __('messages.manga_price_search') }}, {{ __('messages.manga_price_history') }}, {{ __('messages.manga_price_analysis') }}, {{ __('messages.manga_price_tracker') }}, {{ __('messages.manga_price_monitoring') }}, {{ __('messages.manga_price_alert') }}, {{ __('messages.manga_price_trend') }}, {{ __('messages.manga_price_guide') }}, {{ __('messages.manga_price_database') }}, {{ __('messages.manga_price_calculator') }}, {{ __('messages.manga_price_estimator') }}, {{ __('messages.manga_price_checker') }}, {{ __('messages.manga_price_finder') }}, {{ __('messages.manga_price_scanner') }}, {{ __('messages.manga_price_analyzer') }}, {{ __('messages.manga_price_research') }}, {{ __('messages.manga_price_investigation') }}, {{ __('messages.manga_price_study') }}, {{ __('messages.manga_price_report') }}, {{ __('messages.manga_price_statistics') }}, {{ __('messages.manga_price_data') }}, {{ __('messages.manga_price_information') }}, {{ __('messages.manga_price_details') }}, {{ __('messages.manga_price_summary') }}, {{ __('messages.manga_price_overview') }}, {{ __('messages.manga_price_comparison_tool') }}, {{ __('messages.manga_price_estimation_service') }}, {{ __('messages.manga_price_analysis_tool') }}, {{ __('messages.manga_price_tracking_system') }}, {{ __('messages.manga_price_monitoring_service') }}, {{ __('messages.manga_price_alert_system') }}, {{ __('messages.manga_price_guide_service') }}, {{ __('messages.manga_price_database_system') }}, {{ __('messages.manga_price_calculator_tool') }}, {{ __('messages.manga_price_estimator_service') }}, {{ __('messages.manga_price_checker_tool') }}, {{ __('messages.manga_price_finder_service') }}, {{ __('messages.manga_price_scanner_tool') }}, {{ __('messages.manga_price_analyzer_service') }}, {{ __('messages.manga_price_research_tool') }}, {{ __('messages.manga_price_investigation_service') }}, {{ __('messages.manga_price_study_tool') }}, {{ __('messages.manga_price_report_service') }}, {{ __('messages.manga_price_statistics_tool') }}, {{ __('messages.manga_price_data_service') }}, {{ __('messages.manga_price_information_tool') }}, {{ __('messages.manga_price_details_service') }}, {{ __('messages.manga_price_summary_tool') }}, {{ __('messages.manga_price_overview_tool') }}, {{ __('messages.what_is_my_manga_worth') }}, {{ __('messages.manga_price_evaluation') }}, {{ __('messages.manga_price_assessment') }}, {{ __('messages.manga_price_appraisal') }}, {{ __('messages.manga_price_quote') }}, {{ __('messages.manga_price_estimate') }}, {{ __('messages.manga_price_valuation') }}, {{ __('messages.manga_price_analysis_report') }}, {{ __('messages.manga_price_research_report') }}, {{ __('messages.manga_price_study_report') }}, {{ __('messages.manga_price_investigation_report') }}, {{ __('messages.manga_price_statistics_report') }}, {{ __('messages.manga_price_data_report') }}, {{ __('messages.manga_price_information_report') }}, {{ __('messages.manga_price_details_report') }}, {{ __('messages.manga_price_summary_report') }}, {{ __('messages.manga_price_overview_report') }}, {{ __('messages.manga_price_comparison_report') }}, {{ __('messages.manga_price_estimation_report') }}, {{ __('messages.manga_price_analysis_service') }}, {{ __('messages.manga_price_research_service') }}, {{ __('messages.manga_price_study_service') }}, {{ __('messages.manga_price_investigation_service') }}, {{ __('messages.manga_price_statistics_service') }}, {{ __('messages.manga_price_data_service') }}, {{ __('messages.manga_price_information_service') }}, {{ __('messages.manga_price_details_service') }}, {{ __('messages.manga_price_summary_service') }}, {{ __('messages.manga_price_overview_service') }}, {{ __('messages.manga_price_comparison_service') }}, {{ __('messages.manga_price_estimation_service') }}, {{ __('messages.manga_price_analysis_tool') }}, {{ __('messages.manga_price_research_tool') }}, {{ __('messages.manga_price_study_tool') }}, {{ __('messages.manga_price_investigation_tool') }}, {{ __('messages.manga_price_statistics_tool') }}, {{ __('messages.manga_price_data_tool') }}, {{ __('messages.manga_price_information_tool') }}, {{ __('messages.manga_price_details_tool') }}, {{ __('messages.manga_price_summary_tool') }}, {{ __('messages.manga_price_overview_tool') }}, {{ __('messages.manga_price_comparison_tool') }}, {{ __('messages.manga_price_estimation_tool') }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ __('messages.manga_price_comparator') }} - {{ __('messages.manga_price_estimation') }} | MangaValueCheck">
        <meta property="og:description" content="{{ __('messages.manga_price_search') }} {{ __('messages.manga_price_history') }} {{ __('messages.manga_used_price') }} {{ __('messages.manga_new_price') }}. {{ __('messages.manga_price_analysis') }} {{ __('messages.manga_price_tracker') }} {{ __('messages.manga_price_monitoring') }}.">
        <meta property="og:image" content="{{ asset('images/mangavaluecheck_logo.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="{{ __('messages.manga_price_comparator') }} - {{ __('messages.manga_price_estimation') }} | MangaValueCheck">
        <meta property="twitter:description" content="{{ __('messages.manga_price_search') }} {{ __('messages.manga_price_history') }} {{ __('messages.manga_used_price') }} {{ __('messages.manga_new_price') }}. {{ __('messages.manga_price_analysis') }} {{ __('messages.manga_price_tracker') }} {{ __('messages.manga_price_monitoring') }}.">
        <meta property="twitter:image" content="{{ asset('images/mangavaluecheck_logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="bg-gradient-to-br from-purple-900 via-pink-800 to-indigo-900 min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white/10 backdrop-blur-lg border-b border-white/20 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-book-open text-white text-sm sm:text-lg"></i>
                            </div>
                            <span class="text-lg sm:text-xl font-bold bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                                MangaValueCheck
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-white hover:text-yellow-300 transition-colors duration-200 text-sm sm:text-base">
                                    {{ __('messages.dashboard') }}
                                </a>
                            @else
                                <a href="{{ \App\Helpers\LocalizedRoute::localized('login') }}" class="text-white hover:text-yellow-300 transition-colors duration-200 text-sm sm:text-base">
                                    {{ __('messages.login') }}
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ \App\Helpers\LocalizedRoute::localized('register') }}" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white hover:bg-white/20 hover:text-yellow-300 px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 text-sm sm:text-base">
                                        {{ __('messages.register') }}
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-24">
                <div class="text-center">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 sm:mb-6 leading-tight">
                        {{ __('messages.manga_price_comparator') }}
                    </h1>
                    <p class="text-lg sm:text-xl md:text-2xl text-purple-200 mb-6 sm:mb-8 max-w-3xl mx-auto px-4">
                        {{ __('messages.landing_hero_subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center px-4">
                        <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-semibold hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 shadow-lg">
                            {{ __('messages.manga_price_search') }}
                        </a>
                        <a href="{{ \App\Helpers\LocalizedRoute::url('manga.lot.estimation.upload.form') }}" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-semibold hover:bg-white/20 transition-all duration-300">
                            {{ __('messages.manga_lot_estimation') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-12 sm:py-16 lg:py-24 bg-white/5 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ __('messages.why_choose_us') }}
                    </h2>
                    <p class="text-lg sm:text-xl text-purple-200 max-w-2xl mx-auto px-4">
                        {{ __('messages.landing_features_description') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bolt text-white text-lg sm:text-2xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-white mb-3">{{ __('messages.smart_search') }}</h3>
                        <p class="text-purple-200 text-sm sm:text-base">
                            {{ __('messages.smart_search_description') }}
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tags text-white text-lg sm:text-2xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-white mb-3">{{ __('messages.ai_analysis') }}</h3>
                        <p class="text-purple-200 text-sm sm:text-base">
                            {{ __('messages.ai_analysis_description') }}
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6 text-center sm:col-span-2 lg:col-span-1">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-pink-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-white text-lg sm:text-2xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-white mb-3">{{ __('messages.smart_comparison') }}</h3>
                        <p class="text-purple-200 text-sm sm:text-base">
                            {{ __('messages.smart_comparison_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Content Section -->
        <div class="py-12 sm:py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 items-center">
                    <div class="order-2 lg:order-1">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-6">
                            {{ __('messages.what_is_my_manga_worth') }}
                        </h2>
                        <div class="space-y-4 text-purple-200">
                            <p class="text-base sm:text-lg">
                                {{ __('messages.manga_price_estimation') }} {{ __('messages.manga_price_analysis') }} {{ __('messages.manga_price_research') }} {{ __('messages.manga_price_investigation') }} {{ __('messages.manga_price_study') }} {{ __('messages.manga_price_report') }} {{ __('messages.manga_price_statistics') }} {{ __('messages.manga_price_data') }}.
                            </p>
                            <p class="text-base sm:text-lg">
                                {{ __('messages.manga_price_information') }} {{ __('messages.manga_price_details') }} {{ __('messages.manga_price_summary') }} {{ __('messages.manga_price_overview') }} {{ __('messages.manga_price_comparison_tool') }} {{ __('messages.manga_price_estimation_service') }} {{ __('messages.manga_price_analysis_tool') }}.
                            </p>
                            <p class="text-base sm:text-lg">
                                {{ __('messages.manga_price_tracking_system') }} {{ __('messages.manga_price_monitoring_service') }} {{ __('messages.manga_price_alert_system') }} {{ __('messages.manga_price_guide_service') }} {{ __('messages.manga_price_database_system') }} {{ __('messages.manga_price_calculator_tool') }}.
                            </p>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6 sm:p-8 order-1 lg:order-2">
                        <h3 class="text-xl sm:text-2xl font-semibold text-white mb-4">{{ __('messages.manga_price_analysis_report') }}</h3>
                        <div class="space-y-3 text-purple-200">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-3 text-sm sm:text-base"></i>
                                <span class="text-sm sm:text-base">{{ __('messages.manga_price_research_report') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-3 text-sm sm:text-base"></i>
                                <span class="text-sm sm:text-base">{{ __('messages.manga_price_study_report') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-3 text-sm sm:text-base"></i>
                                <span class="text-sm sm:text-base">{{ __('messages.manga_price_investigation_report') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-3 text-sm sm:text-base"></i>
                                <span class="text-sm sm:text-base">{{ __('messages.manga_price_statistics_report') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-3 text-sm sm:text-base"></i>
                                <span class="text-sm sm:text-base">{{ __('messages.manga_price_data_report') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="py-12 sm:py-16 lg:py-24 bg-white/5 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ __('messages.manga_price_analysis_service') }} {{ __('messages.manga_price_research_service') }}
                    </h2>
                    <p class="text-lg sm:text-xl text-purple-200 max-w-2xl mx-auto px-4">
                        {{ __('messages.manga_price_study_service') }} {{ __('messages.manga_price_investigation_service') }} {{ __('messages.manga_price_statistics_service') }} {{ __('messages.manga_price_data_service') }} {{ __('messages.manga_price_information_service') }}.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-4 sm:p-6 text-center">
                        <h4 class="text-base sm:text-lg font-semibold text-white mb-2">{{ __('messages.manga_price_details_service') }}</h4>
                        <p class="text-purple-200 text-xs sm:text-sm">{{ __('messages.manga_price_summary_service') }} {{ __('messages.manga_price_overview_service') }}.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-4 sm:p-6 text-center">
                        <h4 class="text-base sm:text-lg font-semibold text-white mb-2">{{ __('messages.manga_price_comparison_service') }}</h4>
                        <p class="text-purple-200 text-xs sm:text-sm">{{ __('messages.manga_price_estimation_service') }} {{ __('messages.manga_price_analysis_service') }}.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-4 sm:p-6 text-center">
                        <h4 class="text-base sm:text-lg font-semibold text-white mb-2">{{ __('messages.manga_price_research_service') }}</h4>
                        <p class="text-purple-200 text-xs sm:text-sm">{{ __('messages.manga_price_study_service') }} {{ __('messages.manga_price_investigation_service') }}.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-4 sm:p-6 text-center">
                        <h4 class="text-base sm:text-lg font-semibold text-white mb-2">{{ __('messages.manga_price_statistics_service') }}</h4>
                        <p class="text-purple-200 text-xs sm:text-sm">{{ __('messages.manga_price_data_service') }} {{ __('messages.manga_price_information_service') }}.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tools Section -->
        <div class="py-12 sm:py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ __('messages.manga_price_analysis_tool') }} {{ __('messages.manga_price_research_tool') }}
                    </h2>
                    <p class="text-lg sm:text-xl text-purple-200 max-w-2xl mx-auto px-4">
                        {{ __('messages.manga_price_study_tool') }} {{ __('messages.manga_price_investigation_tool') }} {{ __('messages.manga_price_statistics_tool') }} {{ __('messages.manga_price_data_tool') }} {{ __('messages.manga_price_information_tool') }}.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6">
                        <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">{{ __('messages.manga_price_details_tool') }}</h3>
                        <p class="text-purple-200 mb-4 text-sm sm:text-base">{{ __('messages.manga_price_summary_tool') }} {{ __('messages.manga_price_overview_tool') }} {{ __('messages.manga_price_comparison_tool') }} {{ __('messages.manga_price_estimation_tool') }}.</p>
                        <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" class="inline-block bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 text-sm sm:text-base">
                            {{ __('messages.manga_price_search') }}
                        </a>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6">
                        <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">{{ __('messages.manga_price_analysis_tool') }}</h3>
                        <p class="text-purple-200 mb-4 text-sm sm:text-base">{{ __('messages.manga_price_research_tool') }} {{ __('messages.manga_price_study_tool') }} {{ __('messages.manga_price_investigation_tool') }} {{ __('messages.manga_price_statistics_tool') }}.</p>
                        <a href="{{ \App\Helpers\LocalizedRoute::url('manga.lot.estimation.upload.form') }}" class="inline-block bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 text-sm sm:text-base">
                            {{ __('messages.manga_lot_estimation') }}
                        </a>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-6 sm:col-span-2 lg:col-span-1">
                        <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">{{ __('messages.manga_price_data_tool') }}</h3>
                        <p class="text-purple-200 mb-4 text-sm sm:text-base">{{ __('messages.manga_price_information_tool') }} {{ __('messages.manga_price_details_tool') }} {{ __('messages.manga_price_summary_tool') }} {{ __('messages.manga_price_overview_tool') }}.</p>
                        <a href="{{ \App\Helpers\LocalizedRoute::url('price.historique') }}" class="inline-block bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 text-sm sm:text-base">
                            {{ __('messages.history') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-black/20 backdrop-blur-lg border-t border-white/20 py-8 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-2 mb-4">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-xs sm:text-sm"></i>
                        </div>
                        <span class="text-base sm:text-lg font-bold bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                            MangaValueCheck
                        </span>
                    </div>
                    <p class="text-purple-200 mb-4 text-sm sm:text-base px-4">
                        {{ __('messages.manga_price_comparator') }} {{ __('messages.manga_price_estimation') }} {{ __('messages.manga_price_search') }} {{ __('messages.manga_price_history') }} {{ __('messages.manga_used_price') }} {{ __('messages.manga_new_price') }}.
                    </p>
                    <p class="text-purple-300 text-xs sm:text-sm px-4">
                        {{ __('messages.manga_price_analysis') }} {{ __('messages.manga_price_tracker') }} {{ __('messages.manga_price_monitoring') }} {{ __('messages.manga_price_alert') }} {{ __('messages.manga_price_trend') }} {{ __('messages.manga_price_guide') }} {{ __('messages.manga_price_database') }}.
                    </p>
                </div>
            </div>
        </footer>
    </body>
</html>
