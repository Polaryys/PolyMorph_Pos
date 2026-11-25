<div class="relative">
    <x-input-label for="buscar-producto" class="mb-2">
        Agregar Cliente </x-input-label>

    <template x-if="clienteSeleccionado">
        <div
            class="flex min-h-10 items-center justify-between gap-3 rounded-md border border-app-primary-200 dark:border-app-primary-800 bg-app-primary-100/70 dark:bg-app-primary-900/30 px-3 py-2">
            <span class="min-w-0 flex flex-wrap items-center gap-x-2">
                <span class="truncate text-sm font-semibold text-app-text-primary"
                    x-text="clienteSeleccionado.nombre"></span>
                <span class="text-xs text-app-text-secondary whitespace-nowrap">
                    NIT: <span x-text="clienteSeleccionado.nit"></span>
                </span>
            </span>
            <button type="button" @click="quitarCliente()"
                class="shrink-0 text-sm font-medium text-app-primary-700 dark:text-app-primary-300 hover:text-app-primary-900 dark:hover:text-app-primary-200">
                Quitar
            </button>
        </div>
    </template>

    <template x-if="!clienteSeleccionado">
        <div class="flex min-w-0 items-center gap-2">
            <x-lucide-search class="h-5 w-5 shrink-0 text-app-text-muted" />
            <x-text-input id="buscar-cliente" type="search" x-model="buscarCliente"
                @input.debounce.300ms="buscarClientes()" @keydown.enter.prevent="$event.preventDefault()"
                placeholder="Buscar por nombre, NIT o teléfono..." autocomplete="off" class="w-full" />
            <span x-show="cargandoClientes" x-cloak class="text-sm text-app-text-muted">Buscando...</span>
        </div>
    </template>

    <div x-cloak x-show="!clienteSeleccionado && buscarCliente.trim().length > 0"
        class="absolute z-30 mt-1 max-h-80 w-full max-w-[90vw] overflow-y-auto rounded-xl border border-app-border bg-app-surface-elevated shadow-lg">
        <template x-for="cliente in resultadosClientes" :key="cliente.id">
            <button type="button" @click="seleccionarCliente(cliente)"
                class="w-full border-b border-app-border px-4 py-3 text-left transition hover:bg-app-surface-hover">
                <span class="block truncate text-sm font-semibold text-app-text-primary" x-text="cliente.nombre"></span>
                <span class="mt-1 block text-xs text-app-text-secondary">
                    NIT: <span x-text="cliente.nit"></span>
                    <template x-if="cliente.telefono">
                        <span> · Teléfono: <span x-text="cliente.telefono"></span></span>
                    </template>
                </span>
            </button>
        </template>

        <p x-show="!cargandoClientes && !errorBusquedaClientes && resultadosClientes.length === 0"
            class="px-4 py-3 text-sm text-app-text-muted">No se encontraron clientes coincidentes.</p>
    </div>

    <p x-show="errorBusquedaClientes" x-cloak class="mt-2 text-sm text-red-600 dark:text-red-400">
        No se pudo realizar la búsqueda de clientes. Inténtalo nuevamente.
    </p>
</div>
