<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.annonce_results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('fr.generateur.annonces') }}" class="inline-flex items-center text-sm font-medium text-gray-300 hover:text-white">
                                <i class="fas fa-magic mr-2"></i>
                                {{ __('messages.generateur_annonces') }}
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm font-medium text-white">{{ __('messages.annonce_results') }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Results Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl text-white mr-3"></i>
                            <h3 class="text-xl font-bold text-white">{{ __('messages.generation_completed') }}</h3>
                        </div>
                        <a href="{{ route('fr.generateur.annonces') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            {{ __('messages.new_generation') }}
                        </a>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Generated Announcement -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-white mb-4">
                            <i class="fas fa-file-alt mr-2 text-yellow-400"></i>
                            {{ __('messages.generated_annonce') }}
                        </h4>
                        
                        <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                            <div class="prose prose-invert max-w-none">
                                {!! nl2br(e($annonce)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 justify-center">
                        <!-- Copy to Clipboard -->
                        <button onclick="copyToClipboard()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-copy mr-2"></i>
                            {{ __('messages.copy_text') }}
                        </button>

                        <!-- Download as Text -->
                        <button onclick="downloadText()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            {{ __('messages.download_text') }}
                        </button>

                        <!-- New Generation -->
                        <a href="{{ route('fr.generateur.annonces') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-magic mr-2"></i>
                            {{ __('messages.generate_another_annonce') }}
                        </a>
                    </div>

                    <!-- Tips -->
                    <div class="mt-8 bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <h5 class="text-blue-300 font-semibold mb-2">
                            <i class="fas fa-lightbulb mr-2"></i>
                            {{ __('messages.tips_title') }}
                        </h5>
                        <ul class="text-blue-200 text-sm space-y-1">
                            <li>• {{ __('messages.tip_1') }}</li>
                            <li>• {{ __('messages.tip_2') }}</li>
                            <li>• {{ __('messages.tip_3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const text = `{!! addslashes($annonce) !!}`;
            navigator.clipboard.writeText(text).then(() => {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check mr-2"></i>{{ __("messages.copied") }}';
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            }).catch(err => {
                console.error('Erreur lors de la copie:', err);
                alert('{{ __("messages.copy_error") }}');
            });
        }

        function downloadText() {
            const text = `{!! addslashes($annonce) !!}`;
            const blob = new Blob([text], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'annonce-occasion.txt';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
</x-app-layout> 