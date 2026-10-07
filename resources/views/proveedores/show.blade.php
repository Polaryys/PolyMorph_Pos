<x-app-layout>

    <!-- BOTONES -->
    <div class="mb-3 mt-1 flex justify-start space-x-3">

        <a href="{{ route('proveedores.index') }}"
            class="rounded-md border border-gray-300 bg-white py-2 px-4
               text-sm font-medium text-gray-700 shadow-sm
               hover:bg-gray-50 transition">
            Volver
        </a>

        <a href="{{ route('proveedores.edit', $proveedor) }}"
            class="inline-flex justify-center rounded-md border border-transparent
               bg-indigo-600 py-2 px-4 text-sm font-medium text-white
               shadow-sm hover:bg-indigo-700 transition">
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
                    <label class="block text-sm font-medium text-gray-700">
                        NIT
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->nit }}
                    </div>
                </div>

                <!-- NOMBRE -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Nombre
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->nombre }}
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Teléfono
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->telefono }}
                    </div>
                </div>

                <!-- CORREO -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Correo electrónico
                    </label>

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
                    <label class="block text-sm font-medium text-gray-700">
                        Nombre del contacto
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->nombre_contacto ?: '—' }}
                    </div>
                </div>

                <!-- TELÉFONO DEL CONTACTO -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Teléfono del contacto
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->telefono_contacto ?: '—' }}
                    </div>
                </div>

                <!-- DIRECCIÓN -->
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">
                        Dirección
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $proveedor->direccion ?: '—' }}
                    </div>
                </div>

                <!-- NOTAS -->
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">
                        Notas
                    </label>

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