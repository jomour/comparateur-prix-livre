<section>
    <header class="mb-6">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-br from-purple-600/30 to-pink-600/30 rounded-full p-3 mr-4 border border-purple-500/30">
                <i class="fas fa-user-edit text-purple-300 text-xl"></i>
            </div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                {{ __('Profile Information') }}
            </h2>
        </div>

        <p class="mt-1 text-sm text-purple-200">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ \App\Helpers\LocalizedRoute::localized('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ \App\Helpers\LocalizedRoute::localized('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-purple-200" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-purple-900/30 border-purple-400/50 text-white placeholder-purple-300 focus:border-purple-300 focus:ring-purple-300" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-purple-200" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-purple-900/30 border-purple-400/50 text-white placeholder-purple-300 focus:border-purple-300 focus:ring-purple-300" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-yellow-900/30 rounded-lg border border-yellow-400/50">
                    <p class="text-sm text-yellow-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-yellow-300 hover:text-yellow-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-300 bg-green-900/30 px-3 py-2 rounded border border-green-400/50">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
            <x-primary-button class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 border border-purple-500/30 shadow-lg transform hover:scale-105 transition-all duration-300 text-sm">
                <i class="fas fa-save mr-1 sm:mr-2"></i>
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
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
