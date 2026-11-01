<x-app-layout>

    <div class="space-y-6">

        <!-- FOTO DEL USUARIO -->
        <section class="space-y-4">

            <div class="flex flex-col items-center justify-center space-y-3 pt-2">

                <div class="relative h-28 w-28">

                    <img src="{{ $usuario->imagen_id && $usuario->imagen
                        ? asset('storage/imagenes/' . $usuario->imagen->nombre_archivo)
                        : ($usuario->foto
                            ? asset('storage/' . $usuario->foto)
                            : asset('images/default-avatar.png')) }}"
                        alt="Foto de perfil"
                        class="h-28 w-28 rounded-full border-2 border-indigo-100 object-cover shadow-sm">

                </div>

            </div>

        </section>

        <!-- INFORMACIÓN DEL USUARIO -->
        <section class="space-y-4">

            <x-section-title class="border-t border-gray-200 pt-2">
                Información del usuario
            </x-section-title>

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

                <!-- NOMBRES -->
                <div>
                    <x-input-label>
                        {{ __('Nombres') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->nombres }}
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div>
                    <x-input-label>
                        {{ __('Apellidos') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->apellidos }}
                    </div>
                </div>

                <!-- USUARIO -->
                <div>
                    <x-input-label>
                        {{ __('Usuario') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->username }}
                    </div>
                </div>

                <!-- ROL -->
                <div>
                    <x-input-label>
                        {{ __('Rol') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->rol }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div>
                    <x-input-label>
                        {{ __('Teléfono') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->telefono ?: 'No registrado' }}
                    </div>
                </div>

                <!-- CORREO ELECTRÓNICO -->
                <div>
                    <x-input-label>
                        {{ __('Correo electrónico') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->email ?: 'No registrado' }}
                    </div>
                </div>

                <!-- ESTADO -->
                <div>
                    <x-input-label>
                        {{ __('Estado') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                    </div>
                </div>

                <!-- FECHA DE CREACIÓN -->
                <div>
                    <x-input-label>
                        {{ __('Fecha de creación') }}
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50
                                p-2 text-sm text-gray-700 shadow-sm">
                        {{ $usuario->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

            </div>

        </section>
                <!-- BOTONES DE ACCIÓN -->
        <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-5 pb-6">

            <x-secondary-link href="{{ route('usuarios.index') }}" wire:navigate>
                Volver
            </x-secondary-link>

            <a href="{{ route('usuarios.edit', $usuario) }}" wire:navigate
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium
                       text-white shadow-sm transition hover:bg-indigo-700">
                Editar Usuario
            </a>

        </div>

    </div>

</x-app-layout>
