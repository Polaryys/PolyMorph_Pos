<section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <h2 class="mb-3 text-base font-semibold text-slate-900">Agregar productos</h2>

    <div class="relative">
        <div class="flex items-center gap-2">
            <x-lucide-search class="h-5 w-5 shrink-0 text-slate-400" />
            <x-text-input id="buscar-producto" type="search" x-model="buscar"
                @input.debounce.300ms="buscarProductos()" @keydown.enter.prevent
                ::disabled="seleccionado !== null"
                placeholder="Buscar por nombre, SKU o marca..." autocomplete="off" class="w-full sm:max-w-xl" />
            <span x-show="cargando" x-cloak class="text-sm text-slate-500">Buscando...</span>
        </div>

        <div x-cloak x-show="buscar.trim().length > 0 && !seleccionado"
            class="absolute z-30 mt-1 max-h-80 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg sm:max-w-2xl">
            <template x-for="producto in resultados" :key="producto.presentacion_id">
                <button type="button" @click="seleccionar(producto)"
                    class="flex w-full items-center justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-indigo-50">
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold text-slate-900"
                            x-text="`${producto.nombre} · ${producto.marca}`"></span>
                        <span class="mt-1 block text-xs text-slate-500">
                            SKU: <span x-text="producto.sku"></span>
                        </span>
                        <span class="mt-1 block text-xs text-slate-600">
                            Precio: Q <span x-text="Number(producto.precio_unitario_actual).toFixed(2)"></span>
                        </span>
                    </span>
                    <span class="shrink-0 text-right text-xs text-slate-500">
                        Existencias<br><strong class="text-slate-800" x-text="producto.cantidad_actual"></strong>
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

    <div x-show="seleccionado" x-cloak x-transition
        class="mt-4 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Producto seleccionado</p>
                <h3 class="mt-1 font-semibold text-slate-900"
                    x-text="seleccionado ? `${seleccionado.nombre} · ${seleccionado.marca}` : ''"></h3>
                <p class="mt-1 text-xs text-slate-600">
                    SKU: <span x-text="seleccionado?.sku"></span>
                    <span class="mx-1">·</span>
                    Existencias: <span x-text="seleccionado?.cantidad_actual"></span>
                </p>
            </div>
            <button type="button" @click="cambiarProducto()"
                class="self-start rounded-md px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                Cambiar producto
            </button>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="space-y-1">
                <x-input-label for="cantidad-venta">Cantidad</x-input-label>
                <x-text-input id="cantidad-venta" type="number" min="1" step="1"
                    ::max="seleccionado?.cantidad_actual" x-model.number="cantidad" class="w-full" />
            </div>
            <div class="space-y-1">
                <x-input-label for="tipo-precio">Precio aplicado</x-input-label>
                <select id="tipo-precio" x-model="tipoPrecio"
                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="unitario">Unitario</option>
                    <template x-if="seleccionado?.precio_docena_actual !== null && Number(cantidad) >= 12">
                        <option value="docena">Por docena</option>
                    </template>
                    <template x-if="seleccionado?.precio_ciento_actual !== null && Number(cantidad) >= 100">
                        <option value="ciento">Por ciento</option>
                    </template>
                </select>
                <p class="text-xs text-slate-500">
                    Precio: Q <span x-text="seleccionado ? precioActual(seleccionado, tipoPrecio, cantidad).toFixed(2) : '0.00'"></span>
                </p>
            </div>
            <div class="space-y-1">
                <x-input-label for="descuento-venta">Descuento del renglón</x-input-label>
                <x-text-input id="descuento-venta" type="text" inputmode="decimal"
                    pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15" x-model="descuento"
                    placeholder="0.00" class="w-full text-right" />
            </div>
        </div>

        <button type="button" @click="agregarDetalle()"
            :disabled="!seleccionado || !Number.isInteger(Number(cantidad)) || Number(cantidad) < 1 || Number(cantidad) > Number(seleccionado?.cantidad_actual)"
            class="mt-4 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
            Agregar a la venta
        </button>
    </div>
</section>
