<nav x-data="{ open: false }" class="bg-white/10 backdrop-blur-lg border-b border-white/20 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ \App\Helpers\LocalizedRoute::url('price.search') }}" class="flex items-center space-x-2 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-book-open text-white text-lg"></i>
                        </div>
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white hover:bg-white/20 hover:text-yellow-300 focus:outline-none transition-all duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/10 backdrop-blur-lg border-t border-white/20">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="\App\Helpers\LocalizedRoute::url('price.search')" :active="request()->routeIs('fr.comparateur.prix') || request()->routeIs('en.manga.price.comparator')" class="text-white hover:bg-white/10">
                <i class="fas fa-search mr-2"></i>
                {{ __('messages.comparator') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="\App\Helpers\LocalizedRoute::url('price.historique')" :active="request()->routeIs('fr.historique.recherches') || request()->routeIs('en.search.history')" class="text-white hover:bg-white/10">
                <i class="fas fa-history mr-2"></i>
                {{ __('messages.history') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="\App\Helpers\LocalizedRoute::url('image.upload.form')" :active="request()->routeIs('fr.estimation.lot.manga') || request()->routeIs('en.manga.lot.estimation')" class="text-white hover:bg-white/10">
                <i class="fas fa-camera mr-2"></i>
                {{ __('messages.manga_lot_estimation') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/20">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-purple-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="\App\Helpers\LocalizedRoute::localized('profile.edit')" class="text-white hover:bg-white/10">
                    <i class="fas fa-user-edit mr-2"></i>
                    {{ __('messages.profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="\App\Helpers\LocalizedRoute::localized('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-white hover:bg-white/10">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('messages.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
