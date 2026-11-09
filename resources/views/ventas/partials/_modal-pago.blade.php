<template x-teleport="body">
    <div x-cloak x-show="modalPagoAbierto" x-transition.opacity
        @click.self="cerrarModalPago()" @keydown.escape.window="if (modalPagoAbierto) cerrarModalPago()"
        @keydown.enter.prevent="enviarConEnter()"
        class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-black/40 p-4"
        role="dialog" aria-modal="true" aria-labelledby="titulo-modal-pago">
        <div x-show="modalPagoAbierto" x-transition
            class="my-auto w-full max-w-3xl overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-xl"
            @click.stop>
            <div class="mb-5 flex items-start justify-between border-b border-gray-200 pb-3">
                <div>
                    <h2 id="titulo-modal-pago" class="text-lg font-semibold text-indigo-600">Registrar venta</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Total: <strong class="text-gray-800" x-text="'Q ' + totalVenta().toFixed(2)"></strong>
                    </p>
                </div>
                <button type="button" @click="cerrarModalPago()" aria-label="Cerrar"
                    class="rounded p-1 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800">
                    <x-lucide-x class="h-5 w-5" />
                </button>
            </div>

            <div class="max-h-[70vh] space-y-4 overflow-y-auto">
                <p x-show="cargandoMetodosPago" class="text-sm text-slate-500">Cargando métodos de pago...</p>
                <p x-show="errorPago" x-text="errorPago" role="alert"
                    class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>

                <fieldset class="space-y-3" :disabled="cargandoMetodosPago">
                    <legend class="mb-2 text-sm font-semibold text-gray-700">Métodos de pago</legend>
                    <template x-for="(metodo, indice) in metodosPago" :key="metodo.id">
                        <div class="grid grid-cols-1 gap-3 rounded-md border p-3 sm:grid-cols-4"
                            :class="metodo.activo ? 'border-indigo-300 bg-indigo-50/40' : 'border-gray-200 bg-white'">
                            <button type="button"
                                @click="metodo.activo ? metodo.activo = false : activarMetodo(metodo)"
                                :aria-pressed="metodo.activo"
                                class="flex items-center gap-2 rounded text-left text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:col-span-1">
                                <span x-text="metodo.nombre"></span>
                                <span x-show="metodo.activo" class="text-xs font-normal text-indigo-700">Seleccionado</span>
                            </button>

                            <input type="hidden" form="formulario-venta"
                                :name="metodo.activo ? `pagos[${indice}][metodo_pago_id]` : null"
                                :value="metodo.id">

                            <div class="grid grid-cols-1 gap-3 sm:col-span-3 sm:grid-cols-2">
                                <template x-if="!metodo.permite_montos">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600"
                                            :for="`monto-pago-${indice}`">Monto</label>
                                        <input :id="`monto-pago-${indice}`" type="text" inputmode="decimal"
                                            pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                            data-payment-amount="true"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                            :value="metodo.monto"
                                            @focus="activarMetodo(metodo)"
                                            @input="actualizarImporte(metodo, 'monto', $event)"
                                            class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </template>

                                <template x-if="metodo.permite_montos">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600"
                                            :for="`recibido-${indice}`">Monto recibido</label>
                                        <input :id="`recibido-${indice}`" type="text" inputmode="decimal"
                                            pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                            data-cash-received="true"
                                            x-init="$nextTick(() => $el.focus({ preventScroll: true }))"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto_recibido]` : null"
                                            :value="metodo.monto_recibido"
                                            @focus="activarMetodo(metodo)"
                                            @input="actualizarImporte(metodo, 'monto_recibido', $event)"
                                            class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <input type="hidden" form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                            :value="montoAplicado(metodo).toFixed(2)">
                                    </div>
                                </template>

                                <template x-if="metodo.permite_montos">
                                    <div aria-live="polite">
                                        <span class="mb-1 block text-xs font-medium text-slate-600">Cambio / faltante</span>
                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-right text-sm font-medium"
                                            :class="diferenciaEfectivo(metodo) < 0 ? 'text-red-700' : 'text-green-700'"
                                            x-text="diferenciaEfectivo(metodo) < 0
                                                ? 'Falta Q ' + Math.abs(diferenciaEfectivo(metodo)).toFixed(2)
                                                : 'Devolver Q ' + diferenciaEfectivo(metodo).toFixed(2)">
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!metodo.permite_montos">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600"
                                            :for="`referencia-${indice}`">Referencia (opcional)</label>
                                        <input :id="`referencia-${indice}`" type="text" maxlength="100"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][referencia]` : null"
                                            x-model="metodo.referencia"
                                            @focus="activarMetodo(metodo)"
                                            @input="activarMetodo(metodo)"
                                            class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <p x-show="!cargandoMetodosPago && metodosPago.length === 0" class="text-sm text-slate-500">
                        No hay métodos de pago activos. Puedes registrar la venta al crédito.
                    </p>
                </fieldset>

                <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-500">
                    <input type="checkbox" x-model="ventaCredito" data-credit-option="true"
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Venta al crédito (permitir saldo pendiente)</span>
                </label>

                <input type="hidden" form="formulario-venta" name="venta_credito" :value="ventaCredito ? 1 : 0">

                <p x-show="ventaCredito && totalPagadoModal() < totalVenta()" class="text-sm text-amber-700">
                    Saldo pendiente: Q <span x-text="Math.max(0, totalVenta() - totalPagadoModal()).toFixed(2)"></span>
                </p>
                <p x-show="!cargandoMetodosPago && !errorPago && !pagosValidos()" x-cloak
                    class="text-sm text-red-600" role="alert">
                    Registra el total de la venta o activa «Venta al crédito». El monto recibido debe cubrir su monto aplicado.
                </p>
            </div>

            <div class="mt-4 flex flex-col-reverse gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                <button type="button" @click="cerrarModalPago()"
                    class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" form="formulario-venta"
                    :disabled="cargandoMetodosPago || !!errorPago || !pagosValidos()"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Confirmar venta
                </button>
            </div>
        </div>
    </div>
</template>
