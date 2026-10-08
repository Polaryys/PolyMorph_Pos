<section class="space-y-4">
    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                        {{ __('Seguridad y Contraseña') }}
                    </h3>
                    <p class="text-xs text-gray-500">
                        {{ __('Asegúrate de cumplir con los requisitos mínimos de seguridad.') }}
                    </p>
                </div>

                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent
                       bg-indigo-600 py-2 px-4 text-sm font-medium text-white
                       shadow-sm hover:bg-indigo-700 transition">
                    {{ __('Actualizar Contraseña') }}
                </button>
            </div>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-medium text-green-600">
                    {{ __('Contraseña actualizada correctamente.') }}
                </p>
            @endif

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6 pt-2">

                <!-- CONTRASEÑA ACTUAL -->
                <div class="sm:col-span-2">
                    <label for="update_password_current_password" class="block text-sm font-medium text-gray-700">
                        {{ __('Contraseña actual') }} <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="update_password_current_password" name="current_password"
                        autocomplete="current-password" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('current_password', 'updatePassword') border-red-500 @enderror">

                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-xs text-red-500" />
                </div>

                <!-- NUEVA CONTRASEÑA -->
                <div class="sm:col-span-2">
                    <label for="update_password_password" class="block text-sm font-medium text-gray-700">
                        {{ __('Nueva contraseña') }} <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="update_password_password" name="password" autocomplete="new-password"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('password', 'updatePassword') border-red-500 @enderror">

                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-xs text-red-500" />
                </div>

                <!-- CONFIRMAR CONTRASEÑA -->
                <div class="sm:col-span-2">
                    <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700">
                        {{ __('Confirmar contraseña') }} <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                        autocomplete="new-password" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('password_confirmation', 'updatePassword') border-red-500 @enderror">

                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-xs text-red-500" />
                </div>

            </div>
        </div>
    </form>
</section>
