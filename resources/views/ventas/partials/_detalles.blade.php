<section class="min-w-0 overflow-hidden rounded-xl border border-app-border bg-app-surface shadow-sm">

    {{-- Datos compartidos del formulario --}}
    <template x-for="(detalle, indice) in detalles" :key="detalle.presentacion_id">
        <div>
            <input type="hidden" :name="`detalles[${indice}][presentacion_id]`" :value="detalle.presentacion_id">

            <input type="hidden" :name="`detalles[${indice}][tipo_movimiento]`" value="VENTA">

            <input type="hidden" :name="`detalles[${indice}][tipo_precio]`" :value="detalle.tipo_precio">

            <input type="hidden" :name="`detalles[${indice}][cantidad]`" :value="detalle.cantidad">

            <input type="hidden" :name="`detalles[${indice}][descuento]`" :value="detalle.descuento">
        </div>
    </template>

    {{-- Escritorio --}}
    <div class="hidden md:block">
        <div class="w-full">
            <table class="w-full table-fixed border-collapse text-left text-sm">

                <thead
                    class="bg-app-table-header text-xs font-semibold uppercase tracking-wider text-app-table-header-text">

                    <tr>
                        <th class="w-16 px-2 py-2">Imagen</th>
                        <th class="w-128 min-w-0 whitespace-nowrap px-3 py-2 max-w-xs">Artículo</th>
                        <th class="w-32 px-3  py-2 text-center">Cantidad</th>
                        <th class="w-64 px-3 py-2 text-center w-32">Precio</th>
                        <th class="w-32 px-3 py-2 text-center">Descuento</th>
                        <th class="w-24 px-3 py-2 text-center">Subtotal</th>
                        <th class="w-24 px-3  py-2 text-center">Acción</th>
                    </tr>
                </thead>

                <tbody
                    class="divide-y divide-app-border text-app-text-secondary
                    [&_tr:nth-child(odd)]:bg-app-surface
                    [&_tr:nth-child(even)]:bg-app-surface-alt/30
                    [&_tr:hover]:bg-app-surface-hover
                    [&_tr]:transition-colors [&_tr]:duration-150">

                    <template x-for="(detalle, indice) in detalles" :key="detalle.presentacion_id">
                        <tr class="align-middle">
                            <td class="w-16 px-1 py-1">
                                <span class="relative block h-14 w-14 overflow-hidden rounded-md">
                                    <template x-if="detalle.imagen">
                                        <img :src="detalle.imagen" :alt="detalle.nombre"
                                            class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!detalle.imagen">
                                        <span class="flex h-full w-full items-center justify-center">
                                            <x-lucide-image class="h-6 w-6 text-slate-400" />
                                        </span>
                                    </template>
                                    <template
                                        x-if="detalle.tipo_item === 'PRODUCTO' && detalle.cantidad_actual !== null">
                                        <span
                                            class="absolute bottom-0 right-0 rounded-tl-md bg-slate-900/60 px-1 py-0.5 text-[10px] font-medium leading-none text-white/90"
                                            title="Existencias actuales" x-text="detalle.cantidad_actual"></span>
                                    </template>
                                </span>
                            </td>

                            <td class="min-w-0 px-3 py-2 font-normal text-[15px] text-app-text-primary">
                                <span class="block min-w-0 truncate whitespace-nowrap"
                                    x-text="detalle.marca ? `${detalle.nombre} - ${detalle.marca}` : detalle.nombre"></span>
                                <span class="mt-1 block text-xs font-normal text-app-text-secondary truncate">
                                    SKU: <span x-text="detalle.sku"></span>
                                </span>
                                <input type="hidden" :name="`detalles[${indice}][presentacion_id]`"
                                    :value="detalle.presentacion_id">
                                <input type="hidden" :name="`detalles[${indice}][tipo_movimiento]`" value="VENTA">
                                <input type="hidden" :name="`detalles[${indice}][tipo_precio]`"
                                    :value="detalle.tipo_precio">
                                <input type="hidden" :name="`detalles[${indice}][cantidad]`"
                                    :value="detalle.cantidad">
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <button type="button" @click="ajustarCantidad(detalle, -1)"
                                        :disabled="Number(detalle.cantidad) <= 1"
                                        :aria-label="`Disminuir cantidad de ${detalle.nombre}`"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-app-border-strong text-app-text-secondary shadow-sm transition hover:bg-app-surface-alt hover:text-app-text-primary disabled:cursor-not-allowed disabled:opacity-40">
                                        <x-lucide-minus class="h-4 w-4" />
                                    </button>

                                    <input type="number" min="1" step="1"
                                        :max="detalle.tipo_item === 'PRODUCTO' ? cantidadDisponible(detalle) : null"
                                        @change="actualizarDescuentoAutomatico(detalle, true)"
                                        x-model.number="detalle.cantidad"
                                        class="h-9 w-16 max-w-xs rounded-md border border-app-border-strong p-2 text-center text-[15px] shadow-sm [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none focus:border-app-primary-500 focus:ring-1 focus:ring-app-primary-500">

                                    <button type="button" @click="ajustarCantidad(detalle, 1)"
                                        :disabled="detalle.tipo_item === 'PRODUCTO' && Number(detalle.cantidad) >=
                                            cantidadDisponible(
                                                detalle)"
                                        :aria-label="`Aumentar cantidad de ${detalle.nombre}`"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-app-border-strong text-app-text-secondary shadow-sm transition hover:bg-app-surface-alt hover:text-text-app-text-primary disabled:cursor-not-allowed disabled:opacity-40">
                                        <x-lucide-plus class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>

                            <td class="whitespace-nowrap min-w-0 text-center px-3 py-2">
                                <div class="inline-flex items-center gap-2">
                                    <span class="text-base font-normal text-app-text-primary"
                                        x-text="'Q ' + precioActual(detalle, detalle.tipo_precio, detalle.cantidad).toFixed(2)"></span>
                                    <select x-model="detalle.tipo_precio"
                                        @change="actualizarDescuentoAutomatico(detalle)"
                                        class="w-24 max-w-xs bg-app-background rounded-md border-app-border px-2 py-1.5 focus:border-app-primary-500 focus:ring-app-primary-500">
                                        <option value="unitario" class="bg-app-background">Unitario</option>
                                        <template
                                            x-if="detalle.precio_docena_actual !== null && Number(detalle.cantidad) * Number(detalle.factor || 1) >= 12">
                                            <option value="docena" class="bg-app-background">+ Docena</option>
                                        </template>
                                        <template
                                            x-if="detalle.precio_ciento_actual !== null && Number(detalle.cantidad) * Number(detalle.factor || 1) >= 100">
                                            <option value="ciento" class="bg-app-background">+ Ciento</option>
                                        </template>
                                    </select>
                                </div>
                            </td>

                            <td class="whitespace-nowrap min-w-0 px-3 py-2 text-center">
                                <label class="sr-only" :for="`descuento-${indice}`">Descuento para <span
                                        x-text="detalle.nombre"></span></label>
                                <div class="inline-flex items-center gap-1 text-[15px]">
                                    <span class="text-app-text-muted">Q</span>
                                    <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                        maxlength="15" :id="`descuento-${indice}`"
                                        :name="`detalles[${indice}][descuento]`" x-model="detalle.descuento"
                                        class="w-20 max-w-xs rounded-md border-app-border-strong p-2 text-center focus:border-app-primary-500 focus:ring-app-primary-500">
                                </div>
                            </td>

                            <td class="whitespace-nowrap min-w-0 px-3 py-2 text-center font-semibold text-[15px] text-app-text-primary"
                                x-text="'Q ' + subtotalDetalle(detalle).toFixed(2)"></td>

                            <td class="px-3 py-2 text-center">
                                <button type="button" @click="quitarDetalle(indice)"
                                    :aria-label="`Eliminar ${detalle.nombre}`"
                                    class="rounded p-1 text-red-600 transition hover:bg-red-50 hover:text-red-800">
                                    <x-lucide-x class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="detalles.length === 0">
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-app-border-hover">
                            Busca y agrega artículos para comenzar la venta.
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>

    {{-- Móviles --}}

    <div class="space-y-3 p-3 md:hidden">

        <template x-for="(detalle, indice) in detalles" :key="detalle.presentacion_id">
            <article class="rounded-xl border border-app-border bg-app-surface-alt/30 p-3">

                {{-- Imagen, nombre y SKU --}}
                <div class="flex min-w-0 items-center gap-3">

                    <span class="relative block h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-app-surface-alt">

                        <template x-if="detalle.imagen">
                            <img :src="detalle.imagen" :alt="detalle.nombre" class="h-full w-full object-cover">
                        </template>

                        <template x-if="!detalle.imagen">
                            <span class="flex h-full w-full items-center justify-center">
                                <x-lucide-image class="h-6 w-6 text-slate-400" />
                            </span>
                        </template>

                        <template x-if="detalle.tipo_item === 'PRODUCTO' && detalle.cantidad_actual !== null">
                            <span
                                class="absolute bottom-0 right-0 rounded-tl-md bg-slate-900/60 px-1 py-0.5 text-[10px] font-medium leading-none text-white/90"
                                title="Existencias actuales" x-text="detalle.cantidad_actual">
                            </span>
                        </template>

                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[15px] font-medium text-app-text-primary"
                            x-text="detalle.marca ? `${detalle.nombre} - ${detalle.marca}` : detalle.nombre">
                        </p>

                        <p class="mt-1 truncate text-xs text-app-text-secondary">
                            SKU: <span x-text="detalle.sku"></span>
                        </p>
                    </div>

                </div>

                <div class="my-3 space-y-3 border-t border-app-border pt-3">

                    {{-- Cantidad --}}
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-app-text-secondary">Cantidad</span>

                        <div class="inline-flex shrink-0 items-center gap-2">

                            <button type="button" @click="ajustarCantidad(detalle, -1)"
                                :disabled="Number(detalle.cantidad) <= 1"
                                :aria-label="`Disminuir cantidad de ${detalle.nombre}`"
                                class="flex h-9 w-9 items-center justify-center rounded-md border border-app-border-strong text-app-text-secondary shadow-sm transition hover:bg-app-surface-hover disabled:cursor-not-allowed disabled:opacity-40">
                                <x-lucide-minus class="h-4 w-4" />
                            </button>

                            <input type="number" min="1" step="1"
                                :max="detalle.tipo_item === 'PRODUCTO' ? cantidadDisponible(detalle) : null"
                                @change="actualizarDescuentoAutomatico(detalle, true)"
                                x-model.number="detalle.cantidad"
                                class="h-9 w-16 rounded-md border border-app-border-strong bg-app-background p-2 text-center text-[15px] shadow-sm [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none focus:border-app-primary-500 focus:ring-1 focus:ring-app-primary-500">

                            <button type="button" @click="ajustarCantidad(detalle, 1)"
                                :disabled="detalle.tipo_item === 'PRODUCTO' && Number(detalle.cantidad) >= cantidadDisponible(
                                    detalle)"
                                :aria-label="`Aumentar cantidad de ${detalle.nombre}`"
                                class="flex h-9 w-9 items-center justify-center rounded-md border border-app-border-strong text-app-text-secondary shadow-sm transition hover:bg-app-surface-hover disabled:cursor-not-allowed disabled:opacity-40">
                                <x-lucide-plus class="h-4 w-4" />
                            </button>

                        </div>
                    </div>

                    {{-- Precio aplicado --}}
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-app-text-secondary">Precio aplicado</span>

                        <div class="flex min-w-0 items-center gap-2">

                            <span class="whitespace-nowrap text-sm font-medium text-app-text-primary"
                                x-text="'Q ' + precioActual(detalle, detalle.tipo_precio, detalle.cantidad).toFixed(2)">
                            </span>

                            <select x-model="detalle.tipo_precio" @change="actualizarDescuentoAutomatico(detalle)"
                                class="w-24 rounded-md border border-app-border bg-app-background px-2 py-1.5 text-sm focus:border-app-primary-500 focus:ring-app-primary-500">

                                <option value="unitario">Unitario</option>

                                <template
                                    x-if="detalle.precio_docena_actual !== null && Number(detalle.cantidad) * Number(detalle.factor || 1) >= 12">
                                    <option value="docena">+ Docena</option>
                                </template>

                                <template
                                    x-if="detalle.precio_ciento_actual !== null && Number(detalle.cantidad) * Number(detalle.factor || 1) >= 100">
                                    <option value="ciento">+ Ciento</option>
                                </template>

                            </select>
                        </div>
                    </div>

                    {{-- Descuento --}}
                    <div class="flex items-center justify-between gap-3">
                        <label :for="`descuento-movil-${indice}`" class="text-sm text-app-text-secondary">
                            Descuento
                        </label>

                        <div class="inline-flex w-28 shrink-0 items-center gap-1 text-sm">
                            <span class="text-app-text-muted">Q</span>

                            <input type="text" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                maxlength="15" :id="`descuento-movil-${indice}`" x-model="detalle.descuento"
                                class="w-full min-w-0 rounded-md border border-app-border-strong bg-app-background p-2 text-center focus:border-app-primary-500 focus:ring-app-primary-500">
                        </div>
                    </div>

                </div>

                {{-- Subtotal y acción --}}
                <div class="flex items-center justify-between gap-3 border-t border-app-border pt-3">

                    <div>
                        <p class="text-xs text-app-text-secondary">Subtotal</p>
                        <p class="text-base font-semibold text-app-text-primary"
                            x-text="'Q ' + subtotalDetalle(detalle).toFixed(2)">
                        </p>
                    </div>

                    <button type="button" @click="quitarDetalle(indice)" :aria-label="`Eliminar ${detalle.nombre}`"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-red-600 transition hover:bg-red-50 hover:text-red-800 dark:hover:bg-red-950/40">
                        <x-lucide-x class="h-5 w-5" />
                    </button>

                </div>

            </article>
        </template>

        {{-- Carrito vacío --}}
        <div x-show="detalles.length === 0" class="px-4 py-10 text-center text-sm text-app-text-secondary">
            Busca y agrega artículos para comenzar la venta.
        </div>

    </div>



</section>

<div class="flex flex-col gap-5 text-base p-4 sm:p-6">
    <dl class="w-full space-y-2 sm:ml-auto sm:max-w-sm">
        <div class="flex justify-between gap-6 text-app-secondary">
            <dt>Total del importe</dt>
            <dd class="font-medium text-app-primary" x-text="'Q ' + totalImporte().toFixed(2)"></dd>
        </div>
        <div class="flex justify-between gap-6 text-app-secondary">
            <dt>Total del descuento</dt>
            <dd class="font-medium text-app-primary" x-text="'Q ' + totalDescuento().toFixed(2)"></dd>
        </div>
        <div
            class="flex text-xl justify-between gap-6 border-t border-app-border pt-2 text-base font-semibold text-app-primary">
            <dt>Total</dt>
            <dd x-text="'Q ' + totalVenta().toFixed(2)"></dd>
        </div>
    </dl>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <button type="button" @click="limpiarCarrito()" :disabled="detalles.length === 0"
            class="rounded-lg border border-red-200/70 bg-red-50/70 px-4 py-2.5 text-[15px] font-semibold text-red-700 transition hover:bg-red-100 dark:border-red-800/50 dark:bg-red-950/30 dark:text-red-500 dark:hover:bg-red-900/50 disabled:cursor-not-allowed disabled:opacity-50">
            Limpiar carrito
        </button>
        <button type="button" @click="abrirModalPago()" :disabled="!formularioValido()"
            class="rounded-md bg-green-700 px-5 py-2.5 text-[15px] font-semibold text-white shadow-sm transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-50">
            Registrar venta
        </button>
    </div>
</div>
