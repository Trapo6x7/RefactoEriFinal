<x-app-layout>
    <h1 class="text-xl font-bold my-8 text-center uppercase">Liste des {{ $model }}s</h1>
    <div class="w-full mx-auto px-40">

        <div class="mb-4 flex justify-center">
            <input type="text" id="search" placeholder="Rechercher un {{ strtolower($model) }}"
                class="border rounded px-4 py-2 w-1/2">
        </div>

        <div class=" rounded-lg" style="max-height: 800px; overflow-y: auto;">
            <table class="w-full text-lg">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b text-center bg-blue-accent text-secondary-grey">NOM</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach ($items as $item)
    
                        <tr class="border-b">
                            <td class="py-2 px-4 text-center bg-off-white hover:text-blue-accent">
                                <a href="{{ route('model.show', ['model' => $model, 'id' => $item->id]) }}"
                                    class="ml-4 text-blue-500 hover:underline">
                                    @if ($model === 'interlocuteur')
                                        {{ $item->fullname ?? '-' }}
                                    @else
                                        {{ $item->name ?? ($item->title ?? '-') }}
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('search').addEventListener('input', function() {
            let query = this.value;
            fetch(`{{ route('model-suggestions', ['model' => $model]) }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('table-body').innerHTML = html;
                });
        });
    </script>
</x-app-layout>
