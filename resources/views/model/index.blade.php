<x-app-layout>
    <h1 class="text-md sm:text-xl text-blue-accent font-bold my-4 sm:my-8 text-center uppercase">Liste des {{ $model }}s</h1>
    <div class="w-full mx-auto px-2 sm:px-4 md:px-16 lg:px-32 xl:px-40">

        <div class="mb-2 sm:mb-4 flex justify-center">
            <input 
                type="text" 
                id="search" 
                placeholder="Rechercher un {{ strtolower($model) }}"
                class="appearance-none border-2 border-blue-accent rounded-lg px-3 py-2 w-full max-w-xs sm:max-w-md bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition text-sm sm:text-base"
            >
        </div>

        <div class="rounded-lg max-h-[60vh] overflow-y-auto">
            <table class="w-full text-sm sm:text-base">
                <thead>
                    <tr>
                        <th class="py-1 sm:py-2 px-1 sm:px-4 border-b text-center bg-blue-accent text-secondary-grey sticky top-0 left-0 z-10"></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach ($items as $item)
                        <tr class="border-b">
                            <td class="py-1 sm:py-2 px-1 sm:px-4 text-center bg-off-white hover:text-blue-accent">
                                <a href="{{ route('model.show', ['model' => $model, 'id' => $item->id]) }}"
                                    class="ml-1 sm:ml-4 text-blue-500 hover:underline block truncate text-sm sm:text-base">
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
        <div class="py-1 sm:py-2 px-1 sm:px-4 border-b text-center bg-blue-accent text-secondary-grey rounded-lg"></div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const tableBody = document.getElementById('table-body');
        let selectedIndex = -1;

        function updateSelection() {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, idx) => {
                const cell = row.querySelector('td');
                if (idx === selectedIndex) {
                    row.classList.add('bg-blue-accent', 'text-white');
                    if (cell) {
                        cell.classList.add('bg-blue-accent', 'text-white');
                        cell.classList.remove('bg-off-white');
                        cell.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                } else {
                    row.classList.remove('bg-blue-accent', 'text-white');
                    if (cell) {
                        cell.classList.remove('bg-blue-accent', 'text-white');
                        cell.classList.add('bg-off-white');
                    }
                }
            });
        }

        searchInput.addEventListener('keydown', function(e) {
            const rows = tableBody.querySelectorAll('tr');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (selectedIndex < rows.length - 1) selectedIndex++;
                updateSelection();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (selectedIndex > 0) selectedIndex--;
                updateSelection();
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && rows[selectedIndex]) {
                    const link = rows[selectedIndex].querySelector('a');
                    if (link) link.click();
                }
            }
        });

        searchInput.addEventListener('input', function() {
            let query = this.value;
            fetch(`{{ route('model-suggestions', ['model' => $model]) }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    // Sélectionne automatiquement la première ligne si elle existe
                    const rows = tableBody.querySelectorAll('tr');
                    selectedIndex = rows.length > 0 ? 0 : -1;
                    updateSelection();
                    searchInput.focus();
                });
        });
    </script>

</x-app-layout>
