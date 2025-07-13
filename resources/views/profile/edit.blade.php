<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('messages.profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
            <!-- Breadcrumbs -->
            <div class="mb-6">
                <x-breadcrumbs page="profile" />
            </div>

            <!-- Profile Information -->
            <div class="bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-yellow-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-gradient-to-br from-blue-900/40 via-cyan-900/40 to-indigo-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-gradient-to-br from-red-900/40 via-pink-900/40 to-orange-900/40 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
