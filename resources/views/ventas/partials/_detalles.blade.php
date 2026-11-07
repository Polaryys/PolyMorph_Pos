<section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <x-table>
        <x-slot:headers>
            <th class="w-16 whitespace-nowrap px-1 py-2">Imagen</th>
            <th class="px-3 py-2">Artículo</th>
            <th class="whitespace-nowrap px-3 py-2 text-center">Cantidad</th>
            <th class="whitespace-nowrap px-3 py-2">Precio</th>
            <th class="whitespace-nowrap px-3 py-2 text-right">Descuento</th>
            <th class="whitespace-nowrap px-3 py-2 text-right">Subtotal</th>
            <th class="px-3 py-2 text-center"><span class="sr-only">Acción</span></th>
        </x-slot:headers>

        <template x-for="(detalle, indice) in detalles" :key="detalle.presentacion_id">
            <tr class="align-middle">
                <td class="w-16 px-1 py-1">
                    <span class="block h-14 w-14 overflow-hidden rounded-md bg-slate-100">
                        <template x-if="detalle.imagen">
                            <img :src="detalle.imagen" :alt="detalle.nombre"
                                class="h-full w-full object-cover">
                        </template>
                        <template x-if="!detalle.imagen">
                            <span class="flex h-full w-full items-center justify-center">
                                <x-lucide-image class="h-6 w-6 text-slate-400" />
                            </span>
                        </template>
                    </span>
                </td>
                <td class="px-3 py-2 font-semibold text-slate-900">
                    <span class="block"
                        x-text="detalle.marca ? `${detalle.nombre} - ${detalle.marca}` : detalle.nombre"></span>
                    <span class="mt-1 block text-xs font-normal text-slate-500">
                        SKU: <span x-text="detalle.sku"></span>
                    </span>
                    <input type="hidden" :name="`detalles[${indice}][presentacion_id]`" :value="detalle.presentacion_id">
                    <input type="hidden" :name="`detalles[${indice}][tipo_movimiento]`" value="VENTA">
                    <input type="hidden" :name="`detalles[${indice}][tipo_precio]`" :value="detalle.tipo_precio">
                    <input type="hidden" :name="`detalles[${indice}][cantidad]`" :value="detalle.cantidad">
                </td>
                <td class="px-3 py-2 text-center">
                    <div class="inline-flex items-center">
                        <button type="button" @click="ajustarCantidad(detalle, -1)"
                            :disabled="Number(detalle.cantidad) <= 1"
                            :aria-label="`Disminuir cantidad de ${detalle.nombre}`"
                            class="rounded-l-md border border-r-0 border-slate-300 px-2 py-2 text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                            <x-lucide-minus class="h-4 w-4" />
                        </button>
                        <input type="number" min="1" step="1"
                            :max="detalle.tipo_item === 'PRODUCTO' ? cantidadDisponible(detalle) : null"
                            @change="actualizarDescuentoAutomatico(detalle, true)"
                            x-model.number="detalle.cantidad"
                            class="w-16 appearance-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none border-y border-slate-300 p-2 text-center text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" @click="ajustarCantidad(detalle, 1)"
                            :disabled="detalle.tipo_item === 'PRODUCTO' && Number(detalle.cantidad) >= cantidadDisponible(detalle)"
                            :aria-label="`Aumentar cantidad de ${detalle.nombre}`"
                            class="rounded-r-md border border-l-0 border-slate-300 px-2 py-2 text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                            <x-lucide-plus class="h-4 w-4" />
                        </button>
                    </div>
                </td>
                <td class="whitespace-nowrap px-3 py-2">
                    <div class="inline-flex items-center gap-2">
                        <span class="text-base font-semibold text-slate-900"
                            x-text="'Q ' + precioActual(detalle, detalle.tipo_precio, detalle.cantidad).toFixed(2)"></span>
                        <select x-model="detalle.tipo_precio" @change="actualizarDescuentoAutomatico(detalle)"
                            class="w-24 rounded-md border-slate-300 px-2 py-1.5 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="unitario">Unitario</option>
                        <template x-if="detalle.precio_docena_actual !== null && Number(detalle.cantidad) * Number(detalle.factor || 1) >= 12">
                            <option value="docena">+ Docena</option>
                        </template>
                        <template x-if="detalle.precio_ciento_actual !== null && Number(detalle.cantidad) * Number(detalle.factor || 1) >= 100">
                            <option value="ciento">+ Ciento</option>
                        </template>
                        </select>
                    </div>
                </td>
                <td class="whitespace-nowrap px-3 py-2 text-right">
                    <label class="sr-only" :for="`descuento-${indice}`">Descuento para <span x-text="detalle.nombre"></span></label>
                    <div class="inline-flex items-center gap-1">
                        <span class="text-slate-500">Q</span>
                        <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                            maxlength="15" :id="`descuento-${indice}`"
                            :name="`detalles[${indice}][descuento]`" x-model="detalle.descuento"
                            class="w-24 rounded-md border-slate-300 p-2 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </td>
                <td class="whitespace-nowrap px-3 py-2 text-right font-semibold text-slate-900"
                    x-text="'Q ' + subtotalDetalle(detalle).toFixed(2)"></td>
                <td class="px-3 py-2 text-center">
                    <button type="button" @click="quitarDetalle(indice)" :aria-label="`Eliminar ${detalle.nombre}`"
                        class="rounded p-1 text-red-600 transition hover:bg-red-50 hover:text-red-800">
                        <x-lucide-x class="h-5 w-5" />
                    </button>
                </td>
            </tr>
        </template>

        <tr x-show="detalles.length === 0">
            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">
                Busca y agrega artículos para comenzar la venta.
            </td>
        </tr>
    </x-table>

    <div class="flex flex-col gap-5 border-t border-slate-200 p-4 sm:p-6">
        <dl class="w-full space-y-2 text-sm sm:ml-auto sm:max-w-sm">
            <div class="flex justify-between gap-6 text-slate-600">
                <dt>Total del importe</dt>
                <dd class="font-medium text-slate-900" x-text="'Q ' + totalImporte().toFixed(2)"></dd>
            </div>
            <div class="flex justify-between gap-6 text-slate-600">
                <dt>Total del descuento</dt>
                <dd class="font-medium text-slate-900" x-text="'Q ' + totalDescuento().toFixed(2)"></dd>
            </div>
            <div class="flex justify-between gap-6 border-t border-slate-200 pt-2 text-base font-semibold text-slate-900">
                <dt>Total final</dt>
                <dd x-text="'Q ' + totalVenta().toFixed(2)"></dd>
            </div>
        </dl>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <x-secondary-link href="{{ route('ventas.index') }}">Cancelar</x-secondary-link>
            <button type="button" @click="limpiarCarrito()"
                :disabled="detalles.length === 0"
                class="rounded-md border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50">
                Limpiar carrito
            </button>
            <button type="button" @click="abrirModalPago()" :disabled="!formularioValido()"
                class="rounded-md bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-50">
                Registrar venta
            </button>
        </div>
    </div>
</section>
