<x-app-layout>
    <h1 class="text-3xl text-center uppercase font-bold my-6 text-blue-accent">Détail de {{ $model }}</h1>
    <div class="bg-off-white rounded-lg shadow p-6 max-w-[80%] mx-auto">
        <div class="mb-4 flex justify-center">
            <input type="text" id="detail-search" placeholder="Rechercher un champ ou une valeur..."
                class="border rounded px-4 py-2 w-1/2">
        </div>
        <div class="px-8" style="max-height:600px; overflow-y: auto;">
            <ul class="divide-y divide-primary-grey" id="details-list">
                @foreach ($item->getAttributes() as $key => $value)
                    @if ($key !== 'id')
                        <li class="p-3 flex items-center justify-between h-auto group">
                            <span class="font-semibold text-blue-accent w-40 mr-20">{{ $key }}</span>
                            <span
                                class="editable text-primary-grey w-[55rem] px-2 py-1 rounded transition
                                    cursor-text outline-none text-right
                                    focus:border-blue-accent border border-off-white
                                    group-hover:border-blue-hover"
                                contenteditable="true" data-field="{{ $key }}" data-id="{{ $item->id }}"
                                data-model="{{ $model }}" tabindex="0">{{ $value }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    <script>
        document.getElementById('detail-search').addEventListener('input', function() {
            let query = this.value.toLowerCase();
            document.querySelectorAll('#details-list li').forEach(function(li) {
                let text = li.innerText.toLowerCase();
                li.style.display = text.includes(query) ? '' : 'none';
            });
        });

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
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            field,
                            value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Erreur lors de la mise à jour');
                        }
                    });
            });
        });
    </script>
</x-app-layout>
