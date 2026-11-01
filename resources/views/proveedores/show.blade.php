<x-app-layout>

    <!-- BOTONES -->
    <div class="mb-5 mt-1 flex justify-start gap-3">
        <x-secondary-link href="{{ route('proveedores.index') }}">
            Volver
        </x-secondary-link>

        <a href="{{ route('proveedores.edit', $proveedor) }}"
            class="inline-flex justify-center rounded-md border border-transparent
                bg-indigo-600 py-2 px-4 text-sm font-medium text-white
                shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
            Editar Proveedor
        </a>

    </div>

    <div class="space-y-8 divide-y divide-gray-200">

        <!-- datos del proveedor -->
        <div class="space-y-4">

            <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                Datos del Proveedor
            </h3>

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- NIT -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        NIT
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->nit }}
                    </div>
                </div>

                <!-- NOMBRE -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Nombre
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->nombre }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Teléfono
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->telefono }}
                    </div>
                </div>

                <!-- CORREO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Correo electrónico
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->correo ?: '—' }}
                    </div>
                </div>

            </div>
        </div>


        <!-- detalles de contacto -->
        <div class="pt-4 space-y-4">

            <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                Información de Contacto
            </h3>

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- NOMBRE DEL CONTACTO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Nombre del contacto
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->nombre_contacto ?: '—' }}
                    </div>
                </div>

                <!-- TELÉFONO DEL CONTACTO -->
                <div class="sm:col-span-3">
                    <x-input-label>
                        Teléfono del contacto
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->telefono_contacto ?: '—' }}
                    </div>
                </div>

                <!-- DIRECCIÓN -->
                <div class="sm:col-span-6">
                    <x-input-label>
                        Dirección
                    </x-input-label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->direccion ?: '—' }}
                    </div>
                </div>

                <!-- NOTAS -->
                <div class="sm:col-span-6">
                    <x-input-label>
                        Notas
                    </x-input-label>

                    <div
                        class="mt-1 block w-full min-h-[76px] rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700 whitespace-pre-line">
                        {{ $proveedor->notas ?: '—' }}
                    </div>
                </div>

            </div>
        </div>

    </div>

</x-app-layout>