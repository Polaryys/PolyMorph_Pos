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

        <div class="space-y-6">

            <!-- imagen -->
            <div class="flex flex-col items-center justify-center space-y-3 mt-2">

                <div class="relative h-32 w-32">

                    <!-- Avatar -->
                    <div id="avatar-preview"
                        class="flex h-32 w-32 items-center justify-center rounded-full border-2 border-app-border-strong bg-app-background text-app-text-muted">

                        <x-lucide-user-round class="h-16 w-16" />

                    </div>

                    <!-- Quitar imagen -->
                    <button type="button" id="btn-quitar-imagen" title="Quitar imagen"
                        class="absolute left-0 top-0 hidden rounded-full bg-red-500 p-1.5 text-white shadow-md transition hover:bg-red-600">

                        <x-lucide-x class="h-4 w-4" />

                    </button>

                    <!-- Abrir galería -->
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                        title="Seleccionar imagen de la galería" aria-label="Seleccionar imagen de la galería"
                        class="absolute bottom-0 right-0 rounded-full bg-app-primary-600 p-2 text-white shadow-md transition hover:bg-indigo-700">

                        <x-lucide-camera class="h-4 w-4" />

                    </button>

                </div>

                <input type="hidden" id="imagen_id" name="imagen_id" value="">

                <x-input-error :messages="$errors->get('imagen_id')" />

            </div>

            <x-input-error :messages="$errors->get('foto')" />

        </div>

        <!-- datos user-->
        <x-section-title>
            Información del usuario
        </x-section-title>

        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">

            <!-- NOMBRES -->
            <div class="sm:col-span-3">
                <x-input-label for="nombres">
                    {{ __('Nombres') }}
                    <span class="text-red-500">*</span>
                </x-input-label>

                <x-text-input type="text" id="nombres" name="nombres" maxlength="100" :value="old('nombres')" required
                    autocomplete="given-name" :error="$errors->has('nombres')" class="mt-1" />

                <x-input-error :messages="$errors->get('nombres')" />
            </div>

            <!-- APELLIDOS -->
            <div class="sm:col-span-3">
                <x-input-label for="apellidos">
                    {{ __('Apellidos') }}
                    <span class="text-red-500">*</span>
                </x-input-label>

                <x-text-input type="text" id="apellidos" name="apellidos" maxlength="100" :value="old('apellidos')" required
                    autocomplete="family-name" :error="$errors->has('apellidos')" class="mt-1" />

                <x-input-error :messages="$errors->get('apellidos')" />
            </div>

            <!-- USUARIO -->
            <div class="sm:col-span-3">
                <x-input-label for="username">
                    {{ __('Usuario') }}
                    <span class="text-red-500">*</span>
                </x-input-label>

                <x-text-input type="text" id="username" name="username" maxlength="50" :value="old('username')" required
                    autocomplete="username" :error="$errors->has('username')" class="mt-1" />

                <x-input-error :messages="$errors->get('username')" />
            </div>

            <!-- ROL -->
            <div class="sm:col-span-3">
                <x-input-label for="rol">
                    {{ __('Rol') }}
                    <span class="text-red-500">*</span>
                </x-input-label>

                <x-select-input id="rol" name="rol" required class="mt-1 w-full" :error="$errors->has('rol')">
                    <option value="">Selecciona un rol</option>

                    <option value="Vendedor" @selected(old('rol') === 'Vendedor')>
                        Vendedor
                    </option>

                    <option value="Administrador" @selected(old('rol') === 'Administrador')>
                        Administrador
                    </option>
                </x-select-input>

                <x-input-error :messages="$errors->get('rol')" />
            </div>

            <!-- TELÉFONO -->
            <div class="sm:col-span-3">
                <x-input-label for="telefono">
                    {{ __('Teléfono') }}
                </x-input-label>

                <x-text-input type="text" id="telefono" name="telefono" maxlength="20" :value="old('telefono')"
                    autocomplete="tel" :error="$errors->has('telefono')" class="mt-1" />

                <x-input-error :messages="$errors->get('telefono')" />
            </div>

            <!-- email -->
            <div class="sm:col-span-3">
                <x-input-label for="email">
                    {{ __('Correo electrónico') }}
                </x-input-label>

                <x-text-input type="email" id="email" name="email" maxlength="255" :value="old('email')"
                    autocomplete="email" :error="$errors->has('email')" class="mt-1" />

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

        <div class="flex items-center justify-end gap-3 pt-4">

            <x-secondary-link href="{{ route('usuarios.index') }}" wire:navigate>
                Volver
            </x-secondary-link>

            <x-primary-button>
                Crear Usuario
            </x-primary-button>

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
    <script>
        // Seleccionar imagen de la galería
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            if (!imagen || imagen.id == null || !imagen.url) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            document.getElementById('imagen_id').value = imagen.id;

            const avatar = document.getElementById('avatar-preview');

            const nuevaImagen = document.createElement('img');

            nuevaImagen.id = 'avatar-preview';
            nuevaImagen.src = imagen.url;
            nuevaImagen.alt = 'Foto de perfil';
            nuevaImagen.className =
                'h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm';

            avatar.replaceWith(nuevaImagen);

            document.getElementById('btn-quitar-imagen').classList.remove('hidden');
        });

        // Quitar imagen
        document.getElementById('btn-quitar-imagen').addEventListener('click', function() {
            const avatar = document.getElementById('avatar-preview');

            const icono = document.createElement('div');

            icono.id = 'avatar-preview';
            icono.className =
                'flex h-32 w-32 items-center justify-center rounded-full border-2 border-app-border-strong bg-app-background text-app-text-muted';

            icono.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg"
                width="64" height="64" viewBox="0 0 24 24"
                fill="none" stroke="currentColor"
                stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="8" r="5"/>
                <path d="M20 21a8 8 0 0 0-16 0"/>
            </svg>
        `;

            avatar.replaceWith(icono);

            document.getElementById('imagen_id').value = '';

            this.classList.add('hidden');
        });
    </script>

</x-app-layout>
