<x-app-layout>
    <x-slot name="header">
        <h2 class="px-8 font-semibold text-xl text-primary-grey leading-tight">
        {{ __('ACCUEIL') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-secondary-grey min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-primary-grey uppercase">
                    {{ __('BIENVENUE :name', ['name' => Auth::user()->name]) }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>