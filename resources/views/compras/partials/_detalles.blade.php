<section class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-1 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Productos de la compra</h2>
            <p class="mt-1 text-sm text-slate-500">
                Los costos y precios capturados se aplicarán al registrar la compra. El costo promedio se calcula
                con el inventario vigente en ese momento.
            </p>
        </div>
        <p class="text-sm text-slate-600">
            Total: <strong class="text-slate-900" x-text="'Q ' + totalCompra().toFixed(2)"></strong>
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-100 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Existencia</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Costo unitario</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Precio unitario</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Precio docena</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Precio ciento</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Subtotal</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <template x-for="(detalle, indice) in detalles" :key="detalle.presentacion_id">
                    <tr class="align-middle">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-900" x-text="detalle.nombre"></p>
                            <p class="mt-1 text-xs text-slate-500">
                                SKU: <span x-text="detalle.sku"></span>
                                <span class="mx-1">·</span>
                                <span x-text="detalle.marca"></span>
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Costo promedio actual (referencia): Q <span x-text="Number(detalle.costo_promedio_actual).toFixed(2)"></span>
                                <span class="mx-1">·</span>
                                Precio actual: Q <span x-text="Number(detalle.precio_unitario_actual).toFixed(2)"></span>
                                <span class="mx-1">·</span>
                                Docena: <span x-text="detalle.precio_docena_actual === null ? 'No definido' : 'Q ' + Number(detalle.precio_docena_actual).toFixed(2)"></span>
                                <span class="mx-1">·</span>
                                Ciento: <span x-text="detalle.precio_ciento_actual === null ? 'No definido' : 'Q ' + Number(detalle.precio_ciento_actual).toFixed(2)"></span>
                            </p>
                            <input type="hidden" :name="`detalles[${indice}][presentacion_id]`" :value="detalle.presentacion_id">
                            <input type="hidden" :name="`detalles[${indice}][cantidad]`" :value="detalle.cantidad">
                            <input type="hidden" :name="`detalles[${indice}][costo_unitario]`" :value="detalle.costo_unitario">
                            <input type="hidden" :name="`detalles[${indice}][precio_unitario_nuevo]`" :value="detalle.precio_unitario_nuevo">
                            <input type="hidden" :name="`detalles[${indice}][precio_docena_nuevo]`" :value="detalle.precio_docena_nuevo ?? ''">
                            <input type="hidden" :name="`detalles[${indice}][precio_ciento_nuevo]`" :value="detalle.precio_ciento_nuevo ?? ''">
                        </td>
                        <td class="px-4 py-3 text-center" x-text="detalle.cantidad_actual"></td>
                        <td class="px-4 py-3">
                            <input type="number" min="1" step="1" x-model.number="detalle.cantidad"
                                class="w-24 rounded-md border border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                maxlength="15" x-model="detalle.costo_unitario"
                                placeholder="0.00" title="Usa punto como separador decimal, por ejemplo 12.50"
                                class="w-28 rounded-md border border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                maxlength="15" x-model="detalle.precio_unitario_nuevo"
                                placeholder="0.00" title="Usa punto como separador decimal, por ejemplo 12.50"
                                class="w-28 rounded-md border border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                maxlength="15" x-model="detalle.precio_docena_nuevo"
                                placeholder="Opcional" title="Usa punto como separador decimal, por ejemplo 12.50"
                                class="w-28 rounded-md border border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                maxlength="15" x-model="detalle.precio_ciento_nuevo"
                                placeholder="Opcional" title="Usa punto como separador decimal, por ejemplo 12.50"
                                class="w-28 rounded-md border border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-slate-900"
                            x-text="'Q ' + (Number(detalle.cantidad) * Number(detalle.costo_unitario)).toFixed(2)"></td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" @click="quitarDetalle(indice)" title="Quitar producto"
                                class="font-medium text-red-600 transition hover:text-red-800">
                                Quitar
                            </button>
                        </td>
                    </tr>
                </template>

                <tr x-show="detalles.length === 0">
                    <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                        Busca y agrega un producto base para comenzar la compra.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
