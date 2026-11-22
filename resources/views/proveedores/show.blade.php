<x-app-layout>

    <div class="space-y-8 text-sm">

        <!-- datos del proveedor -->
        <div class="space-y-4">

            <x-section-title>
                Datos del Proveedor
            </x-section-title>

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- NIT -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        NIT
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->nit }}
                    </div>
                </div>

                <!-- NOMBRE -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Nombre
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->nombre }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Teléfono
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->telefono }}
                    </div>
                </div>

                <!-- CORREO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Correo electrónico
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->correo ?: '—' }}
                    </div>
                </div>

            </div>
        </div>

        <!-- detalles de contacto -->
        <div class="pt-4 space-y-4">

            <x-section-title>
                Información de Contacto
            </x-section-title>

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- NOMBRE DEL CONTACTO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Nombre del contacto
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->nombre_contacto ?: '—' }}
                    </div>
                </div>

                <!-- TELÉFONO DEL CONTACTO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Teléfono del contacto
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->telefono_contacto ?: '—' }}
                    </div>
                </div>

                <!-- DIRECCIÓN -->
                <div class="sm:col-span-6">
                    <x-input-label>
                        Dirección
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->direccion ?: '—' }}
                    </div>
                </div>

                <!-- NOTAS -->
                <div class="sm:col-span-6">
                    <x-input-label>
                        Notas
                    </x-input-label>

                    <div class="mt-1 block w-full rounded-md border border-app-border-strong px-3 py-2">
                        {{ $proveedor->notas ?: '—' }}
                    </div>
                </div>

            </div>
        </div>

    </div>
        <div class="mt-4 flex justify-end gap-3">
        <x-secondary-link href="{{ route('proveedores.index') }}">
            Volver
        </x-secondary-link>

        <x-primary-link href="{{ route('proveedores.edit', $proveedor) }}" wire:navigate class="">
            Editar Proveedor
        </x-primary-link>

    </div>

</x-app-layout>
