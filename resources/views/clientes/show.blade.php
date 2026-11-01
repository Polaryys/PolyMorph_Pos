<x-app-layout>



    <div class="space-y-7">

        <!-- DATOS DEL CLIENTE -->
        <section class="space-y-5">

            <h3 class="text-lg font-semibold text-slate-800">
                Datos del Cliente
            </h3>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">

                <!-- NIT -->
                <div class="sm:col-span-3">
                    <x-input-label>NIT</x-input-label>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $cliente->nit ?: '—' }}
                    </p>
                </div>

                <!-- CLASIFICACIÓN -->
                <div class="sm:col-span-3">
                    <x-input-label>Clasificación</x-input-label>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $cliente->clasificacion ?: '—' }}
                    </p>
                </div>

                <!-- NOMBRES -->
                <div class="sm:col-span-3">
                    <x-input-label>Nombres</x-input-label>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $cliente->nombres ?: '—' }}
                    </p>
                </div>

                <!-- APELLIDOS -->
                <div class="sm:col-span-3">
                    <x-input-label>Apellidos</x-input-label>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $cliente->apellidos ?: '—' }}
                    </p>
                </div>

            </div>

        </section>

        <div class="border-t border-slate-200"></div>

        <!-- INFORMACIÓN DE CONTACTO -->
        <section class="space-y-5">

            <h3 class="text-lg font-semibold text-slate-800">
                Información de Contacto
            </h3>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">

                <!-- TELÉFONO -->
                <div class="sm:col-span-3">
                    <x-input-label>Teléfono</x-input-label>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $cliente->telefono ?: '—' }}
                    </p>
                </div>

                <!-- CORREO -->
                <div class="sm:col-span-3">
                    <x-input-label>Correo electrónico</x-input-label>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $cliente->correo ?: '—' }}
                    </p>
                </div>

                <!-- NOTAS -->
                <div class="sm:col-span-6">
                    <x-input-label>Notas</x-input-label>

                    <div
                        class="mt-1 min-h-[76px] rounded-md border border-slate-200
                        bg-slate-50 p-3 text-sm text-slate-700 whitespace-pre-line">
                        {{ $cliente->notas ?: '—' }}
                    </div>
                </div>

            </div>

        </section>

    </div>
    <!-- BOTONES -->
    <div class="mb-5 mt-1 flex justify-start gap-3">

        <x-secondary-link href="{{ route('clientes.index') }}">
            Volver
        </x-secondary-link>

        <a href="{{ route('clientes.edit', $cliente) }}"
            class="inline-flex justify-center rounded-md border border-transparent
                bg-indigo-600 py-2 px-4 text-sm font-medium text-white
                shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
            Editar Cliente
        </a>

    </div>

</x-app-layout>
