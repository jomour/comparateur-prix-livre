<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.generateur_annonces') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <span class="text-sm font-medium text-white">
                                <i class="fas fa-magic mr-2"></i>
                                {{ __('messages.generateur_annonces') }}
                            </span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Upload Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                <form method="POST" action="{{ route('fr.generateur.annonces.generate') }}" enctype="multipart/form-data" class="space-y-6" id="generationForm">
                    @csrf
                    
                    <!-- Upload Zone -->
                    <div class="space-y-4">
                        <label class="block text-white font-medium text-lg">
                            <i class="fas fa-upload mr-2 text-yellow-400"></i>
                            {{ __('messages.select_image') }}
                        </label>
                        
                        <div class="relative">
                            <div id="uploadZone" class="border-2 border-dashed border-white/30 rounded-xl p-8 text-center cursor-pointer hover:border-yellow-400 transition-colors duration-200 bg-white/5">
                                <div class="space-y-4">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-yellow-400"></i>
                                    <div class="text-white">
                                        <p class="text-lg font-medium">{{ __('messages.drag_drop_image') }}</p>
                                        <p class="text-gray-300">{{ __('messages.or_click_select') }}</p>
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        <p>{{ __('messages.accepted_formats') }}</p>
                                        <p>{{ __('messages.max_size') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" required>
                            
                            <!-- Preview -->
                            <div id="imagePreview" class="hidden mt-4">
                                <div class="relative inline-block">
                                    <img id="previewImg" class="max-w-full h-64 object-contain rounded-lg border border-white/20" alt="Preview">
                                    <button type="button" id="removeImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                                    <div>
                                        <h4 class="text-red-300 font-semibold">{{ __('messages.generation_error') }}</h4>
                                        <ul class="text-red-200 text-sm mt-1">
                                            @foreach ($errors->all() as $error)
                                                <li>• {{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit" id="generateBtn" class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-8 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-magic mr-2"></i>
                            <span id="generateBtnText">{{ __('messages.generate_annonce') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
                    <div class="text-center">
                        <!-- Spinner -->
                        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-yellow-500 mx-auto mb-6"></div>
                        
                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('messages.generation_in_progress') }}</h3>
                        
                        <!-- Description -->
                        <p class="text-gray-600 mb-6">{{ __('messages.generation_description') }}</p>
                        
                        <!-- Progress Steps -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-center">
                                <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-camera text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-600">{{ __('messages.step_1_title') }}</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-robot text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-600">{{ __('messages.step_2_title') }}</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-600">{{ __('messages.step_3_title') }}</span>
                            </div>
                        </div>
                        
                        <!-- Estimated Time -->
                        <div class="mt-6 p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center justify-center text-yellow-700">
                                <i class="fas fa-clock mr-2"></i>
                                <span class="text-sm font-medium">{{ __('messages.estimated_time') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Card -->
            <div class="mt-8 bg-blue-500/10 border border-blue-500/20 rounded-xl p-6">
                <h3 class="text-blue-300 font-semibold mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('messages.how_it_works') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-blue-200 text-sm">
                    <div class="flex items-start">
                        <i class="fas fa-camera text-blue-400 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-medium mb-1">{{ __('messages.step_1_title') }}</h4>
                            <p>{{ __('messages.step_1_description') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-robot text-blue-400 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-medium mb-1">{{ __('messages.step_2_title') }}</h4>
                            <p>{{ __('messages.step_2_description') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-file-alt text-blue-400 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-medium mb-1">{{ __('messages.step_3_title') }}</h4>
                            <p>{{ __('messages.step_3_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="mt-6 bg-green-500/10 border border-green-500/20 rounded-xl p-6">
                <h3 class="text-green-300 font-semibold mb-4">
                    <i class="fas fa-lightbulb mr-2"></i>
                    {{ __('messages.tips_for_better_results') }}
                </h3>
                <ul class="text-green-200 text-sm space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-400 mr-3 mt-1"></i>
                        <span>{{ __('messages.tip_1') }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-400 mr-3 mt-1"></i>
                        <span>{{ __('messages.tip_2') }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-400 mr-3 mt-1"></i>
                        <span>{{ __('messages.tip_3') }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-400 mr-3 mt-1"></i>
                        <span>{{ __('messages.tip_4') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadZone = document.getElementById('uploadZone');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImage = document.getElementById('removeImage');
            const generateBtn = document.getElementById('generateBtn');
            const generateBtnText = document.getElementById('generateBtnText');

            // Upload zone click
            uploadZone.addEventListener('click', () => imageInput.click());

            // Drag and drop
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('border-yellow-400');
            });

            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('border-yellow-400');
            });

            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('border-yellow-400');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFile(files[0]);
                }
            });

            // File input change
            imageInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });

            // Remove image
            removeImage.addEventListener('click', () => {
                imageInput.value = '';
                imagePreview.classList.add('hidden');
                uploadZone.classList.remove('hidden');
            });

            // Form submit
            document.getElementById('generationForm').addEventListener('submit', function(e) {
                if (!imageInput.files[0]) {
                    e.preventDefault();
                    alert('{{ __("messages.please_select_image") }}');
                    return;
                }

                // Show loading overlay
                document.getElementById('loadingOverlay').classList.remove('hidden');
                
                // Disable button and change text
                generateBtn.disabled = true;
                generateBtnText.textContent = '{{ __("messages.generating") }}';
                
                // Prevent form submission if no image
                if (!imageInput.files[0]) {
                    e.preventDefault();
                    document.getElementById('loadingOverlay').classList.add('hidden');
                    generateBtn.disabled = false;
                    generateBtnText.textContent = '{{ __("messages.generate_annonce") }}';
                    alert('{{ __("messages.please_select_image") }}');
                    return;
                }
            });

            function handleFile(file) {
                if (!file.type.startsWith('image/')) {
                    alert('{{ __("messages.please_select_valid_image") }}');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('{{ __("messages.file_too_large") }}');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    uploadZone.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-app-layout> 