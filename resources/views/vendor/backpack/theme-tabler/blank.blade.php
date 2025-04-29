@extends(backpack_view('layouts.' . (backpack_theme_config('layout') ?? 'vertical')))

@php
    $currentId = $entry->id ?? $currentId ?? null;

    use Illuminate\Support\Str;
    $segments = request()->segments();
    $isShowPage = count($segments) >= 4 && $segments[0] === 'admin' && $segments[3] === 'show';
    $contextTable = $isShowPage ? $segments[1] : null;

    if (isset($widgets)) {
        foreach ($widgets as $section => $widgetSection) {
            foreach ($widgetSection as $key => $widget) {
                \Backpack\CRUD\app\Library\Widget::add($widget)->section($section);
            }
        }
    }
@endphp

@section('before_breadcrumbs_widgets')
    <div class="row mb-5 align-items-center">
        <div class="col-12 col-md">
            <h1 class="text-center text-md-start display-6 display-md-4 fw-bold text-primary text-break">
                {{ __('BIENVENUE, :name', ['name' => strtoupper(Auth::user()->name)]) }}
            </h1>
        </div>
        <div class="col-12 col-md-auto mt-3 mt-md-0 text-md-end">
            <a class="btn btn-primary w-100 w-md-auto" href="{{ backpack_url('logout') }}" role="button">Déconnexion</a>
        </div>
    </div>
    @parent
@endsection

@section('after_breadcrumbs_widgets')
    @include(backpack_view('inc.widgets'), [
        'widgets' => app('widgets')->where('section', 'after_breadcrumbs')->toArray(),
    ])
@endsection

@section('before_content_widgets')
    @if ($isShowPage)
        <div class="mb-4">
            <form id="context-search-form" class="row g-2 g-md-3 align-items-stretch shadow rounded-3">
                <div class="col-12 col-md position-relative">
                    <input type="text" name="q" id="context-search-input"
                        class="form-control bg-dark text-light border-0 h-100"
                        placeholder="Recherche dans cette {{ Str::singular($contextTable) }}..."
                        style="border-radius: 0.5rem;">
                </div>
                <div class="col-12 col-md-auto">
                    <button class="btn btn-primary w-100 w-md-auto" type="submit"
                        style="border-radius: 0.5rem;">Rechercher</button>
                </div>
            </form>
        </div>
        <div id="context-search-results"></div>
    @endif

    @parent
@endsection

@section('content')
@endsection

@section('after_content_widgets')
    @include(backpack_view('inc.widgets'), [
        'widgets' => app('widgets')->where('section', 'after_content')->toArray(),
    ])
@endsection

@if ($isShowPage)
    @push('after_scripts')
        <script>
            document.getElementById('context-search-form').addEventListener('submit', function(e) {
                e.preventDefault();
                let q = document.getElementById('context-search-input').value;
                // Envoie la table courante (ex: society)
                fetch("{{ url('/contextual-search') }}?q=" + encodeURIComponent(q) +
                        "&table={{ $contextTable }}&id={{ $currentId ?? '' }}")
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('context-search-results').innerHTML = html;
                    });
            });

            // Autocomplétion AJAX
            const contextInput = document.getElementById('context-search-input');
            let contextSuggestionBox = document.createElement('div');
            contextSuggestionBox.className = 'list-group position-absolute w-100';
            contextSuggestionBox.style.zIndex = 1000;
            contextInput.parentNode.appendChild(contextSuggestionBox);

            contextInput.addEventListener('input', function() {
                let q = this.value;
                if (q.length < 2) {
                    contextSuggestionBox.innerHTML = '';
                    return;
                }
                fetch("{{ url('/contextual-suggestions') }}?q=" + encodeURIComponent(q) +
                        "&table={{ $contextTable }}&id={{ $currentId ?? '' }}")
                    .then(response => response.json())
                    .then(data => {
                        contextSuggestionBox.innerHTML = '';
                        if (data.length) {
                            data.forEach(suggestion => {
                                let item = document.createElement('button');
                                item.type = 'button';
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = suggestion;
                                item.onclick = function() {
                                    contextInput.value = suggestion;
                                    contextSuggestionBox.innerHTML = '';
                                };
                                contextSuggestionBox.appendChild(item);
                            });
                        }
                    });
            });

            // Fermer la box si on clique ailleurs
            document.addEventListener('click', function(e) {
                if (!contextInput.contains(e.target) && !contextSuggestionBox.contains(e.target)) {
                    contextSuggestionBox.innerHTML = '';
                }
            });
        </script>
    @endpush
@endif
