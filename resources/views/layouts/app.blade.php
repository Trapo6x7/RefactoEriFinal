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

    <link rel="stylesheet" href="{{ asset('build/assets/app-BaLUhxgp.css') }}">
<script src="{{ asset('build/assets/app-D5UJA6KW.js') }}" type="module"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="font-sans antialiased min-h-screen flex flex-col justify-between">
    <div class="min-h-screen ">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header id="dashboard-header" class="hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    window.translatedFields = @json(__('fields'));
    window.currentUserRole = "{{ Auth::user()->role }}";
</script>

</html>
