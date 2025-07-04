<!-- Navigation Desktop -->
<nav x-data="{ open: false }" class="bg-white/10 backdrop-blur-lg border-b border-white/20 shadow-lg hidden md:block">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" class="flex items-center group">
                        <span class="text-xl font-bold bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                            MangaValueCheck
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="\App\Helpers\LocalizedRoute::url('price.search')" :active="request()->routeIs('fr.comparateur.prix') || request()->routeIs('en.manga.price.comparator')" class="text-white hover:text-yellow-300 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        {{ __('messages.comparator') }}
                    </x-nav-link>
                    <x-nav-link :href="\App\Helpers\LocalizedRoute::url('price.historique')" :active="request()->routeIs('fr.historique.recherches') || request()->routeIs('en.search.history')" class="text-white hover:text-yellow-300 transition-colors duration-200">
                        <i class="fas fa-history mr-2"></i>
                        {{ __('messages.history') }}
                    </x-nav-link>
                    <x-nav-link :href="\App\Helpers\LocalizedRoute::url('image.upload.form')" :active="request()->routeIs('fr.estimation.lot.manga') || request()->routeIs('en.manga.lot.estimation')" class="text-white hover:text-yellow-300 transition-colors duration-200">
                        <i class="fas fa-camera mr-2"></i>
                        {{ __('messages.manga_lot_estimation') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4 relative z-[9999]">
                <!-- Language Switcher -->
                <x-language-switcher />
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-sm leading-4 font-medium rounded-xl text-white hover:bg-white/20 hover:text-yellow-300 focus:outline-none transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="\App\Helpers\LocalizedRoute::localized('profile.edit')" class="text-gray-700 hover:bg-purple-50">
                            <i class="fas fa-user-edit mr-2 text-purple-500"></i>
                            {{ __('messages.profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('logout') }}">
                            @csrf

                            <x-dropdown-link :href="\App\Helpers\LocalizedRoute::localized('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-gray-700 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2 text-red-500"></i>
                                {{ __('messages.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>

<!-- Navigation Mobile - Bottom Bar -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-50">
    <!-- Bottom Navigation Bar -->
    <div class="bg-white/10 backdrop-blur-lg border-t border-white/20 shadow-2xl">
        <div class="flex justify-around items-center h-16 px-4">
            <!-- Comparateur -->
            <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" 
               class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('fr.comparateur.prix') || request()->routeIs('en.manga.price.comparator') ? 'text-yellow-300' : 'text-white' }} transition-colors duration-200">
                <i class="fas fa-search text-lg mb-1"></i>
                <span class="text-xs font-medium">{{ __('messages.comparator') }}</span>
            </a>

            <!-- Historique -->
            <a href="{{ \App\Helpers\LocalizedRoute::url('price.historique') }}" 
               class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('fr.historique.recherches') || request()->routeIs('en.search.history') ? 'text-yellow-300' : 'text-white' }} transition-colors duration-200">
                <i class="fas fa-history text-lg mb-1"></i>
                <span class="text-xs font-medium">{{ __('messages.history') }}</span>
            </a>

            <!-- Estimation -->
            <a href="{{ \App\Helpers\LocalizedRoute::url('image.upload.form') }}" 
               class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('fr.estimation.lot.manga') || request()->routeIs('en.manga.lot.estimation') ? 'text-yellow-300' : 'text-white' }} transition-colors duration-200">
                <i class="fas fa-camera text-lg mb-1"></i>
                <span class="text-xs font-medium">{{ __('messages.manga_lot_estimation') }}</span>
            </a>

            <!-- Menu Utilisateur -->
            <div class="flex flex-col items-center justify-center flex-1 py-2" x-data="{ open: false }">
                <button @click="open = !open" class="flex flex-col items-center justify-center text-white hover:text-yellow-300 transition-colors duration-200">
                    <div class="w-6 h-6 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center mb-1">
                        <i class="fas fa-user text-xs text-white"></i>
                    </div>
                    <span class="text-xs font-medium">{{ __('messages.profile') }}</span>
                </button>

                <!-- Menu déroulant mobile -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute bottom-20 left-4 right-4 bg-gray-900/95 backdrop-blur-xl border border-gray-700/50 rounded-xl shadow-2xl p-4 z-[9999]"
                     @click.outside="open = false"
                     style="display: none;">
                    
                    <!-- Language Switcher Mobile -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between p-3 bg-gray-800/80 rounded-lg border border-gray-700/50">
                            <span class="text-white text-sm font-medium">{{ __('messages.language') }}</span>
                            <div class="flex space-x-2">
                                <a href="{{ \App\Helpers\LocalizedRoute::switchLanguage('fr', request()->getRequestUri()) }}" 
                                   class="px-3 py-1 rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-purple-600 text-white' : 'bg-gray-700/80 text-gray-200 hover:bg-gray-600/80' }} text-sm transition-colors duration-200">
                                    FR
                                </a>
                                <a href="{{ \App\Helpers\LocalizedRoute::switchLanguage('en', request()->getRequestUri()) }}" 
                                   class="px-3 py-1 rounded-lg {{ app()->getLocale() === 'en' ? 'bg-purple-600 text-white' : 'bg-gray-700/80 text-gray-200 hover:bg-gray-600/80' }} text-sm transition-colors duration-200">
                                    EN
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="mb-4 p-3 bg-gray-800/80 rounded-lg border border-gray-700/50">
                        <div class="text-white text-sm font-medium">{{ Auth::user()->name }}</div>
                        <div class="text-purple-300 text-xs">{{ Auth::user()->email }}</div>
                    </div>

                    <!-- Menu Items -->
                    <div class="space-y-2">
                        <a href="{{ \App\Helpers\LocalizedRoute::localized('profile.edit') }}" 
                           class="flex items-center p-3 text-white hover:bg-gray-800/80 rounded-lg transition-colors duration-200 border border-transparent hover:border-gray-600/50">
                            <i class="fas fa-user-edit mr-3 text-purple-400"></i>
                            <span class="text-sm">{{ __('messages.profile') }}</span>
                        </a>

                        <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center p-3 text-white hover:bg-gray-800/80 rounded-lg transition-colors duration-200 border border-transparent hover:border-gray-600/50">
                                <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                <span class="text-sm">{{ __('messages.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Padding pour le contenu mobile -->
<div class="md:hidden pb-16"></div>
