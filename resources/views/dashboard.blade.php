<x-app-layout>
    <x-slot name="header">
        @php
            $models = [
                'societe' => 'Société',
                'probleme' => 'Problème',
                'interlocuteur' => 'Interlocuteur',
                'environnement' => 'Environnement',
                'outil' => 'Outil',
            ];
        @endphp

        <article id="header" class="w-full flex justify-center rounded-md
                bg-white">
            <div
                class="rounded-lg p-2 md:p-4 lg:p-6 xl:p-8 2xl:p-10 flex flex-col items-center max-w-full md:max-w-sm w-full">
                <div class="text-base md:text-lg font-semibold mb-2 text-blue-accent">Ajouter une nouvelle entrée</div>
                <div class="flex flex-col md:flex-row gap-2 w-full">
                    <select id="add-model-select"
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition text-sm md:text-md lg:text-lg">
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

    <!-- Overlay pour le flou et l'assombrissement -->
    <div id="modal-overlay" class="modal-overlay"></div>

    <!-- Modale pour le formulaire -->
    <article id="add-model-modal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="rounded-lg p-6 w-full min-w-2xl relative resize overflow-auto">
            <button id="close-add-model-modal"
                class="absolute xl:w-1/4 2xl:w-1/2 top-4 right-4 text-red-accent hover:text-red-hover text-3xl">&times;</button>
            <div id="add-model-modal-content">
                <!-- Le formulaire sera chargé ici -->
                <div class="flex justify-center items-center text-blue-accent">Chargement...</div>
            </div>
        </div>
    </article>

    <section id="main-content" class="text-sm md:text-md lg:text-lg flex flex-col h-full min-h-0 flex-1">
        <section class="mx-0 bg-off-white rounded-lg text-sm md:text-md lg:text-lg px-8 lg:px-0">

            <article class="pt-4 mx-auto flex flex-col items-center justify-center w-full max-w-6xl">
                <form id="user-search-form"
                    class="flex flex-col md:flex-row items-center justify-center gap-2 lg:gap-4 relative md:w-3/4 w-full">
                    <input type="text" id="user-search-input" name="q" autocomplete="off"
                        placeholder="Recherche..."
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition text-sm md:text-md lg:text-lg">
                    <button type="button" id="reset-search-input"
                        class="absolute xl:right-[12rem] lg:right-[11rem] md:right-[8rem] lg:top-1/2 lg:-translate-y-1/2 md:-translate-y-[0.2rem] -translate-y-[1.7rem] right-[1rem] text-red-accent hover:text-red-accent text-3xl hidden"
                        aria-label="Effacer">
                        &times;
                    </button>
                    <div id="autocomplete-results"
                        class="absolute left-0 top-full w-full bg-off-white rounded-md z-50 flex flex-col">
                        <!-- Suggestions injectées ici -->
                    </div>
                    <div class="w-full md:w-1/4 md:mt-0">
                        <select id="user-search-table" name="table"
                            class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                            <option value="" class="hover:bg-blue-accent">Tous</option>
                            <option value="interlocutors">Interlocuteurs</option>
                            <option value="societies">Sociétés</option>
                        </select>
                    </div>
                </form>
                <div class="flex-col lg:flex-row items-center justify-center gap-1 w-full mt-4 hidden md:flex lg:px-24">
                    <div
                        class="relative w-full lg:w-1/2 flex lg:flex-col items-center justify-center lg:justify-between gap-1">
                        <label for="societe-select"
                            class="lg:block hidden mb-1 text-md uppercase text-sm">Société</label>
                        <select id="societe-select"
                            class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-1/2 lg:w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                            <option value="">Sélectionner...</option>
                            @foreach (\App\Models\Society::orderBy('name')->get() as $societe)
                                <option value="societe-{{ $societe->id }}">
                                    {{ $societe->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div
                        class="relative w-full lg:w-1/2 flex lg:flex-col items-center justify-center lg:justify-between gap-1">
                        <label for="societe-select"
                            class="lg:block hidden mb-1 text-md uppercase text-sm">Interlocuteur</label>
                        <select id="interlocuteur-select"
                            class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-1/2 lg:w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                            <option value="" class="font-bold bg-blue-accent text-off-white">Sélectionner...
                            </option>
                            @foreach (\App\Models\Interlocutor::orderBy('fullname')->get() as $interlocuteur)
                                <option value="interlocuteur-{{ $interlocuteur->id }}">
                                    {{ $interlocuteur->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </article>
            <article class="mx-auto">
                <div id="user-search-results" class="mt-4 flex justify-center items-center"></div>
            </article>
        </section>

        <section id="selected-entity-card"
            class="hidden lg:flex flex-col lg:flex-row gap-4 w-full min-w-0 p-1 md:p-2 lg:p-4 flex-1 bg-off-white rounded-lg">
            <article id="card-1"
                class="relative appearance-none rounded-lg px-4 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-accent transition overflow-y-auto overflow-hidden h-[30vh] flex flex-col text-sm md:text-md lg:text-lg">
            </article>
            <article id="card-2"
                class="relative appearance-none rounded-lg px-4 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-accent transition overflow-y-auto overflow-hidden h-[30vh] flex flex-col text-sm md:text-md lg:text-lg">
            </article>
            <div class="border-r border-blue-accent"></div>
            <article id="card-3"
                class="relative appearance-none rounded-lg px-4 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-accent transition overflow-y-auto overflow-hidden h-[30vh] flex flex-col text-sm md:text-md lg:text-lg">
            </article>
            <article id="card-4"
                class="relative appearance-none rounded-lg px-4 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-accent transition overflow-y-auto overflow-hidden h-[30vh] flex flex-col text-sm md:text-md lg:text-lg">
            </article>
        </section>

        <div class="hidden lg:flex flex-col items-center justify-center">
            <div class="border-t border-blue-accent lg:mb-2 w-4/5"></div>
        </div>

        <section
            class="flex flex-col lg:flex-row lg:h-[35vh] bg-off-white rounded-lg mt-1 px-2 md:px-4 lg:px-8 min-h-0 text-sm md:text-md lg:text-lg">
            <article id="problemes-list1"
                class="w-full lg:w-1/2 px-2 md:px-4 py-4 overflow-y-auto overflow-hidden h-full min-h-0">
            </article>
            <div class="border-r border-blue-accent"></div>
            <article id="problemes-list2"
                class="w-full lg:w-1/2 px-2 md:px-8 py-4 overflow-y-auto overflow-hidden h-full min-h-0">
            </article>
        </section>
    </section>

    <script>
        const modal = document.getElementById('add-model-modal');
        const modalOverlay = document.getElementById('modal-overlay');
        const mainContent = document.getElementById('main-content');
        const header = document.getElementById('header');

        // Ouvre la modale et affiche l'overlay
        document.getElementById('add-model-link').addEventListener('click', function() {
            const model = document.getElementById('add-model-select').value;
            const url = "{{ route('model.form', ['model' => ':model', 'action' => 'create']) }}".replace(':model',
                model);

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modalOverlay.classList.add('active'); // Affiche l'overlay
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
                    initTinyMCEInModal();
                    initServiceSelectTinyMCE();
                });
        });

        // Ferme la modale et masque l'overlay
        document.getElementById('close-add-model-modal').addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalOverlay.classList.remove('active'); // Masque l'overlay
            mainContent.classList.remove('modal-blur');
            header.classList.remove('modal-blur');
        });

        // Ferme la modale avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modalOverlay.classList.remove('active'); // Masque l'overlay
                mainContent.classList.remove('modal-blur');
                header.classList.remove('modal-blur');
            }
        });

        // Initialise TinyMCE sur tous les textarea du modal
        function initTinyMCEInModal() {
            if (window.tinymce) {
                tinymce.remove();
                tinymce.init({
                    selector: 'textarea.tinymce, textarea.service-info, #description_form',
                    height: 400,
                    language: 'fr',
                    menubar: false,
                    plugins: 'code link lists align emoticons image table preview textcolor',
                    toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons table | preview code',

                    // suppression des <p>, forcer les <br> à la place
                    forced_root_block: false,
                    force_br_newlines: true,
                    force_p_newlines: false,

                    // styles stricts pour réduire l'interligne
                    content_style: `
      body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 14px;
        margin: 2;
        padding: 2;
        line-height: 1.1;
    }
    p, div {
        margin: 2;
        padding: 2;
    }
    br {
        display: block;
        margin: 2;
        padding: 2;
    }
    p, div {
        margin: 0;
        padding: 0;
    }
    br {
        display: block;
        margin: 0;
        padding: 0;
    }
`,
                });
            }
        }

        // Gère TinyMCE sur les textarea de service affichés dynamiquement
        function initServiceSelectTinyMCE() {
            document.querySelectorAll('.service-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    const wrapper = this.closest('li').querySelector('.service-info-wrapper');
                    const textarea = wrapper ? wrapper.querySelector('.service-info') : null;
                    if (wrapper && textarea) {
                        if (this.value == "1") {
                            wrapper.style.display = "";
                            // Donne un id unique si besoin
                            if (!textarea.id) {
                                textarea.id = 'service_info_' + Math.random().toString(36).substr(2, 9);
                            }
                            // Initialise TinyMCE si pas déjà fait
                            if (!tinymce.get(textarea.id)) {
                                tinymce.init({
                                    selector: '#' + textarea.id,
                                    height: 400,
                                    language: 'fr',
                                    menubar: false,
                                    plugins: 'code link lists align emoticons image table preview textcolor',
                                    toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons table | preview code',

                                    // suppression des <p>, forcer les <br> à la place
                                    forced_root_block: false,
                                    force_br_newlines: true,
                                    force_p_newlines: false,

                                    // styles stricts pour réduire l'interligne
                                    content_style: `
      body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 14px;
        margin: 2;
        padding: 2;
        line-height: 1.1;
    }
    p, div {
        margin: 2;
        padding: 2;
    }
    br {
        display: block;
        margin: 2;
        padding: 2;
    }
    p, div {
        margin: 0;
        padding: 0;
    }
    br {
        display: block;
        margin: 0;
        padding: 0;
    }
`,
                                });
                            }
                        } else {
                            wrapper.style.display = "none";
                            if (textarea.id && tinymce.get(textarea.id)) {
                                tinymce.get(textarea.id).remove();
                            }
                        }
                    }
                });
            });
        }

        window.userRoles = @json(Auth::user()
                ? collect([Auth::user()->isAdmin() ? 'admin' : null, Auth::user()->isSuperAdmin() ? 'superadmin' : null])->filter()->values()
                : []
        );

        window.translatedFields = @json(__('fields'));
        window.currentUserRole = window.userRoles && window.userRoles.length > 0 ? window.userRoles[0] : '';
        
        // Ajout de la fermeture de la modale après un enregistrement réussi dans d'autres cas
        function handleSaveSuccess() {
            closeModalAfterSave(); // Ferme la modale et enlève le flou
        }
      
        // Exemple d'utilisation dans un autre contexte
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    handleSaveSuccess(); // Appelle la fonction pour fermer la modale
                }
            });

        // Ajout d'une vérification globale pour fermer la modale et supprimer le flou après toute interaction réussie
        function closeModalAfterAnySuccess() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalOverlay.classList.remove('active'); // Masque l'overlay
            mainContent.classList.remove('modal-blur');
            header.classList.remove('modal-blur');
        }

        // Ajout de l'appel à cette fonction dans tous les fetch
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModalAfterAnySuccess(); // Ferme la modale et enlève le flou
                }
            });
    </script>
    <script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/6/tinymce.min.js" c
        referrerpolicy="origin"></script>
    <script src="{{ asset('build/assets/dashboard-BzIr33HF.js') }}"></script>
     @vite('resources/js/dashboard.js')
</x-app-layout>
