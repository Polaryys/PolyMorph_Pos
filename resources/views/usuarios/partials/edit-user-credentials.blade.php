<section class="space-y-4" x-data="{ mostrarCredenciales: false }">

    <h2 class="flex items-center gap-2 border-t border-gray-200 pt-2 text-base font-semibold text-indigo-600">

        Credenciales y acceso del usuario

        <button type="button" @click="mostrarCredenciales = !mostrarCredenciales"
            class="text-gray-500 hover:text-indigo-600 transition-colors"
            :title="mostrarCredenciales ? 'Ocultar opciones' : 'Mostrar opciones'" :aria-expanded="mostrarCredenciales">
            <svg class="h-4 w-4 transition-transform duration-200" :class="mostrarCredenciales ? 'rotate-180' : ''"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
            </svg>
        </button>

    </h2>

    <div x-show="mostrarCredenciales" x-cloak class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

        <!-- CONTRASEÑA -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
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

        <!-- CONFIRMAR CONTRASEÑA -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Confirmar contraseña') }}
            </label>

            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                       @error('password_confirmation') border-red-500 @enderror">

            @error('password_confirmation')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- ESTADO -->
        <div>
            <label for="estado" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                {{ __('Estado') }}
                <span class="text-red-500">*</span>

                <span title="Indica si el usuario puede acceder al sistema."
                    class="inline-flex h-4 w-4 items-center justify-center text-xs text-gray-800 cursor-help">
                    ?
                </span>
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

</section>
