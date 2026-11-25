<x-app-layout>

    <div class="space-y-7">

        <!-- DATOS DEL CLIENTE -->
        <section class="space-y-4">

            <x-section-title>
                Datos del Cliente
            </x-section-title>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <!-- NIT -->
                <x-detail-item label="NIT">
                    <span class="font-normal">
                        {{ $cliente->nit ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- CLASIFICACIÓN -->
                <x-detail-item label="Clasificación">
                    @if ($cliente->clasificacion === 'Mayorista')
                        <span
                            class="inline-flex items-center rounded-md border border-blue-500/20
                             bg-blue-500/10 px-2.5 py-1 text-sm font-normal
                             text-blue-700 dark:text-blue-400">
                            Mayorista
                        </span>
                    @elseif ($cliente->clasificacion === 'Minorista')
                        <span
                            class="inline-flex items-center rounded-md border border-violet-500/20
                             bg-violet-500/10 px-2.5 py-1 text-sm font-normal
                             text-violet-700 dark:text-violet-400">
                            Minorista
                        </span>
                    @else
                        <span class="font-normal">
                            {{ $cliente->clasificacion ?: '—' }}
                        </span>
                    @endif
                </x-detail-item>

                <!-- NOMBRES -->
                <x-detail-item label="Nombres">
                    <span class="font-normal">
                        {{ $cliente->nombres ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- APELLIDOS -->
                <x-detail-item label="Apellidos">
                    <span class="font-normal">
                        {{ $cliente->apellidos ?: '—' }}
                    </span>
                </x-detail-item>

            </div>

        </section>


        <!-- INFORMACIÓN DE CONTACTO -->
        <section class="space-y-4">

            <x-section-title>
                Información de Contacto
            </x-section-title>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <!-- TELÉFONO -->
                <x-detail-item label="Teléfono">
                    <span class="font-normal">
                        {{ $cliente->telefono ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- CORREO -->
                <x-detail-item label="Correo electrónico">
                    <span class="font-normal break-all">
                        {{ $cliente->correo ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- NOTAS -->
                <div class="sm:col-span-2">
                    <x-detail-item label="Notas">
                        <span class="font-normal whitespace-pre-line">
                            {{ $cliente->notas ?: '—' }}
                        </span>
                    </x-detail-item>
                </div>

            </div>

        </section>

    </div>
    <!-- BOTONES -->
    <div class="mb-6 mt-4 flex justify-end gap-3">

        <x-secondary-link href="{{ route('clientes.index') }}">
            Volver
        </x-secondary-link>

        <x-primary-link href="{{ route('clientes.edit', $cliente) }}" wire:navigate class="">
            Editar Cliente
        </x-primary-link>

    </div>

</x-app-layout>
