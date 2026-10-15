<x-app-layout>
    <div class="space-y-6">

        <!-- foto perfil -->
        <div class="space-y-4">

            <!-- imagen -->
            <div class="flex flex-col items-center justify-center space-y-3 pt-2">
                <div class="relative w-28 h-28">

                    <img src="{{ $user->imagen_id && $user->imagen
                        ? asset('storage/imagenes/' . $user->imagen->nombre_archivo)
                        : ($user->foto
                            ? asset('storage/' . $user->foto)
                            : asset('images/default-avatar.png')) }}"
                        alt="Foto de perfil"
                        class="w-28 h-28 rounded-full object-cover border-2 border-indigo-100 shadow-sm" />

                </div>
            </div>
        </div>

        <!-- datos del usaurio -->
        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6 pt-4">

            <!-- NOMBRES -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Nombres') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->nombres }}
                </div>
            </div>

            <!-- APELLIDOS -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Apellidos') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->apellidos }}
                </div>
            </div>

            <!-- USUARIO -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Usuario') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->username }}
                </div>
            </div>

            <!-- ROL -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Rol') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->rol }}
                </div>
            </div>

            <!-- TELÉFONO -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Teléfono') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->telefono ?: 'No registrado' }}
                </div>
            </div>

            <!-- CORREO ELECTRÓNICO -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Correo electrónico') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->email }}
                </div>
            </div>

            <!-- ESTADO -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Estado') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                            shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $user->estado ? 'Activo' : 'Inactivo' }}
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
