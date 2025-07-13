<section class="space-y-6">
    <header class="mb-6">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-br from-red-600/30 to-pink-600/30 rounded-full p-3 mr-4 border border-red-500/30">
                <i class="fas fa-user-times text-red-300 text-xl"></i>
            </div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-red-400 to-pink-400 bg-clip-text text-transparent">
                {{ __('Delete Account') }}
            </h2>
        </div>

        <p class="mt-1 text-sm text-red-200">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 border border-red-500/30 shadow-lg transform hover:scale-105 transition-all duration-300"
    >
        <i class="fas fa-trash mr-2"></i>
        {{ __('Delete Account') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ \App\Helpers\LocalizedRoute::localized('profile.destroy') }}" class="p-6 bg-gradient-to-br from-red-900/90 to-pink-900/90 backdrop-blur-lg rounded-xl border border-red-400/50">
            @csrf
            @method('delete')

            <div class="flex items-center mb-4">
                <div class="bg-gradient-to-br from-red-600/50 to-pink-600/50 rounded-full p-3 mr-4 border border-red-400/50">
                    <i class="fas fa-exclamation-triangle text-red-200 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-white">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>
            </div>

            <p class="mt-1 text-sm text-red-200 mb-6">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 bg-red-900/30 border-red-400/50 text-white placeholder-red-300 focus:border-red-300 focus:ring-red-300"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-2 sm:space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 border border-gray-500/30 shadow-lg transform hover:scale-105 transition-all duration-300 text-sm">
                    <i class="fas fa-times mr-1 sm:mr-2"></i>
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 border border-red-500/30 shadow-lg transform hover:scale-105 transition-all duration-300 text-sm">
                    <i class="fas fa-trash mr-1 sm:mr-2"></i>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
