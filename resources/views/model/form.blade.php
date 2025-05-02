<x-app-layout>
    <h2 class="uppercase text-2xl font-bold text-center my-8 text-blue-accent">
        {{ ucfirst($action) }} {{ ucfirst($model) }}
    </h2>
    <form method="POST" action="{{ route('model.submit', [$model, $action, $instance->id ?? null]) }}" class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow space-y-8 flex flex-col">
        @csrf
        @if ($action === 'edit')
            @method('POST')
        @endif
    
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
    
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded shadow uppercase tracking-wider transition">
                {{ $action === 'create' ? 'Créer' : 'Mettre à jour' }}
            </button>
        </div>
    </form>
</x-app-layout>
