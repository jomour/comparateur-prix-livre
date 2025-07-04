@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white/20 bg-white/10 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-400/50 rounded-xl shadow-sm backdrop-blur-sm transition-all duration-300']) }}>
