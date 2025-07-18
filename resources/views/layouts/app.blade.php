<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>MangaValueCheck Beta</title>
        
        {{-- Composant SEO --}}
        <x-seo-meta :meta="$meta ?? null" :type="$seoType ?? 'website'" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        {{-- Analytics --}}
        <x-analytics />
        
        <!-- Favicon rond jaune -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23fbbf24' stroke='%23f59e0b' stroke-width='2'/><text x='50' y='60' font-family='Arial, sans-serif' font-size='35' font-weight='bold' text-anchor='middle' fill='white'>M</text></svg>">
    </head>
    <body class="font-sans antialiased">
        {{-- Google Tag Manager (noscript) --}}
        @if(config('analytics.google_tag_manager.enabled') && config('analytics.google_tag_manager.container_id'))
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('analytics.google_tag_manager.container_id') }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        @endif
        
        <!-- Background animé manga -->
        <div class="fixed inset-0 bg-gradient-to-br from-purple-900 via-pink-800 to-indigo-900 overflow-hidden z-0">
            <!-- Éléments décoratifs manga -->
            <div class="absolute top-20 left-20 w-40 h-40 bg-yellow-400 rounded-full opacity-10 animate-pulse"></div>
            <div class="absolute top-40 right-32 w-32 h-32 bg-pink-400 rounded-full opacity-15 animate-bounce"></div>
            <div class="absolute bottom-40 left-1/3 w-24 h-24 bg-blue-400 rounded-full opacity-12 animate-ping"></div>
            <div class="absolute bottom-20 right-1/4 w-28 h-28 bg-green-400 rounded-full opacity-10 animate-pulse"></div>
            
            <!-- Motifs manga subtils -->
            <div class="absolute inset-0 opacity-3">
                <div class="absolute top-1/4 left-1/4 text-8xl">⚡</div>
                <div class="absolute top-1/3 right-1/4 text-6xl">💫</div>
                <div class="absolute bottom-1/4 left-1/3 text-5xl">🌟</div>
                <div class="absolute bottom-1/3 right-1/3 text-6xl">✨</div>
            </div>
        </div>
        
        <div class="relative z-10 min-h-screen">
            <div class="relative z-20">
                @include('layouts.navigation')
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/10 backdrop-blur-lg border-b border-white/20 shadow-lg">
                    <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10 pb-4 md:pb-0">
                {{ $slot }}
            </main>
        </div>
        </div>
    </body>
</html>
