<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('icon_sistema.png') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|inter:400,500,600,700|roboto:400,500,600,700|nunito-sans:400,500,600,700&display=swap"
        rel="stylesheet" />

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
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://jsdelivr.net"></script>
    @endif
</head>

<body class="bg-app-background font-sans text-app-text-primary antialiased">

    <!-- Contenedor Principal Centrado -->
    <div class="flex min-h-screen flex-col items-center justify-center px-6">

        <div class="w-full max-w-sm space-y-8 text-center">

            <!-- Imagen / Logo Centrado -->
            <div class="flex justify-center">
                <x-icon-sistema class="h-32 w-auto text-app-primary-600" />
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col space-y-3">

                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                        class="w-full rounded-xl bg-app-primary-600 px-4 py-3
                              font-medium text-white shadow-sm
                              transition duration-200 hover:bg-app-primary-700
                              focus-visible:outline-none focus-visible:ring-2
                              focus-visible:ring-app-border-focus
                              focus-visible:ring-offset-2 focus-visible:ring-offset-app-background">
                        Iniciar Sesión
                    </a>
                @else
                    <a href="/login"
                        class="w-full rounded-xl bg-app-primary-600 px-4 py-3
                              font-medium text-white shadow-sm
                              transition duration-200 hover:bg-app-primary-700
                              focus-visible:outline-none focus-visible:ring-2
                              focus-visible:ring-app-border-focus
                              focus-visible:ring-offset-2 focus-visible:ring-offset-app-background">
                        Iniciar Sesión
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="w-full rounded-xl border border-app-border
                              bg-app-surface px-4 py-3 font-medium
                              text-app-text-primary shadow-sm
                              transition duration-200 hover:bg-app-surface-hover
                              focus-visible:outline-none focus-visible:ring-2
                              focus-visible:ring-app-border-focus
                              focus-visible:ring-offset-2 focus-visible:ring-offset-app-background">
                        Registrarse
                    </a>
                @else
                    <a href="/register"
                        class="w-full rounded-xl border border-app-border
                              bg-app-surface px-4 py-3 font-medium
                              text-app-text-primary shadow-sm
                              transition duration-200 hover:bg-app-surface-hover
                              focus-visible:outline-none focus-visible:ring-2
                              focus-visible:ring-app-border-focus
                              focus-visible:ring-offset-2 focus-visible:ring-offset-app-background">
                        Registrarse
                    </a>
                @endif

            </div>

        </div>

    </div>

</body>

</html>
