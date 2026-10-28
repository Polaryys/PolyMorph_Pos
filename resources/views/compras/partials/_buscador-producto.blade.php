<section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <h2 class="mb-4 text-base font-semibold text-slate-900">Agregar productos</h2>

    <div class="relative">
        <label for="buscar-producto" class="mb-1 block text-sm font-medium text-slate-700">
            Buscar producto base
        </label>
        <input id="buscar-producto" type="search" x-model="buscar"
            @input.debounce.300ms="buscarProductos()"
            @keydown.enter.prevent
            :disabled="seleccionado !== null"
            placeholder="Buscar por nombre, SKU o marca..."
            autocomplete="off"
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:bg-slate-100 sm:max-w-xl">

        <div x-cloak x-show="buscar.trim().length > 0 && !seleccionado"
            class="absolute z-30 mt-1 max-h-72 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg sm:max-w-xl">
            <p x-show="cargando" class="px-4 py-3 text-sm text-slate-500">Buscando productos...</p>

            <template x-for="producto in resultados" :key="producto.presentacion_id">
                <button type="button" @click="seleccionar(producto)"
                    class="flex w-full items-center justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-indigo-50">
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold text-slate-800" x-text="producto.nombre"></span>
                        <span class="mt-1 block text-xs text-slate-500">
                            SKU: <span x-text="producto.sku"></span>
                            <span class="mx-1">·</span>
                            <span x-text="producto.marca"></span>
                        </span>
                    </span>
                    <span class="shrink-0 text-xs text-slate-500">
                        Existencias: <strong x-text="producto.cantidad_actual"></strong>
                    </span>
                </button>
            </template>

            <p x-show="!cargando && !errorBusqueda && resultados.length === 0"
                class="px-4 py-3 text-sm text-slate-500">
                No se encontraron productos coincidentes.
            </p>
            <p x-show="errorBusqueda" class="px-4 py-3 text-sm text-red-600">
                No se pudo completar la búsqueda. Intenta nuevamente.
            </p>
        </div>
    </div>

    <div x-cloak x-show="seleccionado" x-transition
        class="mt-4 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Producto seleccionado</p>
                <h3 class="mt-1 text-base font-semibold text-slate-900" x-text="seleccionado?.nombre"></h3>
                <p class="mt-1 text-sm text-slate-600">
                    SKU: <span class="font-medium" x-text="seleccionado?.sku"></span>
                    <span class="mx-1">·</span>
                    Marca: <span class="font-medium" x-text="seleccionado?.marca"></span>
                </p>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-700">
                    <span>Existencia actual: <strong x-text="seleccionado?.cantidad_actual"></strong></span>
                    <span>Costo promedio: <strong x-text="'Q ' + Number(seleccionado?.costo_promedio_actual ?? 0).toFixed(2)"></strong></span>
                    <span>Precio actual: <strong x-text="'Q ' + Number(seleccionado?.precio_unitario_actual ?? 0).toFixed(2)"></strong></span>
                </div>
            </div>

            <button type="button" @click="cambiarProducto()"
                class="self-start rounded-lg px-3 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">
                Cambiar producto
            </button>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label for="cantidad-ingreso" class="mb-1 block text-sm font-medium text-slate-700">
                    Cantidad a ingresar <span class="text-red-500">*</span>
                </label>
                <input id="cantidad-ingreso" type="number" min="1" step="1"
                    :required="seleccionado !== null" :disabled="seleccionado === null"
                    x-model.number="cantidad"
                    class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="costo-unitario" class="mb-1 block text-sm font-medium text-slate-700">
                    Costo unitario <span class="text-red-500">*</span>
                </label>
                <input id="costo-unitario" type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                    maxlength="15" :required="seleccionado !== null" :disabled="seleccionado === null"
                    x-model="costoUnitario"
                    placeholder="0.00" title="Usa punto como separador decimal, por ejemplo 12.50"
                    class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="precio-nuevo" class="mb-1 block text-sm font-medium text-slate-700">
                    Nuevo precio de venta <span class="text-red-500">*</span>
                </label>
                <input id="precio-nuevo" type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                    maxlength="15" :required="seleccionado !== null" :disabled="seleccionado === null"
                    x-model="precioNuevo"
                    placeholder="0.00" title="Usa punto como separador decimal, por ejemplo 12.50"
                    class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="precio-docena-nuevo" class="mb-1 block text-sm font-medium text-slate-700">
                    Precio por docena
                </label>
                <input id="precio-docena-nuevo" type="text" inputmode="decimal"
                    pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                    :disabled="seleccionado === null" x-model="precioDocenaNuevo"
                    placeholder="Opcional" title="Usa punto como separador decimal, por ejemplo 12.50"
                    class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="precio-ciento-nuevo" class="mb-1 block text-sm font-medium text-slate-700">
                    Precio por ciento
                </label>
                <input id="precio-ciento-nuevo" type="text" inputmode="decimal"
                    pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                    :disabled="seleccionado === null" x-model="precioCientoNuevo"
                    placeholder="Opcional" title="Usa punto como separador decimal, por ejemplo 12.50"
                    class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-500">
            Para importes usa punto como separador decimal (por ejemplo, 12.50). Los precios por docena y ciento son opcionales.
        </p>

        <button type="button" @click="agregarDetalle()"
            :disabled="!Number.isInteger(Number(cantidad)) || Number(cantidad) < 1 || !decimalValido(costoUnitario, true, true) || !decimalValido(precioNuevo) || Number(precioNuevo) < 0 || !decimalValido(precioDocenaNuevo, false) || !decimalValido(precioCientoNuevo, false)"
            class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
            Agregar a la compra
        </button>
    </div>
</section>
