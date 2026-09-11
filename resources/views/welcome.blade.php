<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('icon_sistema.png') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://bunny.net">
    <link href="https://bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://jsdelivr.net"></script>
    @endif
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <!-- Contenedor Principal Centrado -->
    <div class="min-h-screen flex flex-col items-center justify-center px-6">
        
        <div class="w-full max-w-sm text-center space-y-8">
            
            <!-- Imagen / Logo Centrado -->
            <div class="flex justify-center">
                <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema" class="h-24 w-auto object-contain drop-shadow-md">
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col space-y-3">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm">
                        Iniciar Sesión
                    </a>
                @else
                    <a href="/login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm">
                        Iniciar Sesión
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-xl border border-gray-200 transition duration-200 shadow-sm">
                        Registrarse
                    </a>
                @else
                    <a href="/register" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-xl border border-gray-200 transition duration-200 shadow-sm">
                        Registrarse
                    </a>
                @endif
            </div>

        </div>

    </div>

</body>
</html>
