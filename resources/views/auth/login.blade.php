<x-guest-layout>
    <!-- Estado de la Sesión -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="w-full max-w-sm mx-auto bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
        
        <!-- Encabezado del Formulario -->
        <div class="flex justify-center">
            <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema" class="h-24 w-auto object-contain drop-shadow-md">
        </div>
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Bienvenido de nuevo</h2>
            <p class="text-sm text-gray-500 mt-1">Ingresa tus credenciales para acceder</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Correo Electrónico -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium text-sm mb-1 block" />
                <x-text-input id="email" 
                    class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600" />
            </div>

            <!-- Contraseña -->
            <div>
  

                <x-text-input id="password" 
                    class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-600" />
            </div>

            <!-- Recordarme -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded-md border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-100 focus:ring-offset-0 w-4 h-4 transition duration-150" name="remember">
                    <span class="ms-2 text-sm text-gray-600 selection:bg-transparent">{{ __('Recuerdame') }}</span>
                </label>
            </div>

            <!-- Botón de Entrada -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('Iniciar Sesion') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
