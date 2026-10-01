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
    <div x-data="{ open: false }" class="min-h-screen flex flex-col">

        @include('layouts.sidebar')

        @include('layouts.top_bar')

        @include('layouts.navigation')

        <div class="flex-1 flex flex-col md:pl-56 pt-28">

            @isset($header)
                <header class="bg-white shadow-sm mt-1 mb-1 mx-4 sm:mx-6 sm:rounded-lg">
                    <div class="w-full px-4 sm:px-6 py-2">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 px-3 sm:px-3 pb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded p-5 text-gray-900">
                    {{ $slot }}
                </div>
            </main>

        </div>

    </div>
</body>

</html>