<x-app-layout>
    <x-slot name="header">
<h1 class="text-2xl font-semibold text-blue-accent leading-tight uppercase text-center">
        {{ __('Dashboard') }}
    </h1>
    </x-slot>

    <section class="px-4 md:px-8">
        <section class="mx-0 md:mx-8 bg-off-white rounded-lg h-auto md:h-[80%]">
            @php
            $models = [
                'société' => 'Société',
                'problème' => 'Problème',
                'problemStatus' => 'Statut',
                'interlocuteur' => 'Interlocuteur',
                'environnement' => 'Environnement',
                'outil' => 'Outil',
            ];
        @endphp

        <article class="w-full flex justify-center">
            <div class=" rounded-lg px-4 md:px-8 py-2 flex flex-col items-center max-w-full md:max-w-sm w-full">
                <div class="text-lg font-semibold mb-2 text-blue-accent">Ajouter une nouvelle entrée</div>
                <div class="flex gap-2 w-full">
                    <select id="add-model-select" class="border rounded px-4 py-2 flex-1">
                        @foreach ($models as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <a id="add-model-link"
                        href="{{ route('model.form', ['model' => array_key_first($models), 'action' => 'create']) }}"
                        class="px-4 py-2 bg-blue-accent text-off-white rounded-md uppercase font-semibold hover:bg-blue-hover transition flex-shrink-0 text-center">
                        +
                    </a>
                </div>
            </div>
        </article>
            

        </section>

        <section class="flex flex-col h-auto md:flex-row md:items-start md:justify-between gap-4 mt-10 px-0 md:px-8">

        </section>
    </section>

    <script>
        window.translatedFields = @json(__('fields'));
    </script>

   
</x-app-layout>
