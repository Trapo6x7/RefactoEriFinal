<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 flex flex-col items-center">
        <div class="w-full max-w-7xl flex flex-col gap-6">
            <!-- Colonne gauche : Infos profil et mot de passe -->
            <div class="flex gap-6 flex-1">
                <div class="p-4 sm:p-8 sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
                <div class="p-4 sm:p-8 sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
            <!-- Colonne droite : Suppression du compte -->
            <div class="flex-1 flex flex-col justify-start">
                <div class="p-4 sm:p-8 sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
