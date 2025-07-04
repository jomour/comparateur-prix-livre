<section>
    <header class="mb-6">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-br from-blue-600/30 to-cyan-600/30 rounded-full p-3 mr-4 border border-blue-500/30">
                <i class="fas fa-lock text-blue-300 text-xl"></i>
            </div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                {{ __('Update Password') }}
            </h2>
        </div>

        <p class="mt-1 text-sm text-blue-200">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ \App\Helpers\LocalizedRoute::localized('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-blue-200" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-blue-900/30 border-blue-400/50 text-white placeholder-blue-300 focus:border-blue-300 focus:ring-blue-300" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-blue-200" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-blue-900/30 border-blue-400/50 text-white placeholder-blue-300 focus:border-blue-300 focus:ring-blue-300" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-blue-200" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-blue-900/30 border-blue-400/50 text-white placeholder-blue-300 focus:border-blue-300 focus:ring-blue-300" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 border border-blue-500/30 shadow-lg transform hover:scale-105 transition-all duration-300">
                <i class="fas fa-key mr-2"></i>
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-300 bg-green-900/30 px-3 py-2 rounded border border-green-400/50"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
