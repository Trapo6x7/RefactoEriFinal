@extends(backpack_view('blank'))

@section('content')
<div class="mb-4">
    <form id="global-search-form"
        class="row  g-md-3 align-items-stretch shadow rounded-3"
        style="background:#181622;">
        <div class="col-12 col-md">
            <input type="text" name="q" id="global-search-input"
                class="form-control bg-dark text-light border-0 h-100"
                placeholder="Recherche globale..."
                style="border-radius: 0.5rem;">
        </div>
        <div class="col-12 col-md-auto">
            <select name="table" id="global-search-table"
                class="form-select bg-dark text-light border-0 h-100"
                style="max-width:180px;">
                <option value="">Toutes les tables</option>
                <option value="users">Utilisateurs</option>
                <option value="societies">Sociétés</option>
                <option value="techs">Techniciens</option>
                <option value="problems">Problèmes</option>
                <option value="problemStatuses">Statuts</option>
                <option value="interlocutors">Interlocuteurs</option>
                <option value="envs">Environnements</option>
                <option value="tools">Outils</option>
                <option value="menus">Menus</option>
            </select>
        </div>
        <div class="col-12 col-md-auto">
            <button class="btn btn-primary w-100 w-md-auto"
                type="submit"
                style="border-radius: 0.5rem;">Rechercher</button>
        </div>
    </form>
</div>

<div id="global-search-results"></div>

    @foreach (\App\Models\Menu::orderBy('order')->get() as $item)
        @if (!$item->role || $item->role === backpack_user()->role)
            <x-backpack::menu-item :title="$item->title" :icon="$item->icon ?: 'la la-bars'" :link="$item->link" />
        @endif
    @endforeach
@endsection

@push('after_scripts')
    <script>
        document.getElementById('global-search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            let q = document.getElementById('global-search-input').value;
            let table = document.getElementById('global-search-table').value;
            fetch("{{ route('global-search') }}?q=" + encodeURIComponent(q) + "&table=" + encodeURIComponent(table))
                .then(response => response.text())
                .then(html => {
                    document.getElementById('global-search-results').innerHTML = html;
                });
        });

        const input = document.getElementById('global-search-input');
        const tableSelect = document.getElementById('global-search-table');
        let suggestionBox = document.createElement('div');
        suggestionBox.className = 'list-group position-absolute w-100';
        suggestionBox.style.zIndex = 1000;
        input.parentNode.appendChild(suggestionBox);

        input.addEventListener('input', function() {
            let q = this.value;
            let table = tableSelect.value;
            if (q.length < 2) {
                suggestionBox.innerHTML = '';
                return;
            }
            fetch("{{ route('global-suggestions') }}?q=" + encodeURIComponent(q) + "&table=" + encodeURIComponent(
                    table))
                .then(response => response.json())
                .then(data => {
                    suggestionBox.innerHTML = '';
                    if (data.length) {
                        data.forEach(suggestion => {
                            let item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = suggestion;
                            item.onclick = function() {
                                input.value = suggestion;
                                suggestionBox.innerHTML = '';
                            };
                            suggestionBox.appendChild(item);
                        });
                    }
                });
        });
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.innerHTML = '';
            }
        });
    </script>
@endpush
