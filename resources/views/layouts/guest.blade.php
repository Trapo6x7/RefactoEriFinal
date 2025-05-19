<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-R.I') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('imgs/logogrissanstexte.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('build/assets/app-BlsAu_6K.css') }}">
    <script src="{{ asset('build/assets/app-Bf4POITK.js') }}"></script>
</head>

<body class="font-sans text-primary-grey bg-off-white antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <img src="{{ asset('imgs/logobleu.png') }}" alt="logo" class="w-32">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
