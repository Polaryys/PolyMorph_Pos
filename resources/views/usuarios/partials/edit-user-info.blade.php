<section class="space-y-4">

    <x-section-title class="border-t border-gray-200 pt-2">
        Información del usuario
    </x-section-title>

    <!-- datos user -->
    <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

        <!-- NOMBRES -->
        <div>
            <x-input-label for="nombres">
                {{ __('Nombres') }}
                <span class="text-red-500">*</span>
            </x-input-label>

            <x-text-input type="text" id="nombres" name="nombres" maxlength="100"
                :value="old('nombres', $usuario->nombres)" required autocomplete="given-name"
                :error="$errors->has('nombres')" class="mt-1" />

            <x-input-error :messages="$errors->get('nombres')" />
        </div>

        <!-- APELLIDOS -->
        <div>
            <x-input-label for="apellidos">
                {{ __('Apellidos') }}
                <span class="text-red-500">*</span>
            </x-input-label>

            <x-text-input type="text" id="apellidos" name="apellidos" maxlength="100"
                :value="old('apellidos', $usuario->apellidos)" required autocomplete="family-name"
                :error="$errors->has('apellidos')" class="mt-1" />

            <x-input-error :messages="$errors->get('apellidos')" />
        </div>

        <!-- USUARIO -->
        <div>
            <x-input-label for="username">
                {{ __('Usuario') }}
                <span class="text-red-500">*</span>
            </x-input-label>

            <x-text-input type="text" id="username" name="username" maxlength="50"
                :value="old('username', $usuario->username)" required autocomplete="username"
                :error="$errors->has('username')" class="mt-1" />

            <x-input-error :messages="$errors->get('username')" />
        </div>

        <!-- ROL -->
        <div>
            <x-input-label for="rol">
                {{ __('Rol') }}
                <span class="text-red-500">*</span>
            </x-input-label>

            <select id="rol" name="rol" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                       @error('rol') border-red-500 @enderror">

                <option value="">Selecciona un rol</option>

                <option value="Vendedor" {{ old('rol', $usuario->rol) === 'Vendedor' ? 'selected' : '' }}
                    @disabled($esUltimoAdministradorActivo)>
                    Vendedor
                </option>

                <option value="Administrador"
                    {{ old('rol', $usuario->rol) === 'Administrador' ? 'selected' : '' }}>
                    Administrador
                </option>

            </select>

            <x-input-error :messages="$errors->get('rol')" />
            @if ($esUltimoAdministradorActivo)
                <p class="mt-1 text-xs text-amber-700">
                    No puedes cambiar el rol porque este es el único administrador activo.
                </p>
            @endif
        </div>

        <!-- TELÉFONO -->
        <div>
            <x-input-label for="telefono">
                {{ __('Teléfono') }}
            </x-input-label>

            <x-text-input type="text" id="telefono" name="telefono" maxlength="20"
                :value="old('telefono', $usuario->telefono)" autocomplete="tel"
                :error="$errors->has('telefono')" class="mt-1" />

            <x-input-error :messages="$errors->get('telefono')" />
        </div>

        <!-- email -->
        <div>
            <x-input-label for="email">
                {{ __('Correo electrónico') }}
            </x-input-label>

            <x-text-input type="email" id="email" name="email" maxlength="255"
                :value="old('email', $usuario->email)" autocomplete="email"
                :error="$errors->has('email')" class="mt-1" />

            <x-input-error :messages="$errors->get('email')" />
        </div>

    </div>

</section>
