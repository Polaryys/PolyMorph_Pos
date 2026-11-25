<x-app-layout>

    <div class="space-y-4 text-sm">

        <!-- datos del proveedor -->
        <div class="space-y-4">

            <x-section-title>
                Datos del Proveedor
            </x-section-title>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <!-- NIT -->
                <x-detail-item label="NIT">
                    <span class="font-normal">
                        {{ $proveedor->nit }}
                    </span>
                </x-detail-item>

                <!-- NOMBRE -->
                <x-detail-item label="Nombre">
                    <span class="font-normal">
                        {{ $proveedor->nombre }}
                    </span>
                </x-detail-item>

                <!-- TELÉFONO -->
                <x-detail-item label="Teléfono">
                    <span class="font-normal">
                        {{ $proveedor->telefono }}
                    </span>
                </x-detail-item>

                <!-- CORREO -->
                <x-detail-item label="Correo electrónico">
                    <span class="font-normal break-all">
                        {{ $proveedor->correo ?: '—' }}
                    </span>
                </x-detail-item>

            </div>
        </div>

        <!-- información de contacto -->
        <div class="space-y-4 pt-4">

            <x-section-title>
                Información de Contacto
            </x-section-title>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <!-- NOMBRE DEL CONTACTO -->
                <x-detail-item label="Nombre del contacto">
                    <span class="font-normal">
                        {{ $proveedor->nombre_contacto ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- TELÉFONO DEL CONTACTO -->
                <x-detail-item label="Teléfono del contacto">
                    <span class="font-normal">
                        {{ $proveedor->telefono_contacto ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- DIRECCIÓN -->
                <x-detail-item label="Dirección">
                    <span class="font-normal">
                        {{ $proveedor->direccion ?: '—' }}
                    </span>
                </x-detail-item>

                <!-- NOTAS -->
                <div class="sm:col-span-2">
                    <x-detail-item label="Notas">
                        <span class="font-normal whitespace-pre-line">
                            {{ $proveedor->notas ?: '—' }}
                        </span>
                    </x-detail-item>
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
