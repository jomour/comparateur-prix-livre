@php
    $currentLocale = app()->getLocale();
    $currentUrl = request()->getRequestUri();
@endphp

<div class="relative inline-block text-left">
    <div>
        <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="language-menu-button" aria-expanded="true" aria-haspopup="true">
            {{ strtoupper($currentLocale) }}
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1" id="language-menu">
        <div class="py-1" role="none">
            <a href="{{ \App\Helpers\LocalizedRoute::switchLanguage('fr', $currentUrl) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 {{ $currentLocale === 'fr' ? 'bg-gray-100' : '' }}" role="menuitem" tabindex="-1">
                {{ __('messages.french') }}
            </a>
            <a href="{{ \App\Helpers\LocalizedRoute::switchLanguage('en', $currentUrl) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 {{ $currentLocale === 'en' ? 'bg-gray-100' : '' }}" role="menuitem" tabindex="-1">
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