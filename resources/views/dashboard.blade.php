<x-app-layout>
    <x-slot name="header">
        @php
            $models = [
                'societe' => 'societe',
                'probleme' => 'probleme',
                'interlocuteur' => 'Interlocuteur',
                'environnement' => 'Environnement',
                'outil' => 'Outil',
                'user' => 'Utilisateur',
                'tech' => 'Technicien',
            ];
        @endphp

        <article class="w-full flex justify-center bg-white rounded-md" id="header">
            <div class="rounded-lg p-2 md:p-4 flex flex-col items-center max-w-full md:max-w-sm w-full">
                <div class="text-lg font-semibold mb-2 text-blue-accent">Ajouter une nouvelle entrée</div>
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
                <div class="flex justify-center items-center text-blue-accent">Chargement...</div>
            </div>
        </div>
    </article>

    <section id="main-content" class="text-sm md:text-md lg:text-lg flex flex-col h-full min-h-0 flex-1">
        <section class="mx-0 bg-off-white rounded-lg text-sm md:text-md lg:text-lg">

            <article class="max-w-4xl pt-4 mx-auto">
                <form id="user-search-form"
                    class="flex flex-col md:flex-row items-center justify-center gap-4 relative">
                    <div class="w-full md:w-1/2 relative">
                        <input type="text" id="user-search-input" name="q" autocomplete="off"
                            placeholder="Recherche..."
                            class="w-full px-4 py-2 border text-sm md:text-md lg:text-lg border-secondary-grey rounded-md focus:outline-none focus:ring-2 focus:ring-blue-accent text-primary-grey">
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
                            class="w-full px-4 py-2 border border-secondary-grey rounded-md focus:outline-none focus:ring-2 focus:ring-blue-accent text-sm md:text-md lg:text-lg">
                            <option value="" class="hover:bg-blue-accent">Toutes les tables</option>
                            <option value="interlocutors">Interlocuteurs</option>
                            <option value="societies">societes</option>
                        </select>
                    </div>
                </form>

            </article>
            <article class="mx-auto">
                <div id="user-search-results" class="mt-4 flex justify-center items-center"></div>
            </article>
        </section>

        <section id="selected-entity-card"
            class="hidden lg:flex flex-col lg:flex-row gap-4 w-full min-w-0 p-1 sm:p-2 lg:p-4 flex-1 min-h-0 h-[40vh] lg:h-[50vh] bg-off-white rounded-lg">
            <article id="card-1"
                class="relative w-full lg:w-1/4 px-2 sm:px-4 py-4 overflow-y-auto overflow-hidden h-[450px] bg-white rounded-lg flex flex-col text-sm md:text-md lg:text-lg">
            </article>
            <article id="card-2"
                class="w-full lg:w-1/4 px-2 sm:px-4 py-4 overflow-y-auto overflow-hidden h-[450px] bg-white rounded-lg flex flex-col text-sm md:text-md lg:text-lg">
            </article>
            <div class="border-r border-blue-accent"></div>
            <article id="card-3"
                class="w-full lg:w-1/4 px-2 sm:px-4 py-4 overflow-y-auto overflow-hidden h-[450px] bg-white rounded-lg flex flex-col text-sm md:text-md lg:text-lg">
            </article>
            <article id="card-4"
                class="w-full lg:w-1/4 px-2 sm:px-4 py-4 overflow-y-auto overflow-hidden h-[450px] bg-white rounded-lg flex flex-col text-sm md:text-md lg:text-lg">
            </article>
        </section>

        <section
            class="flex flex-col lg:flex-row h-[30vh] lg:h-[40vh] bg-off-white rounded-lg mt-1 px-2 sm:px-4 lg:px-8 min-h-0 text-sm md:text-md lg:text-lg">
            <article id="problemes-list1"
                class="w-full lg:w-1/2 px-2 sm:px-4 py-4 overflow-y-auto overflow-hidden h-full min-h-0">
            </article>
            <div class="border-r border-blue-accent"></div>
            <article id="problemes-list2"
                class="w-full lg:w-1/2 px-2 sm:px-8 py-4 overflow-y-auto overflow-hidden h-full min-h-0">
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

        window.userRoles = @json(Auth::user()
                ? collect([Auth::user()->isAdmin() ? 'admin' : null, Auth::user()->isSuperAdmin() ? 'superadmin' : null])->filter()->values()
                : []
        );

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('add-model-select');
            if (!window.userRoles.includes('superadmin')) {
                // Supprime les options "Utilisateur" et "Technicien"
                Array.from(select.options).forEach(option => {
                    if (option.value === 'user' || option.value === 'tech') {
                        option.remove();
                    }
                });
            }
        });

        window.translatedFields = @json(__('fields'));
        window.currentUserRole = window.userRoles && window.userRoles.length > 0 ? window.userRoles[0] : '';
    </script>
    @vite(['resources/js/dashboard.js'])
</x-app-layout>
