<x-app-layout>
    <x-slot name="header">
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
            <div class="rounded-lg px-4 md:px-8 flex flex-col items-center max-w-full md:max-w-sm w-full">
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
    </x-slot>

    <section class="px-4 md:px-8">
        <section class="mx-0 md:mx-8 bg-off-white rounded-lg h-auto md:h-[80%]">


            <article class="max-w-4xl pt-4 mx-auto">
                <form id="user-search-form" class="flex flex-col md:flex-row items-center gap-4 relative">
                    <div class="w-full md:w-1/2 relative">
                        <input type="text" id="user-search-input" name="q" autocomplete="off"
                            placeholder="Recherche..."
                            class="w-full px-4 py-2 border border-secondary-grey rounded-md focus:outline-none focus:ring-2 focus:ring-blue-accent text-primary-grey">
                        <button type="button" id="reset-search-input"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-xl hidden"
                            aria-label="Effacer">
                            &times;
                        </button>
                        <div id="autocomplete-results"
                            class="absolute left-0 top-full mt-1 w-full bg-off-white rounded-md shadow-lg z-50 flex flex-col">
                            <!-- Suggestions injectées ici -->
                        </div>
                    </div>

                    <div class="w-full md:w-1/4">
                        <select id="user-search-table" name="table"
                            class="w-full px-4 py-2 border border-secondary-grey rounded-md focus:outline-none focus:ring-2 focus:ring-blue-accent text-primary-grey">
                            <option value="" class="hover:bg-blue-accent">Toutes les tables</option>
                            <option value="societies">Sociétés</option>
                            <option value="interlocutors">Interlocuteurs</option>
                        </select>
                    </div>
                    <button type="submit" id="submit"
                        class="px-6 py-2 bg-blue-accent text-off-white rounded-md uppercase font-semibold hover:bg-blue-hover transition">
                        Rechercher
                    </button>
                </form>
            </article>
            <article class="mx-auto">
                <div id="user-search-results" class="mt-8 flex justify-center items-center"></div>
            </article>
        </section>

        <section id="selected-entity-card" class="flex flex-wrap gap-4 w-full min-w-0 p-8">
            <article id="card-1"
                class="flex-1 min-w-1/4 max-w-1/4 bg-white rounded-lg p-6 flex flex-col h-64 overflow-hidden overflow-y-scroll relative">
            </article>
            <article id="card-2"
                class="flex-1 min-w-1/4 max-w-1/4 bg-white rounded-lg p-6 flex flex-col h-64 overflow-hidden overflow-y-scroll">
            </article>
            <article id="card-3"
                class="flex-1 min-w-1/4 max-w-1/4 bg-white rounded-lg p-6 flex flex-col h-64 overflow-hidden overflow-y-scroll relative">
            </article>
            <article id="card-4"
                class="flex-1 min-w-1/4 max-w-1/4 bg-white rounded-lg p-6 flex flex-col h-64 overflow-hidden overflow-y-scroll">
            </article>
        </section>

        <section id='bandeau-problem' class="mx-8 h-64 overflow-y-auto bg-blue-accent rounded-lg mt-4">
            <article id="problemes-list" class="min-h-full p-6"></article>
        </section>
        </section>
    </section>
</x-app-layout>
