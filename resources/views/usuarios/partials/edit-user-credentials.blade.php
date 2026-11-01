<section class="space-y-4" x-data="{ mostrarCredenciales: false }">

    <x-section-title class="border-t border-gray-200 pt-2">

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

    </x-section-title>

    <div x-show="mostrarCredenciales" x-cloak class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

        <!-- CONTRASEÑA -->
        <div>
            <x-input-label for="password">
                {{ __('Nueva contraseña') }}
            </x-input-label>

            <x-text-input type="password" id="password" name="password" autocomplete="new-password"
                :error="$errors->has('password')" class="mt-1" />

            <p class="mt-1 text-xs text-gray-400">
                Deja este campo vacío para conservar la contraseña actual.
            </p>

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- CONFIRMAR CONTRASEÑA -->
        <div>
            <x-input-label for="password_confirmation">
                {{ __('Confirmar contraseña') }}
            </x-input-label>

            <x-text-input type="password" id="password_confirmation" name="password_confirmation"
                autocomplete="new-password" :error="$errors->has('password_confirmation')" class="mt-1" />

            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- ESTADO -->
        <div>
            <x-input-label for="estado" class="flex items-center gap-2">
                {{ __('Estado') }}
                <span class="text-red-500">*</span>

                <span title="Indica si el usuario puede acceder al sistema."
                    class="inline-flex h-4 w-4 items-center justify-center text-xs text-gray-800 cursor-help">
                    ?
                </span>
            </x-input-label>

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

            <x-input-error :messages="$errors->get('estado')" />
        </div>

    </div>

</section>
