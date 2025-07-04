<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl']) }}>
    {{ $slot }}
</button>
