<x-guest-layout>

    <div class="w-full max-w-sm mx-auto bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">

        <!-- Logo -->
        <div class="flex justify-center">
            <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema"
                class="h-24 w-auto object-contain drop-shadow-md">
        </div>

        <!-- Encabezado -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                Crea tu cuenta
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Registra el primer usuario del sistema
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Usuario -->
            <div>
                <x-input-label for="username" :value="__('Usuario')" class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="username"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />

                <x-input-error :messages="$errors->get('username')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Nombres -->
            <div>
                <x-input-label for="nombres" :value="__('Nombres')" class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="nombres"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="text" name="nombres" :value="old('nombres')" required autocomplete="given-name" />

                <x-input-error :messages="$errors->get('nombres')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Apellidos -->
            <div>
                <x-input-label for="apellidos" :value="__('Apellidos')" class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="apellidos"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="text" name="apellidos" :value="old('apellidos')" required autocomplete="family-name" />

                <x-input-error :messages="$errors->get('apellidos')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Teléfono -->
            <div>
                <x-input-label for="telefono" :value="__('Número de teléfono')" class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="telefono"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="text" name="telefono" :value="old('telefono')" autocomplete="tel" />

                <x-input-error :messages="$errors->get('telefono')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Correo electrónico -->
            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="email"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="email" name="email" :value="old('email')" required autocomplete="email" />

                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Contraseña -->
            <div>
                <x-input-label for="password" :value="__('Contraseña')" class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="password"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="password" name="password" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Confirmar contraseña -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')"
                    class="text-gray-700 font-medium text-sm mb-1 block" />

                <x-text-input id="password_confirmation"
                    class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm"
                    type="password" name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <!-- Registro -->
            <div class="flex flex-col space-y-3 pt-3">

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Registrar
                </button>

                <div class="text-center">
                    <a class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline transition duration-150"
                        href="{{ route('login') }}">
                        ¿Ya posees una cuenta?
                    </a>
                </div>

            </div>
        </form>
    </div>

</x-guest-layout>
