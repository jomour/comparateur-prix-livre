<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('messages.verify_email_description') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('messages.verification_link_sent') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('messages.resend_verification_email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ \App\Helpers\LocalizedRoute::localized('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('messages.logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>
