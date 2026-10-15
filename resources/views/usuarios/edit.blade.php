<x-app-layout>

    <div class="space-y-4">

        @if (session('success'))
            <p class="text-sm font-medium text-green-600">
                {{ session('success') }}
            </p>
        @endif

    </div>

    <!-- form editar -->
    <form id="edit-user-form" method="POST" action="{{ route('usuarios.update', $usuario) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="flex items-center justify-left gap-3">

            <a href="{{ route('usuarios.index') }}" wire:navigate
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition">
                Volver
            </a>

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium rounded-md shadow-sm transition">
                Guardar Cambios
            </button>

        </div>

        <div class="space-y-6">

            <!-- imagen -->

            <div class="flex flex-col items-center justify-center space-y-3">

                <div class="relative h-28 w-28">

                    <img id="avatar-preview"
                        src="{{ $usuario->imagen_id && $usuario->imagen
                            ? asset('storage/imagenes/' . $usuario->imagen->nombre_archivo)
                            : ($usuario->foto
                                ? asset('storage/' . $usuario->foto)
                                : asset('images/default-avatar.png')) }}"
                        alt="Foto de perfil"
                        class="h-28 w-28 rounded-full border-2 border-indigo-100 object-cover shadow-sm">

                    <!-- Botón  abrir la galería -->
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                        title="Seleccionar imagen de la galería" aria-label="Seleccionar imagen de la galería"
                        class="absolute bottom-0 right-0 rounded-full bg-indigo-600 p-2 text-white shadow-md transition hover:bg-indigo-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>

                </div>

                <input type="hidden" id="imagen_id" name="imagen_id"
                    value="{{ old('imagen_id', $usuario->imagen_id) }}">

                @error('imagen_id')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <!-- datos user -->
            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">

                <!-- NOMBRES -->
                <div class="sm:col-span-3">
                    <label for="nombres" class="block text-sm font-medium text-gray-700">
                        {{ __('Nombres') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="nombres" name="nombres" maxlength="100"
                        value="{{ old('nombres', $usuario->nombres) }}" required autocomplete="given-name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('nombres') border-red-500 @enderror">

                    @error('nombres')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- APELLIDOS -->
                <div class="sm:col-span-3">
                    <label for="apellidos" class="block text-sm font-medium text-gray-700">
                        {{ __('Apellidos') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="apellidos" name="apellidos" maxlength="100"
                        value="{{ old('apellidos', $usuario->apellidos) }}" required autocomplete="family-name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('apellidos') border-red-500 @enderror">

                    @error('apellidos')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- USUARIO -->
                <div class="sm:col-span-3">
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        {{ __('Usuario') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="username" name="username" maxlength="50"
                        value="{{ old('username', $usuario->username) }}" required autocomplete="username"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('username') border-red-500 @enderror">

                    @error('username')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ROL -->
                <div class="sm:col-span-3">
                    <label for="rol" class="block text-sm font-medium text-gray-700">
                        {{ __('Rol') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <select id="rol" name="rol" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('rol') border-red-500 @enderror">

                        <option value="">Selecciona un rol</option>

                        <option value="Vendedor" {{ old('rol', $usuario->rol) === 'Vendedor' ? 'selected' : '' }}>
                            Vendedor
                        </option>

                        <option value="Administrador"
                            {{ old('rol', $usuario->rol) === 'Administrador' ? 'selected' : '' }}>
                            Administrador
                        </option>

                    </select>

                    @error('rol')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <label for="telefono" class="block text-sm font-medium text-gray-700">
                        {{ __('Teléfono') }}
                    </label>

                    <input type="text" id="telefono" name="telefono" maxlength="20"
                        value="{{ old('telefono', $usuario->telefono) }}" autocomplete="tel"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('telefono') border-red-500 @enderror">

                    @error('telefono')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- email -->
                <div class="sm:col-span-3">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        {{ __('Correo electrónico') }}
                    </label>

                    <input type="email" id="email" name="email" maxlength="255"
                        value="{{ old('email', $usuario->email) }}" autocomplete="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('email') border-red-500 @enderror">

                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CONTRASEÑA -->
                <div class="sm:col-span-3">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        {{ __('Nueva contraseña') }}
                    </label>

                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('password') border-red-500 @enderror">

                    <p class="mt-1 text-xs text-gray-400">
                        Deja este campo vacío para conservar la contraseña actual.
                    </p>

                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- confirmar la pasword -->
                <div class="sm:col-span-3">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        {{ __('Confirmar contraseña') }}
                    </label>

                    <input type="password" id="password_confirmation" name="password_confirmation"
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('password_confirmation') border-red-500 @enderror">

                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ESTADO -->
                <div class="sm:col-span-3">
                    <label for="estado" class="block text-sm font-medium text-gray-700">
                        {{ __('Estado') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <select id="estado" name="estado" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('estado') border-red-500 @enderror">

                        <option value="1" {{ old('estado', $usuario->estado) == 1 ? 'selected' : '' }}>
                            Activo
                        </option>

                        <option value="0" {{ old('estado', $usuario->estado) == 0 ? 'selected' : '' }}>
                            Inactivo
                        </option>

                    </select>

                    @error('estado')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

        </div>

    </form>



    <script>
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            console.log('Imagen recibida:', imagen);

            if (!imagen || imagen.id == null || !imagen.url) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            document.getElementById('imagen_id').value = imagen.id;

            document.getElementById('avatar-preview').src = imagen.url;

        });
    </script>



    @include('imagenes.partials.selector')

</x-app-layout>
