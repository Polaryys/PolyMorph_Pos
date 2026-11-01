<x-app-layout>

    <div class="space-y-4">

        @if (session('success'))
            <p class="text-sm font-medium text-green-600">
                {{ session('success') }}
            </p>
        @endif

    </div>

    <!-- formualrio de create -->
    <form id="create-user-form" method="POST" action="{{ route('usuarios.store') }}" enctype="multipart/form-data">

        @csrf

        <div class="flex items-center justify-left gap-3 ">

            <x-secondary-link href="{{ route('usuarios.index') }}" wire:navigate>
                Volver
            </x-secondary-link>

            <x-primary-button>
                Crear Usuario
            </x-primary-button>

        </div>

        <div class="space-y-6">

            <!-- imagen -->
            <div class="flex flex-col items-center justify-center space-y-3">

                <div class="relative w-28 h-28">

                    <img id="avatar-preview" src="{{ asset('images/default-avatar.png') }}" alt="Foto de perfil"
                        class="w-28 h-28 rounded-full object-cover border-2 border-indigo-100 shadow-sm">

                    <label for="foto"
                        class="absolute bottom-0 right-0 p-2 bg-indigo-600 text-white rounded-full
                               hover:bg-indigo-700 cursor-pointer shadow-md transition">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                    </label>

                    <input type="file" id="foto" name="foto" class="hidden" accept="image/*"
                        onchange="previewImage(event)">

                </div>

                <label for="foto"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 cursor-pointer">
                    {{ __('Agregar Foto') }}
                </label>

                <x-input-error :messages="$errors->get('foto')" />

            </div>

            <!-- datos user-->
            <x-section-title>
                Información del usuario
            </x-section-title>

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- NOMBRES -->
                <div class="sm:col-span-3">
                    <x-input-label for="nombres">
                        {{ __('Nombres') }}
                        <span class="text-red-500">*</span>
                    </x-input-label>

                    <x-text-input type="text" id="nombres" name="nombres" maxlength="100"
                        :value="old('nombres')" required autocomplete="given-name"
                        :error="$errors->has('nombres')" class="mt-1" />

                    <x-input-error :messages="$errors->get('nombres')" />
                </div>

                <!-- APELLIDOS -->
                <div class="sm:col-span-3">
                    <x-input-label for="apellidos">
                        {{ __('Apellidos') }}
                        <span class="text-red-500">*</span>
                    </x-input-label>

                    <x-text-input type="text" id="apellidos" name="apellidos" maxlength="100"
                        :value="old('apellidos')" required autocomplete="family-name"
                        :error="$errors->has('apellidos')" class="mt-1" />

                    <x-input-error :messages="$errors->get('apellidos')" />
                </div>

                <!-- USUARIO -->
                <div class="sm:col-span-3">
                    <x-input-label for="username">
                        {{ __('Usuario') }}
                        <span class="text-red-500">*</span>
                    </x-input-label>

                    <x-text-input type="text" id="username" name="username" maxlength="50"
                        :value="old('username')" required autocomplete="username"
                        :error="$errors->has('username')" class="mt-1" />

                    <x-input-error :messages="$errors->get('username')" />
                </div>

                <!-- ROL -->
                <div class="sm:col-span-3">
                    <x-input-label for="rol">
                        {{ __('Rol') }}
                        <span class="text-red-500">*</span>
                    </x-input-label>

                    <select id="rol" name="rol" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('rol') border-red-500 @enderror">

                        <option value="">Selecciona un rol</option>

                        <option value="Vendedor" {{ old('rol') === 'Vendedor' ? 'selected' : '' }}>
                            Vendedor
                        </option>

                        <option value="Administrador" {{ old('rol') === 'Administrador' ? 'selected' : '' }}>
                            Administrador
                        </option>

                    </select>

                    <x-input-error :messages="$errors->get('rol')" />
                </div>

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <x-input-label for="telefono">
                        {{ __('Teléfono') }}
                    </x-input-label>

                    <x-text-input type="text" id="telefono" name="telefono" maxlength="20"
                        :value="old('telefono')" autocomplete="tel"
                        :error="$errors->has('telefono')" class="mt-1" />

                    <x-input-error :messages="$errors->get('telefono')" />
                </div>

                <!-- email -->
                <div class="sm:col-span-3">
                    <x-input-label for="email">
                        {{ __('Correo electrónico') }}
                    </x-input-label>

                    <x-text-input type="email" id="email" name="email" maxlength="255"
                        :value="old('email')" autocomplete="email"
                        :error="$errors->has('email')" class="mt-1" />

                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <!-- password -->
                <div class="sm:col-span-3">
                    <x-input-label for="password">
                        {{ __('Contraseña') }}
                        <span class="text-red-500">*</span>
                    </x-input-label>

                    <x-text-input type="password" id="password" name="password" required autocomplete="new-password"
                        :error="$errors->has('password')" class="mt-1" />

                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <!-- cofirmacion de password -->
                <div class="sm:col-span-3">
                    <x-input-label for="password_confirmation">
                        {{ __('Confirmar contraseña') }}
                        <span class="text-red-500">*</span>
                    </x-input-label>

                    <x-text-input type="password" id="password_confirmation" name="password_confirmation" required
                        autocomplete="new-password" :error="$errors->has('password_confirmation')" class="mt-1" />

                    <x-input-error :messages="$errors->get('password_confirmation')" />
                </div>

            </div>

        </div>

    </form>

    <script>
        function previewImage(event) {
            const reader = new FileReader();

            reader.onload = function() {
                document.getElementById('avatar-preview').src = reader.result;
            };

            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>

</x-app-layout>
