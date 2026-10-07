<x-app-layout>

    <!-- BOTONES -->
    <div class="mb-3 mt-1 flex justify-start space-x-3">

        <a href="{{ route('clientes.index') }}"
            class="rounded-md border border-gray-300 bg-white py-2 px-4
               text-sm font-medium text-gray-700 shadow-sm
               hover:bg-gray-50 transition">
            Volver
        </a>

        <a href="{{ route('clientes.edit', $cliente) }}"
            class="inline-flex justify-center rounded-md border border-transparent
               bg-indigo-600 py-2 px-4 text-sm font-medium text-white
               shadow-sm hover:bg-indigo-700 transition">
            Editar Cliente
        </a>

    </div>

    <div class="space-y-8 divide-y divide-gray-200">

        <!-- datos del cliente -->
        <div class="space-y-4">

            <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                Datos del Cliente
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
                        {{ $cliente->nit }}
                    </div>
                </div>

                <!-- CLASIFICACIÓN -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Clasificación
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $cliente->clasificacion }}
                    </div>
                </div>

                <!-- NOMBRES -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Nombres
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $cliente->nombres }}
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Apellidos
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $cliente->apellidos ?: '—' }}
                    </div>
                </div>

            </div>
        </div>


        <!-- info contacto -->
        <div class="pt-4 space-y-4">

            <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                Información de Contacto
            </h3>

            <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Teléfono
                    </label>

                    <div
                        class="mt-1 block w-full rounded-md border border-gray-200
                            bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $cliente->telefono ?: '—' }}
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
                        {{ $cliente->correo ?: '—' }}
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
                        {{ $cliente->notas ?: '—' }}
                    </div>
                </div>

            </div>
        </div>

    </div>

</x-app-layout>
