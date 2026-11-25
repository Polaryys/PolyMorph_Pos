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

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">

                <!-- NOMBRES -->
                <x-detail-item label="Nombres">
                    <span class="font-normal">
                        {{ $usuario->nombres }}
                    </span>
                </x-detail-item>

                <!-- APELLIDOS -->
                <x-detail-item label="Apellidos">
                    <span class="font-normal">
                        {{ $usuario->apellidos }}
                    </span>
                </x-detail-item>

                <!-- USUARIO -->
                <x-detail-item label="Usuario">
                    <span class="font-normal">
                        {{ $usuario->username }}
                    </span>
                </x-detail-item>

                <!-- ROL -->
                <x-detail-item label="Rol">
                    <span class="font-normal">
                        {{ $usuario->rol }}
                    </span>
                </x-detail-item>

                <!-- TELÉFONO -->
                <x-detail-item label="Teléfono">
                    <span class="font-normal">
                        {{ $usuario->telefono ?: 'No registrado' }}
                    </span>
                </x-detail-item>

                <!-- CORREO ELECTRÓNICO -->
                <x-detail-item label="Correo electrónico">
                    <span class="font-normal break-all">
                        {{ $usuario->email ?: 'No registrado' }}
                    </span>
                </x-detail-item>

                <!-- ESTADO -->
                <x-detail-item label="Estado">
                    @if ($usuario->estado)
                        <span
                            class="inline-flex items-center rounded-md border border-emerald-500/20
                             bg-emerald-500/10 px-2.5 py-1 text-sm font-normal
                             text-emerald-700 dark:text-emerald-400">
                            Activo
                        </span>
                    @else
                        <span
                            class="inline-flex items-center rounded-md border border-red-500/20
                             bg-red-500/10 px-2.5 py-1 text-sm font-normal
                             text-red-700 dark:text-red-400">
                            Inactivo
                        </span>
                    @endif
                </x-detail-item>

                <!-- FECHA DE CREACIÓN -->
                <x-detail-item label="Fecha de creación">
                    <span class="font-normal tabular-nums">
                        {{ $usuario->created_at->format('d/m/Y H:i') }}
                    </span>
                </x-detail-item>

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
