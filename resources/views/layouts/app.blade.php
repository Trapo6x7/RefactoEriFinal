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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Scripts -->

    <link rel="stylesheet" href="{{ asset('build/assets/app-Bu_bwFjy.css') }}">
    <script defer src="{{ asset('build/assets/app-Bf4POITK.js') }}" type="module"></script>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body class="text-sm md:text-md lg:text-lg font-sans antialiased min-h-screen flex flex-col bg-off-white">

    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header id="dashboard-header" class="hidden text-sm md:text-md lg:text-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sm md:text-md lg:text-lg">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>
    @include('layouts.footer')

</body>
<script>
    window.translatedFields = @json(__('fields'));
    window.currentUserRole = "{{ Auth::check() ? Auth::user()->role : '' }}";
</script>

</html>
