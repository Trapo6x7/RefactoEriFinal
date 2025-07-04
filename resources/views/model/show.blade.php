<?php
use Illuminate\Support\Str;
?>
@php
    $fields = __('fields');
    $attributes = $item->getAttributes();

    // Séparer les champs service_ des autres
    $serviceKeys = [];
    $otherKeys = [];
    foreach ($attributes as $key => $value) {
        if (str_starts_with($key, 'service_')) {
            $serviceKeys[] = $key;
        } elseif (!str_starts_with($key, 'infos_')) {
            $otherKeys[] = $key;
        }
    }
    // Statut combiné
    $client = $attributes['status_client'] ?? 0;
    $distrib = $attributes['status_distrib'] ?? 0;
    $selectedStatus = $client && $distrib ? 'both' : ($client ? 'client' : ($distrib ? 'distrib' : 'none'));
@endphp

<x-app-layout>
    <h1 class="text-md md:text-lg text-center uppercase font-bold my-6 text-blue-accent">Détail de {{ $model }}
    </h1>

    <div class="bg-off-white rounded-lg p-4 md:p-6 max-w-full md:max-w-[80%] mx-auto">
        <div class="mb-4 flex justify-center">
            <input type="text" id="detail-search" placeholder="Rechercher un champ ou une valeur..."
                class="appearance-none border-2 border-blue-accent rounded-lg px-3 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition" />
        </div>
        <div class="px-2 md:px-2 w-full" style="max-height:600px; overflow-y: auto;">
            <ul class="divide-y divide-primary-grey w-full" id="details-list">
                {{-- Sélecteur combiné Client/Distributeur --}}
                @if ($model === 'societe')
                    <li class="p-3 flex flex-col items-start justify-between h-auto group gap-2">
                        <span class="font-semibold text-blue-accent w-full mb-2">
                            Statut Client/Distributeur
                        </span>
                        <select name="status_combined" id="status_combined"
                            class="px-4 py-2 border border-secondary-grey rounded-lg text-base w-full focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
                            data-id="{{ $item->id }}" data-model="{{ $model }}">
                            <option value="client" {{ $selectedStatus == 'client' ? 'selected' : '' }}>Client</option>
                            <option value="distrib" {{ $selectedStatus == 'distrib' ? 'selected' : '' }}>Distributeur
                            </option>
                            <option value="both" {{ $selectedStatus == 'both' ? 'selected' : '' }}>Client &
                                Distributeur</option>
                        </select>
                    </li>
                @endif

                {{-- Champs non-service (hors description) --}}
                @foreach ($otherKeys as $key)
                    @php
                        $value = $attributes[$key];
                        $relationMap = [
                            'tool' => 'tool',
                            'env' => 'env',
                            'societe' => 'society',
                            'society' => 'society',
                        ];
                    @endphp
                    @if (
                        $key !== 'id' &&
                            $key !== 'created_at' &&
                            $key !== 'updated_at' &&
                            $key !== 'status_client' &&
                            $key !== 'status_distrib' &&
                            $key !== 'description')
                        <li class="p-3 flex flex-col items-start justify-between h-auto group gap-2">
                            <span class="font-semibold text-blue-accent w-full mb-2">
                                {{ $fields[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                            </span>
                            @if ($key === 'status')
                                <select class="editable-select border rounded px-2 py-1 w-full text-base"
                                    style="width: 100%;" data-field="{{ $key }}" data-id="{{ $item->id }}"
                                    data-model="{{ $model }}">
                                    <option value="active" @if ($value === 'active') selected @endif>Active
                                    </option>
                                    <option value="inactive" @if ($value === 'inactive') selected @endif>Inactive
                                    </option>
                                </select>
                            @elseif (array_key_exists($key, $relationMap) && isset($item->getRelations()[$relationMap[$key]]))
                                <span
                                    class="editable text-primary-grey w-full px-2 py-1 rounded transition cursor-text outline-none text-right focus:border-blue-accent border border-off-white group-hover:border-blue-hover"
                                    contenteditable="false">
                                    {{ $item->getRelations()[$relationMap[$key]]->name ??
                                        ($item->getRelations()[$relationMap[$key]]->nom ?? $value) }}
                                </span>
                            @else
                                <span
                                    class="editable text-primary-grey w-full px-2 py-1 rounded transition cursor-text outline-none text-right focus:border-blue-accent border border-off-white group-hover:border-blue-hover"
                                    contenteditable="true" data-field="{{ $key }}"
                                    data-id="{{ $item->id }}" data-model="{{ $model }}" tabindex="0">
                                    @if (str_starts_with($key, 'statut') && $value == 1)
                                        Oui
                                    @elseif (str_starts_with($key, 'statut') && $value == 0)
                                        Non
                                    @else
                                        {{ $value }}
                                    @endif
                                </span>
                            @endif
                        </li>
                    @endif
                @endforeach

                {{-- Description du problème (TinyMCE, pas editable inline) --}}
                @if (isset($attributes['description']))
                    <li class="p-3 flex flex-col items-start justify-between h-auto group w-full gap-2">
                        <span class="font-semibold text-blue-accent w-full mb-2">
                            {{ $fields['description'] ?? 'Description' }}
                        </span>
                        <textarea id="descriptionProbleme" name="description" class="w-full border rounded px-2 py-1"
                            style="max-width:100%;min-width:0;width:100%;" data-id="{{ $item->id }}" data-model="{{ $model }}">{{ $attributes['description'] }}</textarea>
                    </li>
                @endif

                {{-- Champs service (toujours à la fin) --}}
                @foreach ($serviceKeys as $key)
                    @php
                        $value = $attributes[$key];
                        $infoKey = 'infos_' . Str::after($key, 'service_');
                        $infoValue = array_key_exists($infoKey, $attributes) ? $attributes[$infoKey] : '';
                    @endphp
                    <li class="p-3 flex flex-col items-start justify-between h-auto group gap-2">
                        <span class="font-semibold text-blue-accent w-full mb-2">
                            {{ $fields[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                        </span>
                        <div class="flex flex-col gap-2 w-full">
                            <div class="flex justify-end">
                                <select class="service-select border rounded px-2 py-1 w-full text-base"
                                    data-field="{{ $key }}" data-id="{{ $item->id }}"
                                    data-model="{{ $model }}">
                                    <option value="1" @if ($value == 1) selected @endif>Oui
                                    </option>
                                    <option value="0" @if ($value == 0) selected @endif>Non
                                    </option>
                                </select>
                            </div>
                            <div class="service-info-wrapper"
                                style="@if ($value != 1) display:none; @endif">
                                <textarea class="service-info w-full border rounded px-2 py-1 mt-2" placeholder="Infos service..."
                                    data-field="{{ $infoKey }}" data-id="{{ $item->id }}" data-model="{{ $model }}">{!! $infoValue !!}</textarea>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="flex justify-center mt-4">
            <button id="delete-item-btn"
                class="px-4 py-2 text-base bg-red-accent text-white rounded hover:bg-red-hover w-1/2 md:w-1/4">
                Supprimer
            </button>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/6.8.5-39/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        window.currentUserRole = "{{ strtolower(auth()->user()->role ?? '') }}";

        // Fonction d'initialisation globale
        function initTinyMCEAndApp() {
            // --- Initialisation TinyMCE sur description et services ---
            tinymce.init({
                selector: '#descriptionProbleme, textarea.service-info',
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
        margin: 2;
        padding: 2;
    }
    br {
        display: block;
        margin: 2;
        padding: 2;
    }
`,
            });
            // --- Champs éditables inline ---
            if (["admin", "superadmin"].includes(window.currentUserRole)) {
                document.querySelectorAll('.editable').forEach(function(span) {
                    span.addEventListener('blur', function() {
                        const value = this.innerText;
                        const field = this.dataset.field;
                        const id = this.dataset.id;
                        const model = this.dataset.model;
                        fetch(`/model/${model}/update-field/${id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute("content"),
                                    Accept: 'application/json'
                                },
                                body: JSON.stringify({
                                    field,
                                    value
                                }),
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (!data.success) {
                                    alert('Erreur lors de la mise à jour');
                                } else {
                                    this.style.background = "#678BD8";
                                    setTimeout(() => this.style.background = "", 500);
                                }
                            })
                            .catch(() => {
                                this.style.background = "#DB7171";
                                setTimeout(() => this.style.background = "", 1000);
                            });
                    });
                });

                // --- Select Oui/Non des services ---
                document.querySelectorAll('.service-select').forEach(function(select) {
                    select.addEventListener('change', function() {
                        const value = this.value;
                        const wrapper = this.closest('li').querySelector('.service-info-wrapper');
                        const textarea = wrapper ? wrapper.querySelector('.service-info') : null;
                        if (wrapper) {
                            if (value == "1") {
                                wrapper.style.display = "";
                                // Si tu ajoutes dynamiquement un textarea, réinitialise TinyMCE :
                                // tinymce.remove();
                                // tinymce.init({ selector: '#descriptionProbleme, textarea.service-info', ... });
                            } else {
                                wrapper.style.display = "none";
                                // Optionnel : tu peux retirer l'éditeur si tu veux
                                // if (textarea && tinymce.get(textarea.id)) tinymce.get(textarea.id).remove();
                            }
                        }
                    });
                });
            }

            // --- Select statut (active/inactive) ---
            document.querySelectorAll('.editable-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    const value = this.value;
                    const field = this.dataset.field;
                    const id = this.dataset.id;
                    const model = this.dataset.model;
                    fetch(`/model/${model}/update-field/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                                Accept: 'application/json'
                            },
                            body: JSON.stringify({
                                field,
                                value
                            }),
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Erreur lors de la mise à jour');
                            } else {
                                this.style.background = "#678BD8";
                                setTimeout(() => this.style.background = "", 500);
                            }
                        })
                        .catch(() => {
                            this.style.background = "#DB7171";
                            setTimeout(() => this.style.background = "", 1000);
                        });
                });
            });

            // --- Select combiné Client/Distributeur ---
            const statusCombined = document.getElementById('status_combined');
            if (statusCombined) {
                statusCombined.addEventListener('change', function() {
                    const value = this.value;
                    const id = this.dataset.id;
                    const model = this.dataset.model;
                    let status_client = 0,
                        status_distrib = 0;
                    if (value === 'client') status_client = 1;
                    if (value === 'distrib') status_distrib = 1;
                    if (value === 'both') {
                        status_client = 1;
                        status_distrib = 1;
                    }
                    fetch(`/model/${model}/update-field/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content"),
                            Accept: 'application/json'
                        },
                        body: JSON.stringify({
                            field: 'status_client',
                            value: status_client
                        }),
                    }).then(() => {
                        fetch(`/model/${model}/update-field/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                                Accept: 'application/json'
                            },
                            body: JSON.stringify({
                                field: 'status_distrib',
                                value: status_distrib
                            }),
                        });
                    });
                });
            }

            // --- Recherche dans la liste des détails ---
            const searchInput = document.getElementById('detail-search');
            const detailsList = document.getElementById('details-list');
            if (searchInput && detailsList) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    detailsList.querySelectorAll('li').forEach(function(li) {
                        const text = li.textContent.toLowerCase();
                        li.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            }

            // --- Suppression ---
            const deleteBtn = document.getElementById('delete-item-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    if (confirm('Voulez-vous vraiment supprimer cet élément ?')) {
                        fetch(`/model/{{ $model }}/delete/{{ $item->id }}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => {
                                if (res.ok) {
                                    window.location.href = "{{ route('model.index', ['model' => $model]) }}";
                                } else {
                                    alert('Erreur lors de la suppression.');
                                }
                            })
                            .catch(() => alert('Erreur lors de la suppression.'));
                    }
                });
            }
        }

        // Attendre que TinyMCE soit chargé avant d'initialiser
        if (window.tinymce) {
            initTinyMCEAndApp();
        } else {
            let check = setInterval(function() {
                if (window.tinymce) {
                    clearInterval(check);
                    initTinyMCEAndApp();
                }
            }, 50);
        }
    </script>
</x-app-layout>
