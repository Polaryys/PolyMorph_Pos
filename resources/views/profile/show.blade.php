<x-app-layout>
    <div class="space-y-6">

        <!-- FOTO DE PERFIL -->
        <section class="space-y-4">


            <div class="flex flex-col items-center justify-center space-y-3 pt-2">
                <div class="relative h-28 w-28">

                    <img src="{{ $user->imagen_id && $user->imagen
                        ? asset('storage/imagenes/' . $user->imagen->nombre_archivo)
                        : ($user->foto
                            ? asset('storage/' . $user->foto)
                            : asset('images/default-avatar.png')) }}"
                        alt="Foto de perfil"
                        class="h-28 w-28 rounded-full border-2 border-indigo-100 object-cover shadow-sm" />

                </div>
            </div>

        </section>

        <!-- INFORMACIÓN DEL USUARIO -->
        <section class="space-y-4">

            <h2 class="border-t border-gray-200 pt-2 text-base font-semibold text-indigo-600">
                Información del usuario
            </h2>

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

                <!-- NOMBRES -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('Nombres') }}
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $user->nombres }}
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('Apellidos') }}
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $user->apellidos }}
                    </div>
                </div>

                <!-- USUARIO -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('Usuario') }}
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $user->username }}
                    </div>
                </div>

                <!-- ROL -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('Rol') }}
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $user->rol }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('Teléfono') }}
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $user->telefono ?: 'No registrado' }}
                    </div>
                </div>

                <!-- CORREO ELECTRÓNICO -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('Correo electrónico') }}
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $user->email ?: 'No registrado' }}
                    </div>
                </div>

            </div>

        </section>

    </div>
</x-app-layout>
