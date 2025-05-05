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
                <div class="rounded-lg px-4 md:px-8 py-2 flex flex-col items-center max-w-full md:max-w-sm w-full">
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



        <section class="flex flex-col h-auto md:flex-row md:items-start md:justify-between gap-4 mt-10 px-0 md:px-8">
            <div id="selected-entity-card" class="hidden">
                <div id="interlocutor-select-zone" class="mt-4"></div>
            </div>
        </section>
    </section>

    <script>
        window.translatedFields = @json(__('fields'));
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Lien dynamique pour le bouton d'ajout ---
            const select = document.getElementById('add-model-select');
            const link = document.getElementById('add-model-link');
            select.addEventListener('change', function() {
                const model = this.value;
                link.href = `/model/${model}/create`;
            });

            // --- Recherche utilisateur, autocomplétion, reset, fermeture résultats ---
            const form = document.getElementById('user-search-form');
            const results = document.getElementById('user-search-results');
            const input = document.getElementById('user-search-input');
            const tableSelect = document.getElementById('user-search-table');
            const suggestionBox = document.getElementById('autocomplete-results');
            const resetBtn = document.getElementById('reset-search-input');

            input.addEventListener('input', function() {
                resetBtn.classList.toggle('hidden', !this.value.length);
                const q = this.value.trim();
                const table = tableSelect.value;
                if (q.length < 2) {
                    suggestionBox.innerHTML = '';
                    suggestionBox.classList.add('hidden');
                    return;
                }
                fetch(`/user-suggestions?q=${encodeURIComponent(q)}&table=${encodeURIComponent(table)}`)
                    .then(res => res.json())
                    .then(data => {
                        suggestionBox.innerHTML = '';
                        const suggestions = data.slice(0, 5);
                        if (suggestions.length) {
                            suggestions.forEach(suggestion => {
                                let item = document.createElement('button');
                                item.type = 'button';
                                item.className =
                                    'text-left px-4 py-2 hover:bg-blue-accent hover:text-off-white cursor-pointer';
                                item.textContent = suggestion.label ?? suggestion;
                                item.onclick = function() {
                                    input.value = suggestion.label ?? suggestion;
                                    suggestionBox.innerHTML = '';
                                    suggestionBox.classList.add('hidden');
                                };
                                suggestionBox.appendChild(item);
                            });
                            suggestionBox.classList.remove('hidden');
                        } else {
                            suggestionBox.classList.add('hidden');
                        }
                    });
            });

            resetBtn.addEventListener('click', function() {
                input.value = '';
                input.focus();
                resetBtn.classList.add('hidden');
                suggestionBox.innerHTML = '';
                suggestionBox.classList.add('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.innerHTML = '';
                    suggestionBox.classList.add('hidden');
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                results.innerHTML = '<div class="text-gray-400 p-4">Recherche en cours...</div>';
                fetch('/user-search', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.text())
                    .then(html => {
                        results.innerHTML = `
                    <div class="relative w-full">
                        <button id="close-search-results" type="button"
                            class="absolute right-36 top-2 text-xl text-red-accent hover:text-red-hover font-bold z-10">&times;</button>
                        <div class="pt-6 flex justify-center items-center">${html}</div>
                    </div>
                `;
                        document.getElementById('close-search-results').onclick = function() {
                            results.innerHTML = '';
                        };
                    })
                    .catch(() => {
                        results.innerHTML =
                            '<div class="text-red-500 p-4">Erreur lors de la recherche.</div>';
                    });
            });

        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('search-result-link')) {
                e.preventDefault();
                // Récupère dynamiquement tous les data-attributes
                const dataset = e.target.dataset;
                const entity = {};
                for (const key in dataset) {
                    entity[key] = dataset[key];
                }
                // Stocke en mémoire locale (localStorage)
                localStorage.setItem('selectedEntity', JSON.stringify(entity));
                // Affiche dans la card
                showSelectedEntityCard(entity);
            }
        });

        function showSelectedEntityCard(entity) {
            const card = document.getElementById('selected-entity-card');
            card.classList.remove('hidden');

            // Récupère les labels traduits depuis Laravel
            const fields = window.translatedFields || {};

            let html = `
        <div class="bg-white rounded-lg shadow-md p-6 min-w-[260px] max-w-xs">
            <div class="font-bold text-blue-accent text-lg mb-2">${entity.fullname || ''}</div>
            <div class="text-xs uppercase text-gray-500 mb-2">${entity.type || ''}</div>
            <ul class="mb-2">
    `;

            // Affiche tous les champs sauf name/fullname/type/id
            Object.entries(entity).forEach(([key, value]) => {
                if (['name', 'fullname', 'type', 'id', 'model'].includes(key) || value === '' || value === null)
                    return;
                html += `<li class="mb-1"><span class="font-semibold">${fields[key] || key} :</span> ${value}</li>`;
            });

            html += `
            </ul>
            <div class="text-xs text-gray-400 mt-2">ID : ${entity.id || ''}</div>
        </div>
    `;
            card.innerHTML = html;
        }

        // Affiche la sélection au chargement si elle existe
        document.addEventListener('DOMContentLoaded', function() {
            const entity = localStorage.getItem('selectedEntity');
            if (entity) {
                showSelectedEntityCard(JSON.parse(entity));
            }
        });

        function showInterlocutorSelect(societeId) {
            const zone = document.getElementById('interlocutor-select-zone');
            zone.innerHTML = 'Chargement des interlocuteurs...';
            fetch(`/societe/${societeId}/interlocuteurs`)
                .then(res => res.json())
                .then(interlocutors => {
                    if (!interlocutors.length) {
                        zone.innerHTML = '<div class="text-gray-500">Aucun interlocuteur pour cette société.</div>';
                        return;
                    }
                    let html = `<label class="block mb-1 text-blue-accent font-semibold">Interlocuteur :</label>
                <select id="interlocutor-select" class="border rounded px-4 py-2 w-full mb-2">
                    <option value="">Sélectionner...</option>`;
                    interlocutors.forEach(i => {
                        html += `<option value="${i.id}">${i.fullname || i.name}</option>`;
                    });
                    html += `</select>`;
                    zone.innerHTML = html;

                    // Optionnel : afficher la card de l'interlocuteur sélectionné
                    document.getElementById('interlocutor-select').addEventListener('change', function() {
                        const selected = interlocutors.find(i => i.id == this.value);
                        if (selected) {
                            showSelectedEntityCard(selected);
                        }
                    });
                });
        }

        // Quand une société est sélectionnée, affiche le select interlocuteur
        function showSelectedEntityCard(entity) {
            const card = document.getElementById('selected-entity-card');
            card.classList.remove('hidden');
            const fields = window.translatedFields || {};
            let html = `
        <div class="bg-white rounded-lg shadow-md p-6 min-w-[260px] max-w-xs">
            <div class="font-bold text-blue-accent text-lg mb-2">${entity.name || entity.fullname || ''}</div>
            <div class="text-xs uppercase text-gray-500 mb-2">${entity.type || ''}</div>
            <ul class="mb-2">
    `;
            Object.entries(entity).forEach(([key, value]) => {
                if (['name', 'fullname', 'type', 'id', 'model'].includes(key) || value === '' || value === null)
                    return;
                html += `<li class="mb-1"><span class="font-semibold">${fields[key] || key} :</span> ${value}</li>`;
            });
            html += `
            </ul>
            <div class="text-xs text-gray-400 mt-2">ID : ${entity.id || ''}</div>
        </div>
    `;
            card.innerHTML = html;

            // Si c'est une société, affiche le select interlocuteur
            if (entity.type === 'société' || entity.model === 'société') {
                showInterlocutorSelect(entity.id);
            } else {
                document.getElementById('interlocutor-select-zone').innerHTML = '';
            }
        }
    </script>
</x-app-layout>
