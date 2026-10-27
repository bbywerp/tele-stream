<x-app-layout>
    <x-slot name="header">
        Profil admin
    </x-slot>

    <div class="pb-6">
        <div class="max-w-3xl space-y-6">
            <div class="clay-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="clay-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="clay-card">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
