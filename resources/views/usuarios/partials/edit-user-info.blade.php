<section class="space-y-4">

    <h2 class="border-t border-gray-200 pt-2 text-base font-semibold text-indigo-600">
        Información del usuario
    </h2>

    <!-- datos user -->
    <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

        <!-- NOMBRES -->
        <div>
            <label for="nombres" class="block text-sm font-medium text-gray-700 mb-2">
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
        <div>
            <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-2">
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
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
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
        <div>
            <label for="rol" class="block text-sm font-medium text-gray-700 mb-2">
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
        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
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
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
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

    </div>

</section>
