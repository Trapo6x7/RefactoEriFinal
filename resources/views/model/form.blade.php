<x-app-layout>
    <h2 class="uppercase text-2xl font-bold text-center my-8 text-blue-accent">
        CRÉER {{ ucfirst($model) }}
    </h2>
    <form method="POST" action="{{ route('model.submit', [$model, $action, $instance->id ?? null]) }}"
        class="max-w-4xl mx-auto bg-off-white p-8 rounded-lg shadow space-y-8 flex flex-col gap-4">
        @csrf

        @if ($errors->any())
            <div class="mb-4 text-red-accent">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($action === 'edit')
            @method('POST')
        @endif

        <div class="overflow-y-auto max-h-[60vh] px-2 flex flex-col gap-4 text-sm">
            @if ($model === 'société')
                @include('model.partials._society_field')
            @elseif($model === 'interlocuteur')
                @include('model.partials._interlocutor_field')
            @elseif($model === 'environnement')
                @include('model.partials._env_field')
            @elseif($model === 'problème')
                @include('model.partials._problem_field')
            @elseif($model === 'outil')
                @include('model.partials._tool_field')
            @endif
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-accent hover:bg-blue-hover text-off-white font-bold py-2 px-8 rounded uppercase tracking-wider transition">
                {{ $action === 'create' ? 'Créer' : 'Mettre à jour' }}
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                let valid = true;
                let firstInvalid = null;
                // Sélectionne tous les champs requis (ajoute l'attribut required sur tes inputs Blade)
                form.querySelectorAll('[required]').forEach(function(input) {
                    if (!input.value.trim()) {
                        valid = false;
                        input.classList.add('border-red-accent', 'ring-2', 'ring-red-300');
                        if (!firstInvalid) firstInvalid = input;
                    } else {
                        input.classList.remove('border-red-accent', 'ring-2', 'ring-red-300');
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    if (firstInvalid) firstInvalid.focus();
                }
            });
        });
    </script>
</x-app-layout>
