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
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|inter:400,500,600,700|roboto:400,500,600,700|nunito-sans:400,500,600,700&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->


    <script>
        (() => {
            const temas = ['light', 'dim', 'dark'];
            const acentos = ['indigo', 'blue', 'teal'];
            const fuentes = ['figtree', 'inter', 'roboto', 'nunito'];

            const theme = localStorage.getItem('app-theme');
            const accent = localStorage.getItem('app-accent');
            const font = localStorage.getItem('app-font');

            document.documentElement.dataset.theme =
                temas.includes(theme) ? theme : 'light';

            document.documentElement.dataset.accent =
                acentos.includes(accent) ? accent : 'indigo';

            document.documentElement.dataset.font =
                fuentes.includes(font) ? font : 'figtree';
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-app-background">
    <div x-data="{ open: false }" class="h-screen flex flex-col overflow-hidden">

        @include('layouts.sidebar')
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col min-h-0 bg-app-surface">

            <div class="flex-1 min-h-0 md:ml-56 bg-app-surface">
                <main
                    class="h-full min-h-0 overflow-y-auto scrollbar-app px-5 py-2 mt-6 pb-10 text-app-text-primary text-[15px]">
                    @include('layouts.top_bar')
                    {{ $slot }}
                </main>
            </div>

        </div>
    </div>

    @include('imagenes.partials.selector')
    @stack('scripts')
</body>

</html>
