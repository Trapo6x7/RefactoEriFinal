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
                            !str_starts_with($key, 'service') &&
                            !is_null($value) &&
                            $value !== '' &&
                            $value !== 0 &&
                            $value !== '0')
                        <li class="p-3 flex items-center justify-between h-auto group">
                            <span class="font-semibold text-blue-accent w-40 mr-20">
                                {{ $fields[$key] ?? $key }}
                            </span>
                            <span
                                class="editable text-primary-grey w-[55rem] px-2 py-1 rounded transition
                        cursor-text outline-none text-right
                        focus:border-blue-accent border border-off-white
                        group-hover:border-blue-hover"
                                contenteditable="true" data-field="{{ $key }}" data-id="{{ $item->id }}"
                                data-model="{{ $model }}" tabindex="0">
                                @if ((in_array($key, ['status_client', 'status_distrib']) || str_starts_with($key, 'statut')) && $value == 1)
                                    Oui
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifie le rôle JS (à adapter selon comment tu exposes le rôle)
            if (
                window.currentUserRole && ["admin", "superadmin"].includes(window.currentUserRole.toLowerCase())
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
                                        'meta[name="csrf-token"]')?.getAttribute(
                                        "content") || '{{ csrf_token() }}',
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
                                    span.style.background = "#678BD8";
                                    setTimeout(() => span.style.background = "", 500);
                                }
                            })
                            .catch(() => {
                                span.style.background = "#DB7171";
                                setTimeout(() => span.style.background = "", 1000);
                            });
                    });
                });
            }
        });
    </script>

</x-app-layout>
