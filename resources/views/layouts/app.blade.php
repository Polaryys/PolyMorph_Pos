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
        @include('layouts.navigation')
        @include('layouts.top_bar')



        <div class="flex-1 flex flex-col min-h-screen">


            <main class="flex-1 flex">
                <div class="flex-1 min-w-0 bg-white overflow-hidden p-5 ml-2.5 text-gray-900 border border-gray-200/60">
                    {{ $slot }}
                </div>
            </main>

        </div>

    </div>
    @include('imagenes.partials.selector')

</body>

</html>
