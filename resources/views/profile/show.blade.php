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

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3 text-sm">

                <!-- NOMBRES -->
                <div>
                    <x-input-label>
                        Nombres
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $user->nombres }}
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div>
                    <x-input-label>
                        Apellidos
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $user->apellidos }}
                    </div>
                </div>

                <!-- USUARIO -->
                <div>
                    <x-input-label>
                        Usuario
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $user->username }}
                    </div>
                </div>

                <!-- ROL -->
                <div>
                    <x-input-label>
                        Rol
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $user->rol }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div>
                    <x-input-label>
                        Telefono
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $user->telefono ?: 'No registrado' }}
                    </div>
                </div>

                <!-- CORREO ELECTRÓNICO -->
                <div>
                    <x-input-label>
                        Correo electrónico
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $user->email ?: 'No registrado' }}
                    </div>
                </div>

            </div>

        </section>

    </div>
</x-app-layout>
