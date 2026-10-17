<x-guest-layout>
    <!-- Estado de sesión -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="w-full max-w-md mx-auto px-10 py-10 bg-white shadow-xl shadow-gray-200/50 rounded-2xl border border-gray-200">

        <!-- Encabezado -->
        <div class="flex justify-center mb-4">
            <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema" class="h-24 w-auto object-contain">
        </div>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold tracking-wider text-gray-900">
                EBEN-EZER
            </h2>

            <p class="text-base text-gray-500 mt-1">
                Ingresa tus credenciales para iniciar sesión
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Usuario -->
            <div>
                <x-input-label for="username" :value="__('Usuario')" class="text-gray-900 font-medium text-sm mb-2 block" />

                <x-text-input id="username"
                    class="block w-full px-4 py-2.5 rounded-md border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm text-base"
                    placeholder="Ingresa tu usuario" type="text" name="username" :value="old('username')" required autofocus
                    autocomplete="username" />

                <x-input-error :messages="$errors->get('username')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Contraseña -->
            <div x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('Contraseña')" class="text-gray-900 font-medium text-sm mb-2 block" />

                <div class="relative">
                    <x-text-input id="password"
                        class="block w-full px-4 py-2.5 rounded-md border border-gray-200 text-gray-900 focus:border-blue-500 focus:ring focus:ring-blue-100 transition duration-200 shadow-sm text-base pr-10"
                        ::type="showPassword ? 'text' : 'password'" name="password" placeholder="Ingresa la contraseña" required
                        autocomplete="current-password" />

                    <!-- visiblidad de contraseña -->
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">

                        <!-- icono ojo abierto -->
                        <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <!-- Icono de Ojo Cerrado -->
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- botón de ingresar -->
            <div class="pt-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-base">
                    Ingresar
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
