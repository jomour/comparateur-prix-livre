<x-guest-layout>
    <!-- Message d'information -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            <p class="text-sm text-blue-800">
                <strong>{{ __('messages.restricted_access') }}</strong>
            </p>
        </div>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('messages.forgot_password_description') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('messages.email_password_reset_link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
