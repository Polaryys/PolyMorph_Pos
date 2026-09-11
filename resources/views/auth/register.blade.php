<x-guest-layout>
    
    <div class="w-full max-w-sm mx-auto bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
        
           <div class="flex justify-center">
            <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema" class="h-24 w-auto object-contain drop-shadow-md">
        </div>
        <!-- Encabezado del Formulario -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Crea tu cuenta</h2>
            <p class="text-sm text-gray-500 mt-1">Regístrate para empezar a usar el sistema</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <x-input-label for="name" :value="__('Nombre')" class="text-gray-700 font-medium text-sm mb-1 block" />
                <x-text-input id="name" 
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Correo Electrónico -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium text-sm mb-1 block" />
                <x-text-input id="email" 
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Contraseña -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium text-sm mb-1 block" />
                <x-text-input id="password" 
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Password')" class="text-gray-700 font-medium text-sm mb-1 block" />
                <x-text-input id="password_confirmation" 
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Enlace y Botón de Registro -->
            <div class="flex flex-col space-y-3 pt-3">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('Registrar') }}
                </button>

                <div class="text-center">
                    <a class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline transition duration-150" href="{{ route('login') }}">
                        {{ __('Ya posees una cuenta?') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
