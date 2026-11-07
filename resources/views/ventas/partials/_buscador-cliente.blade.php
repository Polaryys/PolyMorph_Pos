<div class="relative">
    <label for="buscar-cliente" class="mb-1 block text-sm font-medium text-slate-700">
        Cliente registrado <span class="font-normal text-slate-500">(opcional)</span>
    </label>

    <template x-if="clienteSeleccionado">
        <div class="flex min-h-10 items-center justify-between gap-3 rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2">
            <span class="min-w-0">
                <span class="block truncate text-sm font-semibold text-slate-900"
                    x-text="clienteSeleccionado.nombre"></span>
                <span class="block text-xs text-slate-600">
                    NIT: <span x-text="clienteSeleccionado.nit"></span>
                </span>
            </span>
            <button type="button" @click="quitarCliente()"
                class="shrink-0 text-sm font-medium text-indigo-700 hover:text-indigo-900">
                Quitar
            </button>
        </div>
    </template>

    <template x-if="!clienteSeleccionado">
        <div class="flex items-center gap-2">
            <x-lucide-search class="h-5 w-5 shrink-0 text-slate-400" />
            <x-text-input id="buscar-cliente" type="search" x-model="buscarCliente"
                @input.debounce.300ms="buscarClientes()" @keydown.enter.prevent
                placeholder="Buscar por nombre, NIT o teléfono..." autocomplete="off" class="w-full" />
            <span x-show="cargandoClientes" x-cloak class="text-sm text-slate-500">Buscando...</span>
        </div>
    </template>

    <div x-cloak x-show="!clienteSeleccionado && buscarCliente.trim().length > 0"
        class="absolute z-30 mt-1 max-h-80 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
        <template x-for="cliente in resultadosClientes" :key="cliente.id">
            <button type="button" @click="seleccionarCliente(cliente)"
                class="w-full border-b border-slate-100 px-4 py-3 text-left transition hover:bg-indigo-50">
                <span class="block truncate text-sm font-semibold text-slate-900" x-text="cliente.nombre"></span>
                <span class="mt-1 block text-xs text-slate-600">
                    NIT: <span x-text="cliente.nit"></span>
                    <template x-if="cliente.telefono">
                        <span> · Teléfono: <span x-text="cliente.telefono"></span></span>
                    </template>
                </span>
            </button>
        </template>

        <p x-show="!cargandoClientes && !errorBusquedaClientes && resultadosClientes.length === 0"
            class="px-4 py-3 text-sm text-slate-500">No se encontraron clientes coincidentes.</p>
    </div>

    <p x-show="errorBusquedaClientes" x-cloak class="mt-2 text-sm text-red-600">
        No se pudo realizar la búsqueda de clientes. Inténtalo nuevamente.
    </p>
</div>
