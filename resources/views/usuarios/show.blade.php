<x-app-layout>

    <div class="space-y-6 text-sm">

        <!-- FOTO DEL USUARIO -->
        <section class="space-y-4">

            <div class="flex flex-col items-center justify-center space-y-3 pt-2">

                <div class="relative h-32 w-32">

                    @if ($usuario->imagen_id && $usuario->imagen)
                        <img src="{{ asset('storage/imagenes/' . $usuario->imagen->nombre_archivo) }}"
                            alt="Foto de perfil"
                            class="h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm">
                    @elseif ($usuario->foto)
                        <img src="{{ asset('storage/' . $usuario->foto) }}" alt="Foto de perfil"
                            class="h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm">
                    @else
                        <div
                            class="flex h-32 w-32 items-center justify-center rounded-full border-2 border-app-border-strong bg-app-background text-app-text-muted">
                            <x-lucide-user-round class="h-16 w-16" />
                        </div>
                    @endif

                </div>

            </div>

        </section>

        <!-- INFORMACIÓN DEL USUARIO -->
        <section class="space-y-4">

            <x-section-title class="border-b border-app-border-strong pt-2">
                Información del usuario
            </x-section-title>

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">

                <!-- NOMBRES -->
                <div>
                    <x-input-label>
                        {{ __('Nombres') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->nombres }}
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div>
                    <x-input-label>
                        {{ __('Apellidos') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->apellidos }}
                    </div>
                </div>

                <!-- USUARIO -->
                <div>
                    <x-input-label>
                        {{ __('Usuario') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->username }}
                    </div>
                </div>

                <!-- ROL -->
                <div>
                    <x-input-label>
                        {{ __('Rol') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->rol }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div>
                    <x-input-label>
                        {{ __('Teléfono') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->telefono ?: 'No registrado' }}
                    </div>
                </div>

                <!-- CORREO ELECTRÓNICO -->
                <div>
                    <x-input-label>
                        {{ __('Correo electrónico') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->email ?: 'No registrado' }}
                    </div>
                </div>

                <!-- ESTADO -->
                <div>
                    <x-input-label>
                        {{ __('Estado') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                    </div>
                </div>

                <!-- FECHA DE CREACIÓN -->
                <div>
                    <x-input-label>
                        {{ __('Fecha de creación') }}
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $usuario->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

            </div>

        </section>
        <!-- BOTONES DE ACCIÓN -->
        <div class="flex items-center justify-end gap-3 border-t border-app-border-strong pt-5 pb-6">

            <x-secondary-link href="{{ route('usuarios.index') }}" wire:navigate>
                Volver
            </x-secondary-link>

            <x-primary-link href="{{ route('usuarios.edit', $usuario) }}" wire:navigate class="">
                Editar usuario
            </x-primary-link>

        </div>

    </div>

</x-app-layout>
