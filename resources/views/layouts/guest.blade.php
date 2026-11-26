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

<body class="font-sans text-app-text-primary antialiased">

    <div
        class="relative flex min-h-screen flex-col items-center justify-center
                overflow-hidden bg-app-background px-4 py-6">

        <!-- Fondo degradado -->
        <div
            class="pointer-events-none absolute inset-0
                   bg-linear-to-br
                   from-app-primary-500/10
                   via-app-background
                   to-app-primary-700/15">
        </div>

        <!-- Patrón geométrico -->
        <div
            class="pointer-events-none absolute inset-0
                   opacity-[0.12]
                   [background-image:linear-gradient(var(--app-border)_1px,transparent_1px),linear-gradient(90deg,var(--app-border)_1px,transparent_1px)]
                   [background-size:42px_42px]">
        </div>

        <!-- Contenido -->
        <div class="relative z-10 w-full">
            {{ $slot }}
        </div>

    </div>

</body>

</html>
