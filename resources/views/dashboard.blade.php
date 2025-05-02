<x-app-layout>
    <x-slot name="header">
        <h2 class="px-8 font-semibold text-xl text-center text-primary-grey leading-tight">
            BIENVENUE {{ strtoupper(Auth::user()->name) }}
        </h2>
    </x-slot>

    <div class="py-4 mx-12 bg-off-white rounded-lg max-h-screen">
        <div class="max-w-4xl mx-auto">
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
                        class="absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg z-50 flex flex-col">
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
        </div>
        <div class="mx-auto">
            <div id="user-search-results" class="mt-8 flex justify-center items-center"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('user-search-form');
            const results = document.getElementById('user-search-results');
            const input = document.getElementById('user-search-input');
            const tableSelect = document.getElementById('user-search-table');
            const suggestionBox = document.getElementById('autocomplete-results');
            const resetBtn = document.getElementById('reset-search-input');

            // Autocomplétion
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

            // Bouton reset
            resetBtn.addEventListener('click', function() {
                input.value = '';
                input.focus();
                resetBtn.classList.add('hidden');
                suggestionBox.innerHTML = '';
                suggestionBox.classList.add('hidden');
            });

            // Fermer l'autocomplétion si clic à l'extérieur
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.innerHTML = '';
                    suggestionBox.classList.add('hidden');
                }
            });

            // Soumission du formulaire de recherche
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
    </script>
</x-app-layout>
