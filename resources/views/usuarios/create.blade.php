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

            <a href="{{ route('usuarios.index') }}" wire:navigate
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium
                           text-gray-700 hover:bg-gray-50 transition">
                Volver
            </a>

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                           text-sm font-medium rounded-md shadow-sm transition">
                Crear Usuario
            </button>

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

                @error('foto')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <!-- datos user-->
            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- NOMBRES -->
                <div class="sm:col-span-3">
                    <label for="nombres" class="block text-sm font-medium text-gray-700">
                        {{ __('Nombres') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="nombres" name="nombres" maxlength="100" value="{{ old('nombres') }}"
                        required autocomplete="given-name"
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
                        value="{{ old('apellidos') }}" required autocomplete="family-name"
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

                    <input type="text" id="username" name="username" maxlength="50" value="{{ old('username') }}"
                        required autocomplete="username"
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

                        <option value="Vendedor" {{ old('rol') === 'Vendedor' ? 'selected' : '' }}>
                            Vendedor
                        </option>

                        <option value="Administrador" {{ old('rol') === 'Administrador' ? 'selected' : '' }}>
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

                    <input type="text" id="telefono" name="telefono" maxlength="20" value="{{ old('telefono') }}"
                        autocomplete="tel"
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
                        value="{{ old('email') }}" autocomplete="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
               @error('email') border-red-500 @enderror">

                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- password -->
                <div class="sm:col-span-3">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        {{ __('Contraseña') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('password') border-red-500 @enderror">

                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- cofirmacion de password -->
                <div class="sm:col-span-3">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        {{ __('Confirmar contraseña') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                               @error('password_confirmation') border-red-500 @enderror">

                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
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
