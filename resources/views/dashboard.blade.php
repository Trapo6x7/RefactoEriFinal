<x-app-layout>
    <x-slot name="header">
        <h2 class="px-8 font-semibold text-xl text-center text-primary-grey leading-tight">
            BIENVENUE {{ strtoupper(Auth::user()->name) }}
        </h2>
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
                            <option value="problems">Problèmes</option>
                            <option value="problemStatuses">Statuts</option>
                            <option value="interlocutors">Interlocuteurs</option>
                            <option value="envs">Environnements</option>
                            <option value="tools">Outils</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-accent text-off-white rounded-md uppercase font-semibold hover:bg-blue-hover transition">
                        Rechercher
                    </button>
                </form>
            </article>

            <div class="mx-auto">
                <div id="user-search-results" class="mt-8 flex justify-center items-center"></div>
            </div>

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

            <article class="w-full flex justify-center mt-6">
                <div class=" rounded-lg px-4 md:px-8 py-6 flex flex-col items-center max-w-full md:max-w-sm w-full">
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

        <section class="flex flex-col h-auto md:flex-row md:items-start md:justify-between gap-4 mt-8 px-0 md:px-8">
            <article id="saved-card-1"
                class=" rounded-lg shadow-md p-0 flex flex-col items-center justify-start w-full md:w-1/3 max-w-full md:max-w-lg flex-grow h-72 overflow-y-auto mb-4 md:mb-0">
            </article>
        
            <article id="saved-card-2"
                class=" rounded-lg shadow-md p-0 flex flex-col items-center justify-start w-full md:w-1/3 max-w-full md:max-w-lg flex-grow h-72 overflow-y-auto mb-4 md:mb-0">
            </article>
        
            <article id="saved-card-3"
                class=" rounded-lg shadow-md p-0 flex flex-col items-center justify-start w-full md:w-1/3 max-w-full md:max-w-lg flex-grow h-72 overflow-y-auto">
            </article>
        </section>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lien de création dynamique
            const select = document.getElementById('add-model-select');
            const link = document.getElementById('add-model-link');
            select.addEventListener('change', function() {
                const model = this.value;
                link.href = `/model/${model}/create`;
            });
        
            // Autocomplétion recherche utilisateur
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
                                item.className = 'text-left px-4 py-2 hover:bg-blue-accent hover:text-off-white cursor-pointer';
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
                        results.innerHTML = '<div class="text-red-500 p-4">Erreur lors de la recherche.</div>';
                    });
            });
        });
        
        // Gestion des cards dashboard
        let savedResults = [null, null, null];
        
        function updateCards() {
            for (let i = 0; i < 3; i++) {
                const card = document.getElementById('saved-card-' + (i + 1));
                if (savedResults[i]) {
                    card.innerHTML = `
            <div class="flex items-center justify-between w-full mb-2">
                <h3 class="text-lg font-semibold text-blue-accent flex-1">${savedResults[i].title}</h3>
                <button type="button"
                    class="ml-2 text-xl text-red-500 hover:text-red-700 font-bold remove-saved-result-btn"
                    data-index="${i}" title="Retirer">&times;</button>
                <a href="${savedResults[i].url}" class="ml-4 text-blue-600 hover:underline text-sm" title="Voir la fiche">Voir</a>
            </div>
            <p class="text-gray-600 text-center mb-2">${savedResults[i].description ?? ''}</p>
            <div class="bg-secondary-grey p-3 mb-2 w-full">
                <ul class="space-y-2 w-full">
                    ${
                        savedResults[i].details
                            ? Object.entries(savedResults[i].details)
                                .filter(([key]) => !['id', 'created_at', 'updated_at'].includes(key))
                                .map(([key, value]) =>
                                    `<li class="border-b border-gray-200 py-2 w-full">
                                        <div class="font-semibold text-blue-700 text-xs uppercase tracking-wide mb-1 w-full">${key}</div>
                                        <div class="text-gray-700 break-words text-base text-right w-full editable-value"
                                            contenteditable="true"
                                            data-key="${key}"
                                            data-index="${i}"
                                            data-model="${savedResults[i].details.model ?? ''}"
                                            data-id="${savedResults[i].details.id ?? ''}">
                                            ${value ?? ''}
                                        </div>
                                    </li>`
                                ).join('')
                            : ''
                    }
                </ul>
            </div>
        `;
                } else {
                    card.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full">
                            <span class="text-gray-400">Aucun résultat enregistré</span>
                        </div>
                    `;
                }
            }
        }
        
        // Délégation pour le bouton "Retirer"
        document.addEventListener('click', function(e) {
            if (e.target.matches('.remove-saved-result-btn')) {
                const index = parseInt(e.target.dataset.index, 10);
                removeSavedResult(index);
            }
        });
        
        // Fonction pour retirer une card
        function removeSavedResult(index) {
            savedResults[index] = null;
            updateCards();
        }
        
        // Ajout d'un résultat dans la première card vide
        document.addEventListener('click', function(e) {
            if (e.target.matches('.search-result-link')) {
                e.preventDefault();
                const title = e.target.dataset.title;
                const description = e.target.dataset.description || '';
                const url = e.target.href;
                const model = e.target.dataset.model;
                const id = e.target.dataset.id;
        
                fetch(`/api/model/${model}/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        const idx = savedResults.findIndex(r => r === null);
                        if (idx !== -1) {
                            // Ajoute le model dans details pour l'édition
                            savedResults[idx] = {
                                title,
                                description,
                                url,
                                details: { ...data, model }
                            };
                            updateCards();
                        } else {
                            alert('Toutes les cartes sont déjà utilisées.');
                        }
                    });
            }
        });
        
        // Délégation pour la sauvegarde à la sortie du champ éditable
        document.addEventListener('blur', function(e) {
            if (e.target.matches('.editable-value')) {
                const newValue = e.target.innerText.trim();
                const key = e.target.dataset.key;
                const index = e.target.dataset.index;
                const model = e.target.dataset.model;
                const id = e.target.dataset.id;
        
                // Met à jour en mémoire
                if (savedResults[index] && savedResults[index].details) {
                    savedResults[index].details[key] = newValue;
                }
        
                // Envoi AJAX pour sauvegarder en BDD
                fetch(`/api/model/${model}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ [key]: newValue })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Erreur lors de la sauvegarde');
                    e.target.classList.add('bg-green-100');
                    setTimeout(() => e.target.classList.remove('bg-green-100'), 1000);
                })
                .catch(() => {
                    e.target.classList.add('bg-red-100');
                    setTimeout(() => e.target.classList.remove('bg-red-100'), 1000);
                });
            }
        }, true); // useCapture pour blur
        </script>
</x-app-layout>
