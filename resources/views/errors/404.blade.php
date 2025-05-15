<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-off-white">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-blue-accent mb-4">404</h1>
            <p class="text-lg text-primary-grey">Oups, cette page n'existe pas.</p>
        </div>
        <a href="{{ url('/') }}"
            class="bg-blue-accent text-off-white px-6 py-2 rounded hover:bg-blue-hover transition mt-8">Retour à l'accueil</a>
    </div>
</x-app-layout>
