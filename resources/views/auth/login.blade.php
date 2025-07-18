<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>MangaValueCheck Beta</title>

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

    <!-- Contenu principal -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 mx-auto">
        <div class="w-full max-w-md mx-auto">
            <!-- Logo et titre -->
            <div class="text-center mb-6">
                <h1 class="text-4xl font-bold text-white mb-2 bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                    MangaValueCheck <span class="text-sm bg-red-500 text-white px-2 py-1 rounded-full ml-2">Beta</span>
                </h1>
                <p class="text-purple-200 text-lg font-medium">
                    {{ __('messages.comparator_de_prix_manga') }}
                </p>
            </div>

            <!-- Carte de connexion -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />



                <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('messages.email')" class="text-white font-semibold" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-purple-300"></i>
                            </div>
                            <x-text-input id="email" 
                                         class="block w-full pl-10 pr-3 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-purple-200 focus:bg-white/20 focus:border-purple-400 focus:ring-2 focus:ring-purple-400/50 transition-all duration-300" 
                                         type="email" 
                                         name="email" 
                                         :value="old('email')" 
                                         required 
                                         autofocus 
                                         autocomplete="username"
                                         placeholder="votre@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('messages.password')" class="text-white font-semibold" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-purple-300"></i>
                            </div>
                            <x-text-input id="password" 
                                         class="block w-full pl-10 pr-3 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-purple-200 focus:bg-white/20 focus:border-purple-400 focus:ring-2 focus:ring-purple-400/50 transition-all duration-300" 
                                         type="password"
                                         name="password"
                                         required 
                                         autocomplete="current-password"
                                         placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   class="rounded border-white/30 text-purple-600 shadow-sm focus:ring-purple-500 bg-white/10" 
                                   name="remember">
                            <span class="ms-2 text-sm text-white">{{ __('messages.remember_me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-purple-200 hover:text-white transition-colors duration-200 underline" 
                               href="{{ \App\Helpers\LocalizedRoute::localized('password.request') }}">
                                {{ __('messages.forgot_password') }}
                            </a>
                        @endif
                    </div>

                    <!-- Bouton de connexion -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ __('messages.log_in') }}</span>
                        </button>
                    </div>

                    <!-- Lien vers inscription -->
                    <div class="text-center mt-4">
                        <a class="text-sm text-purple-200 hover:text-white transition-colors duration-200 underline" 
                           href="{{ \App\Helpers\LocalizedRoute::localized('register') }}">
                            {{ __('messages.not_registered_yet') }}
                        </a>
                    </div>
                </form>

                <!-- Footer avec icônes manga -->
                <div class="mt-8 pt-6 border-t border-white/20">
                    <div class="flex justify-center space-x-6 text-white/60">
                        <div class="flex items-center space-x-2">
                            <i class="fab fa-amazon text-orange-400"></i>
                            <span class="text-xs">Amazon</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-store text-blue-400"></i>
                            <span class="text-xs">Cultura</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-shopping-cart text-red-400"></i>
                            <span class="text-xs">Fnac</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Éléments décoratifs en bas -->
            <div class="text-center mt-8">
                <div class="flex justify-center space-x-4 text-white/40">
                    <i class="fas fa-star text-yellow-400 animate-pulse"></i>
                    <i class="fas fa-heart text-pink-400 animate-bounce"></i>
                    <i class="fas fa-bolt text-blue-400 animate-ping"></i>
                    <i class="fas fa-magic text-purple-400 animate-pulse"></i>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>
