@php
    $currentLocale = app()->getLocale();
    $currentUrl = request()->getRequestUri();
@endphp

<div class="relative inline-block text-left z-[9999]">
    <div>
        <button type="button" class="inline-flex justify-center w-full rounded-xl border border-white/20 shadow-lg px-4 py-2 bg-white/10 backdrop-blur-sm text-sm font-medium text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-400 transition-all duration-200" id="language-menu-button" aria-expanded="true" aria-haspopup="true">
            <i class="fas fa-globe mr-2 text-purple-300"></i>
            {{ strtoupper($currentLocale) }}
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white/10 backdrop-blur-lg border border-white/20 ring-1 ring-black ring-opacity-5 focus:outline-none hidden z-[9999]" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1" id="language-menu">
        <div class="py-1" role="none">
            <a href="{{ \App\Helpers\LocalizedRoute::switchLanguage('fr', $currentUrl) }}" class="text-white block px-4 py-2 text-sm hover:bg-white/10 {{ $currentLocale === 'fr' ? 'bg-purple-500/20' : '' }} transition-colors duration-200" role="menuitem" tabindex="-1">
                <i class="fas fa-flag mr-2 text-blue-400"></i>
                {{ __('messages.french') }}
            </a>
            <a href="{{ \App\Helpers\LocalizedRoute::switchLanguage('en', $currentUrl) }}" class="text-white block px-4 py-2 text-sm hover:bg-white/10 {{ $currentLocale === 'en' ? 'bg-purple-500/20' : '' }} transition-colors duration-200" role="menuitem" tabindex="-1">
                <i class="fas fa-flag-usa mr-2 text-red-400"></i>
                {{ __('messages.english') }}
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('language-menu-button');
    const menu = document.getElementById('language-menu');
    
    button.addEventListener('click', function() {
        menu.classList.toggle('hidden');
    });
    
    // Fermer le menu quand on clique ailleurs
    document.addEventListener('click', function(event) {
        if (!button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
});
</script> 