<x-app-layout>



    <div class="space-y-7">

        <!-- DATOS DEL CLIENTE -->
        <section class="space-y-3">

            <x-section-title>
                Datos del Cliente
            </x-section-title>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">

                <!-- NIT -->
                <div class="sm:col-span-3">
                    <x-input-label>NIT</x-input-label>
                    <div class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->nit ?: '—' }}
                    </div>
                </div>

                <!-- CLASIFICACIÓN -->
                <div class="sm:col-span-3">
                    <x-input-label>Clasificación</x-input-label>
                    <div class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->clasificacion ?: '—' }}
                    </div>
                </div>

                <!-- NOMBRES -->
                <div class="sm:col-span-3">
                    <x-input-label>Nombres</x-input-label>
                    <div class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->nombres ?: '—' }}
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div class="sm:col-span-3">
                    <x-input-label>Apellidos</x-input-label>
                    <div class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->apellidos ?: '—' }}
                    </div>
                </div>

            </div>

        </section>


        <!-- INFORMACIÓN DE CONTACTO -->
        <section class="space-y-5">

            <x-section-title>
            Información de Contacto
            </x-section-title>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <x-input-label>Teléfono</x-input-label>
                    <div class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->telefono ?: '—' }}
                    </div>
                </div>

                <!-- CORREO -->
                <div class="sm:col-span-3">
                    <x-input-label>Correo electrónico</x-input-label>
                    <div class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->correo ?: '—' }}
                    </div>
                </div>

                <!-- NOTAS -->
                <div class="sm:col-span-6">
                    <x-input-label>Notas</x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border text-sm border-app-border-strong px-3 py-2">
                        {{ $cliente->notas ?: '—' }}
                    </class=>
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
