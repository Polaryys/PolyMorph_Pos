<section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-1 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Detalle de la venta</h2>
            <p class="text-sm text-slate-500">Los precios finales se verifican al registrar.</p>
        </div>
        <p class="text-sm text-slate-600">
            Total: <strong class="text-lg text-slate-900" x-text="'Q ' + totalVenta().toFixed(2)"></strong>
        </p>
    </div>

    <x-table>
        <x-slot:headers>
            <th class="px-4 py-3">Artículo</th>
            <th class="whitespace-nowrap px-4 py-3 text-center">Existencia</th>
            <th class="whitespace-nowrap px-4 py-3 text-center">Cantidad</th>
            <th class="whitespace-nowrap px-4 py-3">Precio</th>
            <th class="whitespace-nowrap px-4 py-3 text-right">Descuento</th>
            <th class="whitespace-nowrap px-4 py-3 text-right">Subtotal</th>
            <th class="px-4 py-3 text-center">Acción</th>
        </x-slot:headers>

        <template x-for="(detalle, indice) in detalles" :key="detalle.presentacion_id">
            <tr class="align-middle">
                <td class="px-4 py-3">
                    <p class="font-semibold text-slate-900" x-text="detalle.nombre"></p>
                    <p class="mt-1 text-xs text-slate-500">
                        SKU: <span x-text="detalle.sku"></span>
                        <span class="mx-1">·</span><span x-text="detalle.marca"></span>
                    </p>
                    <input type="hidden" :name="`detalles[${indice}][presentacion_id]`" :value="detalle.presentacion_id">
                    <input type="hidden" :name="`detalles[${indice}][tipo_movimiento]`" value="VENTA">
                    <input type="hidden" :name="`detalles[${indice}][tipo_precio]`" :value="detalle.tipo_precio">
                    <input type="hidden" :name="`detalles[${indice}][cantidad]`" :value="detalle.cantidad">
                    <input type="hidden" :name="`detalles[${indice}][descuento]`" :value="detalle.descuento">
                </td>
                <td class="px-4 py-3 text-center" x-text="detalle.cantidad_actual"></td>
                <td class="px-4 py-3 text-center">
                    <input type="number" min="1" step="1" :max="detalle.cantidad_actual"
                        x-model.number="detalle.cantidad"
                        class="w-20 rounded-md border-slate-300 p-2 text-center text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </td>
                <td class="px-4 py-3">
                    <select x-model="detalle.tipo_precio"
                        class="w-32 rounded-md border-slate-300 p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="unitario">Unitario</option>
                        <template x-if="detalle.precio_docena_actual !== null && Number(detalle.cantidad) >= 12">
                            <option value="docena">Docena</option>
                        </template>
                        <template x-if="detalle.precio_ciento_actual !== null && Number(detalle.cantidad) >= 100">
                            <option value="ciento">Ciento</option>
                        </template>
                    </select>
                    <span class="mt-1 block text-xs text-slate-500"
                        x-text="'Q ' + precioActual(detalle, detalle.tipo_precio, detalle.cantidad).toFixed(2) + ' c/u'"></span>
                </td>
                <td class="px-4 py-3 text-right">
                    <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                        maxlength="15" x-model="detalle.descuento"
                        class="w-24 rounded-md border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900"
                    x-text="'Q ' + subtotalDetalle(detalle).toFixed(2)"></td>
                <td class="px-4 py-3 text-center">
                    <button type="button" @click="quitarDetalle(indice)"
                        class="font-medium text-red-600 transition hover:text-red-800">Quitar</button>
                </td>
            </tr>
        </template>

        <tr x-show="detalles.length === 0">
            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">
                Busca y agrega artículos para comenzar la venta.
            </td>
        </tr>
    </x-table>
</section>
