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
                <header class="bg-white border-b border-gray-200/60 shadow-2xs">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 p-4 sm:p-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 border border-gray-200/60">
                    {{ $slot }}
                </div>
            </main>

        </div>

    </div>
</body>

</html>