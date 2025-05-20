<x-app-layout>
    @php
        $fields = __('fields');
    @endphp
    <h1 class="text-3xl text-center uppercase font-bold my-6 text-blue-accent">Détail de {{ $model }}</h1>

    <div class="bg-off-white rounded-lg p-6 max-w-[80%] mx-auto">
        <div class="mb-4 flex justify-center">
            <input type="text" id="detail-search" placeholder="Rechercher un champ ou une valeur..."
                class="border rounded px-4 py-2 w-1/2">
        </div>
        <div class="px-8" style="max-height:600px; overflow-y: auto;">
            <ul class="divide-y divide-primary-grey" id="details-list">
                @foreach ($item->getAttributes() as $key => $value)
                    @if (
                        $key !== 'id' &&
                            $key !== 'created_at' &&
                            $key !== 'updated_at' &&
                            !str_starts_with($key, 'service') &&
                            !is_null($value) &&
                            $value !== '' &&
                            $value !== 0 &&
                            $value !== '0')
                        <li class="p-3 flex items-start justify-between h-auto group"> {{-- <-- items-start ici --}}
                            <span class="font-semibold text-blue-accent w-40 mr-20">
                                {{ $fields[$key] ?? $key }}
                            </span>
                            @if ($model === 'probleme' && $key === 'description')
                                @if (in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'superadmin']))
                                    <form id="desc-form" class="flex flex-col items-end gap-2 w-full">
                                        <textarea id="ckeditor-description" name="description" class="w-[55rem] min-h-[120px]">{{ $value }}</textarea>
                                        <div class="flex flex-row items-center justify-end w-[55rem] gap-2">
                                            <span id="desc-status" class="text-sm order-1"></span>
                                            <button type="submit" id="desc-save-btn"
                                                class="order-2 px-3 py-1 bg-blue-accent text-white rounded">Enregistrer</button>
                                        </div>
                                    </form>
                                    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
                                    <script>
                                        let ckeditorInstance;
                                        ClassicEditor
                                            .create(document.querySelector('#ckeditor-description'))
                                            .then(editor => {
                                                ckeditorInstance = editor;
                                            })
                                            .catch(error => {
                                                console.error(error);
                                            });

                                        document.getElementById('desc-form').addEventListener('submit', function(e) {
                                            e.preventDefault();
                                            const btn = document.getElementById('desc-save-btn');
                                            const status = document.getElementById('desc-status');
                                            btn.disabled = true;
                                            status.textContent = 'Sauvegarde...';
                                            status.className = 'text-sm text-secondary-grey order-1';

                                            fetch(`/model/{{ $model }}/update-field/{{ $item->id }}`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                                            "content") || '{{ csrf_token() }}',
                                                        Accept: 'application/json'
                                                    },
                                                    body: JSON.stringify({
                                                        field: 'description',
                                                        value: ckeditorInstance.getData()
                                                    }),
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    if (!data.success) {
                                                        status.textContent = 'Erreur lors de la mise à jour';
                                                        status.className = 'text-sm text-red-accent order-1';
                                                    } else {
                                                        status.textContent = 'Enregistré !';
                                                        status.className = 'text-sm text-blue-accent order-1';
                                                    }
                                                    btn.disabled = false;
                                                })
                                                .catch(() => {
                                                    status.textContent = 'Erreur réseau';
                                                    status.className = 'text-sm text-red-hover order-1';
                                                    btn.disabled = false;
                                                });
                                        });
                                    </script>
                                @else
                                    <div class="w-[55rem] px-2 py-1 rounded transition bg-white border border-off-white text-left prose text-md"
                                        style="min-height:120px; display:block;">
                                        {!! $value !!}
                                    </div>
                                @endif
                            @else
                                @php
                                    $relationMap = [
                                        'tool' => 'tool',
                                        'env' => 'env',
                                        'societe' => 'society',
                                        'society' => 'society',
                                    ];
                                @endphp

                                @if (array_key_exists($key, $relationMap) && isset($item->getRelations()[$relationMap[$key]]))
                                    <span
                                        class="editable text-primary-grey w-[55rem] px-2 py-1 rounded transition
        cursor-text outline-none text-right
        focus:border-blue-accent border border-off-white
        group-hover:border-blue-hover"
                                        contenteditable="false">
                                        {{ $item->getRelations()[$relationMap[$key]]->name ?? ($item->getRelations()[$relationMap[$key]]->nom ?? $value) }}
                                    </span>
                                @else
                                    <span
                                        class="editable text-primary-grey w-[55rem] px-2 py-1 rounded transition
        cursor-text outline-none text-right
        focus:border-blue-accent border border-off-white
        group-hover:border-blue-hover"
                                        contenteditable="true" data-field="{{ $key }}"
                                        data-id="{{ $item->id }}" data-model="{{ $model }}" tabindex="0">
                                        @if ((in_array($key, ['status_client', 'status_distrib']) || str_starts_with($key, 'statut')) && $value == 1)
                                            Oui
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                @endif
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="flex justify-center mt-4">
            <button id="delete-item-btn" class="px-2 py-1 text-md bg-red-accent text-white rounded hover:bg-red-hover">
                Supprimer
            </button>
        </div>
    </div>

    <script>
        window.currentUserRole = "{{ strtolower(auth()->user()->role ?? '') }}";
        document.addEventListener('DOMContentLoaded', function() {
            if (
                window.currentUserRole && ["admin", "superadmin"].includes(window.currentUserRole)
            ) {
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
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute("content"),
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
            }
        });
    </script>

</x-app-layout>
