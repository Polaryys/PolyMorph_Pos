<x-app-layout>

    <div class="flex items-center justify-left gap-3">

        <a href="{{ route('usuarios.index') }}" wire:navigate
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition">
            Volver
        </a>

        <a href="{{ route('usuarios.edit', $usuario) }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium rounded-md shadow-sm transition">
            Editar Usuario
        </a>

    </div>
    <div class="space-y-6">

        <div class="space-y-4">

            <!-- imagen -->

            <!-- Imagen del usuario -->
            <div class="flex flex-col items-center justify-center space-y-3">
                <div class="relative h-28 w-28">

                    <img src="{{ $usuario->imagen_id && $usuario->imagen
                        ? asset('storage/imagenes/' . $usuario->imagen->nombre_archivo)
                        : ($usuario->foto
                            ? asset('storage/' . $usuario->foto)
                            : asset('images/default-avatar.png')) }}"
                        alt="Foto de perfil"
                        class="h-28 w-28 rounded-full object-cover border-2 border-indigo-100 shadow-sm">

                </div>
            </div>


        </div>

        <!-- datos user -->
        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6 pt-4">

            <!-- NOMBRES -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Nombres') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                           shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $usuario->nombres }}
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
                    {{ $usuario->apellidos }}
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
                    {{ $usuario->username }}
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
                    {{ $usuario->rol }}
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
                    {{ $usuario->telefono ?: 'No registrado' }}
                </div>
            </div>

            <!-- email -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Correo electrónico') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                           shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $usuario->email ?: 'No registrado' }}
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
                    {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                </div>
            </div>

            <!-- creado en  -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ __('Fecha de creación') }}
                </label>

                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50
                           shadow-sm sm:text-sm p-2 border text-gray-700">
                    {{ $usuario->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

        </div>



    </div>

</x-app-layout>
