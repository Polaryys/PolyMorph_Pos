<x-guest-layout max-width="sm:max-w-2xl">

    <div
        class="mx-auto w-full max-w-2xl rounded-2xl border border-app-border
                bg-app-surface px-10 py-6 shadow-xl shadow-black/5">

        <!-- Logo -->
        <div class="mb-4 flex justify-center">
            <x-icon-sistema class="h-24 w-auto text-app-primary-600" />
        </div>

        <!-- Encabezado -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold tracking-wider text-app-text-primary">
                Crea tu cuenta
            </h2>

            <p class="mt-1 text-base text-app-text-secondary">
                Registra el primer usuario del sistema
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Usuario y teléfono -->
            <div class="grid grid-cols-2 gap-4">

                <!-- Usuario -->
                <div>
                    <x-input-label for="username" :value="__('Usuario')"
                        class="mb-2 block text-sm font-medium text-app-text-primary" />

                    <x-text-input id="username"
                        class="block w-full rounded-md border border-app-border bg-app-background
                               px-4 py-2 text-base text-app-text-primary shadow-sm
                               transition duration-200
                               placeholder:text-app-text-muted
                               focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                        placeholder="Ingresa tu usuario" type="text" name="username" :value="old('username')" required
                        autofocus autocomplete="username" />

                    <x-input-error :messages="$errors->get('username')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

                <!-- Teléfono -->
                <div>
                    <x-input-label for="telefono" :value="__('Número de teléfono')"
                        class="mb-2 block text-sm font-medium text-app-text-primary" />

                    <x-text-input id="telefono"
                        class="block w-full rounded-md border border-app-border bg-app-background
                               px-4 py-2 text-base text-app-text-primary shadow-sm
                               transition duration-200
                               placeholder:text-app-text-muted
                               focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                        placeholder="Ingresa tu teléfono" type="text" name="telefono" :value="old('telefono')"
                        autocomplete="tel" />

                    <x-input-error :messages="$errors->get('telefono')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

            </div>

            <!-- Correo electrónico -->
            <div>
                <x-input-label for="email" :value="__('Correo electrónico')"
                    class="mb-2 block text-sm font-medium text-app-text-primary" />

                <x-text-input id="email"
                    class="block w-full rounded-md border border-app-border bg-app-background
                           px-4 py-2 text-base text-app-text-primary shadow-sm
                           transition duration-200
                           placeholder:text-app-text-muted
                           focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                    placeholder="Ingresa tu correo" type="email" name="email" :value="old('email')" required
                    autocomplete="email" />

                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Nombres y apellidos -->
            <div class="grid grid-cols-2 gap-4">

                <!-- Nombres -->
                <div>
                    <x-input-label for="nombres" :value="__('Nombres')"
                        class="mb-2 block text-sm font-medium text-app-text-primary" />

                    <x-text-input id="nombres"
                        class="block w-full rounded-md border border-app-border bg-app-background
                               px-4 py-2 text-base text-app-text-primary shadow-sm
                               transition duration-200
                               placeholder:text-app-text-muted
                               focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                        placeholder="Ingresa los nombres" type="text" name="nombres" :value="old('nombres')" required
                        autocomplete="given-name" />

                    <x-input-error :messages="$errors->get('nombres')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

                <!-- Apellidos -->
                <div>
                    <x-input-label for="apellidos" :value="__('Apellidos')"
                        class="mb-2 block text-sm font-medium text-app-text-primary" />

                    <x-text-input id="apellidos"
                        class="block w-full rounded-md border border-app-border bg-app-background
                               px-4 py-2 text-base text-app-text-primary shadow-sm
                               transition duration-200
                               placeholder:text-app-text-muted
                               focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                        placeholder="Ingresa los apellidos" type="text" name="apellidos" :value="old('apellidos')" required
                        autocomplete="family-name" />

                    <x-input-error :messages="$errors->get('apellidos')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

            </div>

            <!-- Contraseñas -->
            <div class="grid grid-cols-2 gap-4">

                <!-- Contraseña -->
                <div x-data="{ showPassword: false }">
                    <x-input-label for="password" :value="__('Contraseña')"
                        class="mb-2 block text-sm font-medium text-app-text-primary" />

                    <div class="relative">
                        <x-text-input id="password"
                            class="block w-full rounded-md border border-app-border bg-app-background
                                   px-4 py-2 pr-10 text-base text-app-text-primary shadow-sm
                                   transition duration-200
                                   placeholder:text-app-text-muted
                                   focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                            ::type="showPassword ? 'text' : 'password'" placeholder="Ingresa la contraseña" name="password" required
                            autocomplete="new-password" />

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center rounded-sm pr-3
                                   text-app-text-muted transition-colors hover:text-app-text-primary
                                   focus:outline-none focus-visible:ring-2
                                   focus-visible:ring-app-border-focus
                                   focus-visible:ring-offset-2 focus-visible:ring-offset-app-surface"
                            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'">

                            <!-- Ojo abierto -->
                            <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <!-- Ojo cerrado -->
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

                <!-- Confirmar contraseña -->
                <div x-data="{ showPassword: false }">
                    <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')"
                        class="mb-2 block text-sm font-medium text-app-text-primary" />

                    <div class="relative">
                        <x-text-input id="password_confirmation"
                            class="block w-full rounded-md border border-app-border bg-app-background
                                   px-4 py-2 pr-10 text-base text-app-text-primary shadow-sm
                                   transition duration-200
                                   placeholder:text-app-text-muted
                                   focus:border-app-primary-500 focus:ring focus:ring-app-primary-500/20"
                            ::type="showPassword ? 'text' : 'password'" placeholder="Confirma la contraseña" name="password_confirmation"
                            required autocomplete="new-password" />

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center rounded-sm pr-3
                                   text-app-text-muted transition-colors hover:text-app-text-primary
                                   focus:outline-none focus-visible:ring-2
                                   focus-visible:ring-app-border-focus
                                   focus-visible:ring-offset-2 focus-visible:ring-offset-app-surface"
                            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'">

                            <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

            </div>

            <!-- Botón de registro -->
            <div class="space-y-4 pt-1">
                <button type="submit"
                    class="w-full rounded-lg bg-app-primary-600 px-4 py-3 text-base
                           font-medium text-white shadow-sm transition duration-200
                           hover:bg-app-primary-700
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-app-border-focus
                           focus-visible:ring-offset-2 focus-visible:ring-offset-app-surface">
                    Registrar
                </button>

                <div class="text-center">
                    <a class="text-sm font-medium text-app-primary-600 transition duration-150
                              hover:text-app-primary-700 hover:underline"
                        href="{{ route('login') }}">
                        ¿Ya posees una cuenta?
                    </a>
                </div>
            </div>

        </form>
    </div>

</x-guest-layout>
