<x-app-layout>
    <div class="space-y-6">

        <!-- FOTO DE PERFIL -->
        <section class="space-y-4">

            <div class="flex flex-col items-center justify-center space-y-3 pt-2">

                <div class="relative h-32 w-32">

                    @if ($user->imagen_id && $user->imagen)
                        <img src="{{ asset('storage/imagenes/' . $user->imagen->nombre_archivo) }}" alt="Foto de perfil"
                            class="h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm">
                    @elseif ($user->foto)
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto de perfil"
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

            <x-section-title>
                Información del Usuario
            </x-section-title>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">

                <!-- NOMBRES -->
                <x-detail-item label="Nombres">
                    <span class="font-normal">
                        {{ $user->nombres }}
                    </span>
                </x-detail-item>

                <!-- APELLIDOS -->
                <x-detail-item label="Apellidos">
                    <span class="font-normal">
                        {{ $user->apellidos }}
                    </span>
                </x-detail-item>

                <!-- USUARIO -->
                <x-detail-item label="Usuario">
                    <span class="font-normal">
                        {{ $user->username }}
                    </span>
                </x-detail-item>

                <!-- ROL -->
                <x-detail-item label="Rol">
                    <span class="font-normal">
                        {{ $user->rol }}
                    </span>
                </x-detail-item>

                <!-- TELÉFONO -->
                <x-detail-item label="Teléfono">
                    <span class="font-normal">
                        {{ $user->telefono ?: 'No registrado' }}
                    </span>
                </x-detail-item>

                <!-- CORREO ELECTRÓNICO -->
                <x-detail-item label="Correo electrónico">
                    <span class="font-normal break-all">
                        {{ $user->email ?: 'No registrado' }}
                    </span>
                </x-detail-item>

            </div>

        </section>

    </div>
</x-app-layout>
