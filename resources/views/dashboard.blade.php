<x-app-layout>
    <x-slot name="header">
        @php
            $models = [
                'société' => 'Société',
                'problème' => 'Problème',
                'interlocuteur' => 'Interlocuteur',
                'environnement' => 'Environnement',
                'outil' => 'Outil',
            ];
        @endphp

        <article class="w-full flex justify-center" id="header">
            <div class="rounded-lg px-4 md:px-8 flex flex-col items-center max-w-full md:max-w-sm w-full">
                <div class="text-sm font-semibold mb-2 text-blue-accent">Ajouter une nouvelle entrée</div>
                <div class="flex gap-2 w-full">
                    <select id="add-model-select" class="border rounded px-4 py-1 flex-1">
                        @foreach ($models as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button id="add-model-link" type="button"
                        class="px-4 py-1 bg-blue-accent text-off-white rounded-md uppercase font-semibold hover:bg-blue-hover transition flex-shrink-0 text-center">
                        +
                    </button>
                </div>
            </div>
        </article>


    </x-slot>
    <!-- Modale pour le formulaire -->
    <article id="add-model-modal" class="fixed inset-0 z-50 items-center justify-center bg-black hidden">
        <div class="rounded-lg shadow-lg p-6 w-full max-w-lg relative">
            <button id="close-add-model-modal"
                class="absolute top-2 right-2 text-red-accent hover:text-red-hover text-2xl">&times;</button>
            <div id="add-model-modal-content">
                <!-- Le formulaire sera chargé ici -->
                <div class="flex justify-center items-center h-32 text-blue-accent">Chargement...</div>
            </div>
        </div>
    </article>

    <section id="main-content">
        <section class="mx-0 bg-off-white rounded-lg md:h-[80%]">

            <article class="max-w-4xl pt-4 mx-auto">
                <form id="user-search-form"
                    class="flex flex-col md:flex-row items-center justify-center gap-4 relative">
                    <div class="w-full md:w-1/2 relative">
                        <input type="text" id="user-search-input" name="q" autocomplete="off"
                            placeholder="Recherche..."
                            class="w-full px-4 py-2 border text-sm h-9 border-secondary-grey rounded-md focus:outline-none focus:ring-2 focus:ring-blue-accent text-primary-grey">
                        <button type="button" id="reset-search-input"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-accent text-xl hidden"
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
                            class="w-full px-4 text-sm py-2 border h-9 border-secondary-grey rounded-md focus:outline-none focus:ring-2 focus:ring-blue-accent text-primary-grey">
                            <option value="" class="hover:bg-blue-accent">Toutes les tables</option>
                            <option value="interlocutors">Interlocuteurs</option>
                            <option value="societies">Sociétés</option>
                        </select>
                    </div>
                </form>

                <div id="edit-desc-info" class="text-xs text-center text-blue-accent mt-1">
                    Cliquez sur une entrée pour l'éditer. Cliquez en dehors pour sauvegarder.
                </div>

            </article>
            <article class="mx-auto">
                <div id="user-search-results" class="mt-4 flex justify-center items-center"></div>
            </article>
        </section>

        <section id="selected-entity-card"
            class="hidden md:flex flex-col md:flex-row flex-wrap gap-4 w-full min-w-0 p-2 sm:p-4 md:p-8">
            <article id="card-1"
                class="flex-1 min-w-0 md:min-w-[22%] max-w-full md:max-w-[24%] bg-white rounded-lg p-4 md:p-6 flex flex-col h-80 overflow-hidden overflow-y-scroll relative text-sm">
            </article>
            <article id="card-2"
                class="flex-1 min-w-0 md:min-w-[22%] max-w-full md:max-w-[24%] bg-white rounded-lg p-4 md:p-6 flex flex-col h-80 overflow-hidden overflow-y-scroll text-sm">
            </article>
            <div class="border-r border-blue-accent"></div>
            <article id="card-3"
                class="flex-1 min-w-0 md:min-w-[22%] max-w-full md:max-w-[24%] bg-white rounded-lg p-4 md:p-6 flex flex-col h-80 overflow-hidden overflow-y-scroll relative text-sm">
            </article>
            <article id="card-4"
                class="flex-1 min-w-0 md:min-w-[22%] max-w-full md:max-w-[24%] bg-white rounded-lg p-4 md:p-6 flex flex-col h-80 overflow-hidden overflow-y-scroll text-sm">
            </article>
        </section>

        <section class="flex flex-col md:flex-row h-auto md:h-96 bg-off-white rounded-lg mt-4 px-2 sm:px-4 md:px-8">
            <article id="problemes-list1"
                class="w-full md:w-1/2 px-2 sm:px-4 py-4 border-b md:border-b-0 md:border-r overflow-y-auto overflow-hidden border-blue-accent">
            </article>
            <article id="problemes-list2" class="w-full md:w-1/2 px-2 sm:px-8 py-4 overflow-y-auto overflow-hidden">
            </article>
        </section>
    </section>

    <script>
        const modal = document.getElementById('add-model-modal');
        const mainContent = document.getElementById('main-content');
        const header = document.getElementById('header');

        document.getElementById('add-model-link').addEventListener('click', function() {
            const model = document.getElementById('add-model-select').value;
            const url = "{{ route('model.form', ['model' => ':model', 'action' => 'create']) }}".replace(':model',
                model);

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            mainContent.classList.add('modal-blur');
            header.classList.add('modal-blur');
            document.getElementById('add-model-modal-content').innerHTML =
                '<div class="flex justify-center items-center h-32 text-blue-accent">Chargement...</div>';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('add-model-modal-content').innerHTML = html;
                });
        });

        document.getElementById('close-add-model-modal').addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            mainContent.classList.remove('modal-blur');
            header.classList.remove('modal-blur');
        });

        window.userRoles = @json(Auth::user() ? Auth::user()->isAdmin() || Auth::user()->isSuperAdmin() : []);

        document.addEventListener('DOMContentLoaded', function() {
            if (window.userRoles && (window.userRoles.includes('admin') || window.userRoles.includes(
                    'superadmin'))) {
                document.getElementById('edit-desc-info').style.display = '';
            } else {
                document.getElementById('edit-desc-info').style.display = 'none';
            }
        });

        
    </script>
    @vite('resources/js/dashboard.js')
</x-app-layout>
