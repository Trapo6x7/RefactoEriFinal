<form id="add-model-form" method="POST" action="{{ route('model.submit', [$model, $action, $instance->id ?? null]) }}"
    class="max-w-4xl mx-auto bg-off-white p-8 rounded-lg shadow space-y-8 flex flex-col gap-4">
    @csrf

    @if (!empty($success))
        <div class="text-blue-accent font-bold mb-4">Succès</div>
    @endif

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

    <div class="overflow-y-auto max-h-[60vh] px-2 flex flex-col gap-4 text-lg">
        @if ($model === 'societe')
            @include('model.partials._society_field')
        @elseif($model === 'interlocuteur')
            @include('model.partials._interlocutor_field')
        @elseif($model === 'environnement')
            @include('model.partials._env_field')
        @elseif($model === 'probleme')
            @include('model.partials._problem_field')
        @elseif($model === 'outil')
            @include('model.partials._tool_field')
        @elseif($model === 'user')
            @include('model.partials._user_field')
        @elseif($model === 'tech')
            @include('model.partials._tech_field')
        @endif
    </div>

    <div class="flex justify-end">
        <button type="submit"
            class="bg-blue-accent hover:bg-blue-hover text-off-white font-bold py-2 px-8 rounded uppercase tracking-wider transition">
            {{ $action === 'create' ? 'Créer' : 'Mettre à jour' }}
        </button>
    </div>
</form>
