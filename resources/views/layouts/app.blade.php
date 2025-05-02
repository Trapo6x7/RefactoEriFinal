<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E.R.I.') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('imgs/logogrissanstexte.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="font-sans antialiased min-h-screen flex flex-col justify-between">
    <div class="min-h-screen ">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header>
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
        {{-- @include('layouts.footer') --}}
    </div>
</body>


<script>
    document.getElementById('global-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        let q = document.getElementById('global-search-input').value;
        let table = document.getElementById('global-search-table').value;
        fetch("{{ route('user-search') }}?q=" + encodeURIComponent(q) + "&table=" + encodeURIComponent(table))
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

</html>
