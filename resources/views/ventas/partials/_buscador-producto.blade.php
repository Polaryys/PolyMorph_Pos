<div class="relative">
    <div class="flex items-center gap-2">
            <x-lucide-search class="h-5 w-5 shrink-0 text-slate-400" />
            <x-text-input id="buscar-producto" type="search" x-model="buscar"
                @input.debounce.300ms="buscarProductos()" @keydown.enter.prevent
                placeholder="Buscar por nombre o SKU..." autocomplete="off" class="w-full sm:max-w-xl" />
            <span x-show="cargando" x-cloak class="text-sm text-slate-500">Buscando...</span>
        </div>

        <div x-cloak x-show="buscar.trim().length > 0"
            class="absolute z-30 mt-1 max-h-80 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg sm:max-w-2xl">
            <template x-for="producto in resultados" :key="producto.presentacion_id">
                <button type="button" @click="seleccionar(producto)"
                    class="flex w-full items-center justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-indigo-50">
                    <span class="flex min-w-0 items-center gap-3">
                        <span class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-slate-100">
                            <template x-if="producto.imagen">
                                <img :src="producto.imagen" :alt="producto.nombre"
                                    class="h-full w-full object-cover">
                            </template>
                            <template x-if="!producto.imagen">
                                <span class="flex h-full w-full items-center justify-center">
                                    <x-lucide-image class="h-6 w-6 text-slate-400" />
                                </span>
                            </template>
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-slate-900"
                                x-text="producto.marca ? `${producto.nombre} - ${producto.marca}` : producto.nombre"></span>
                            <span class="mt-1 block text-xs text-slate-500">
                                SKU: <span x-text="producto.sku"></span>
                            </span>
                            <span class="mt-1 block text-xs text-slate-600">
                                Precio: Q <span x-text="Number(producto.precio_unitario_actual).toFixed(2)"></span>
                            </span>
                        </span>
                    </span>
                    <span class="shrink-0 text-right text-xs text-slate-500">
                        <template x-if="producto.tipo_item === 'PRODUCTO'">
                            <span>Existencias<br><strong class="text-slate-800"
                                x-text="cantidadDisponible(producto)"></strong></span>
                        </template>
                        <template x-if="producto.tipo_item === 'SERVICIO'">
                            <span class="font-medium text-indigo-700">Servicio</span>
                        </template>
                    </span>
                </button>
            </template>

            <p x-show="!cargando && !errorBusqueda && resultados.length === 0"
                class="px-4 py-3 text-sm text-slate-500">No se encontraron productos coincidentes.</p>
        </div>

        <p x-show="errorBusqueda" x-cloak class="mt-2 text-sm text-red-600">
            No se pudo realizar la búsqueda. Inténtalo nuevamente.
        </p>
    </div>
