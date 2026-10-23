<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('icon_sistema.png') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ open: false }" class="h-screen flex flex-col overflow-hidden">

        @include('layouts.sidebar')
        @include('layouts.navigation')
        @include('layouts.top_bar')

        <div class="flex-1 flex flex-col min-h-0 bg-white">

            <div class="flex-1 min-h-0 pt-[5rem] md:ml-56 bg-white">
                <main class="h-full min-h-0 overflow-y-auto px-5 py-2 text-gray-900">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </div>

    @include('imagenes.partials.selector')
</body>

</html>
