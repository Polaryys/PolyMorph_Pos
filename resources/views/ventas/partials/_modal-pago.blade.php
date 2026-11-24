<template x-teleport="body">
    <div x-cloak x-show="modalPagoAbierto" x-transition.opacity
        @click.self="cerrarModalPago()" @keydown.escape.window="if (modalPagoAbierto) cerrarModalPago()"
        @keydown.enter.prevent="enviarConEnter()"
        class="fixed inset-0 z-100 flex items-center justify-center overflow-y-auto bg-black/40 p-4"
        role="dialog" aria-modal="true" aria-labelledby="titulo-modal-pago">
        <div x-show="modalPagoAbierto" x-transition
            class="my-auto w-full max-w-3xl overflow-hidden rounded-lg border border-app-border bg-app-surface p-6 shadow-xl"
            @click.stop>
            <div class="mb-5 flex items-start justify-between border-b border-app-border pb-3">
                <div>
                    <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
                        <h2 id="titulo-modal-pago" class="text-2xl font-bold text-app-primary-700 sm:text-3xl">
                            Registrar venta
                        </h2>
                        <p class="text-lg font-semibold text-app-text-secondary sm:text-xl">
                            Total:
                            <strong class="text-xl font-bold text-app-text-primary sm:text-2xl"
                                x-text="'Q ' + totalVenta().toFixed(2)"></strong>
                        </p>
                    </div>
                </div>
                <button type="button" @click="cerrarModalPago()" aria-label="Cerrar"
                    class="rounded p-1 text-app-text-muted transition hover:bg-app-surface-hover hover:text-app-text-primary">
                    <x-lucide-x class="h-5 w-5" />
                </button>
            </div>

            <div class="max-h-[70vh] space-y-4 overflow-y-auto">
                <p x-show="cargandoMetodosPago" class="text-base text-app-text-muted">Cargando métodos de pago...</p>
                <p x-show="errorPago" x-text="errorPago" role="alert"
                    class="rounded-md border border-red-200 bg-red-50 p-3 text-base text-red-700"></p>

                <fieldset class="space-y-3" :disabled="cargandoMetodosPago">
                    <legend class="mb-2 text-base font-semibold text-app-text-primary">Métodos de pago</legend>
                    <template x-for="(metodo, indice) in metodosPago" :key="metodo.id">
                        <div class="grid grid-cols-1 gap-2 rounded-md border p-3 sm:grid-cols-[max-content_minmax(0,1fr)] sm:gap-x-4"
                            :class="metodo.activo ? 'border-app-primary-500/40 bg-app-primary-500/10' : 'border-app-border bg-app-surface'">
                            <button type="button"
                                @click="metodo.activo ? metodo.activo = false : activarMetodo(metodo)"
                                :aria-pressed="metodo.activo"
                                class="flex w-fit items-center gap-2 whitespace-nowrap rounded text-left text-base font-semibold text-app-text-primary focus:border-app-border-strong focus:outline-none focus:ring-1 focus:ring-app-border-strong sm:col-span-1">
                                <span x-text="metodo.nombre"></span>
                            </button>

                            <input type="hidden" form="formulario-venta"
                                :name="metodo.activo ? `pagos[${indice}][metodo_pago_id]` : null"
                                :value="metodo.id">

                            <div class="grid grid-cols-1 gap-2 sm:col-span-1 sm:grid-cols-2 sm:gap-2">
                                <template x-if="!metodo.permite_montos">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-app-text-secondary"
                                            :for="`monto-pago-${indice}`">Monto</label>
                                        <input :id="`monto-pago-${indice}`" type="text" inputmode="decimal"
                                            pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                            data-payment-amount="true"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                            :value="metodo.monto"
                                            @focus="activarMetodo(metodo)"
                                            @input="actualizarImporte(metodo, 'monto', $event)"
                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-right text-base text-app-text-primary shadow-sm focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                    </div>
                                </template>

                                <template x-if="metodo.permite_montos">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-app-text-secondary"
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
                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-right text-base font-medium text-app-text-primary shadow-sm transition-colors focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                        <input type="hidden" form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                            :value="montoAplicado(metodo).toFixed(2)">
                                    </div>
                                </template>

                                <template x-if="metodo.permite_montos">
                                    <div aria-live="polite">
                                        <span class="mb-1 block text-sm font-medium text-app-text-secondary">Cambio / faltante</span>
                                        <div class="rounded-md border border-app-border bg-app-surface-alt px-3 py-2 text-right text-base font-medium text-app-text-primary"
                                            :class="diferenciaEfectivo(metodo) < 0 ? 'text-red-700' : 'text-green-700'"
                                            x-text="diferenciaEfectivo(metodo) < 0
                                                ? 'Falta Q ' + Math.abs(diferenciaEfectivo(metodo)).toFixed(2)
                                                : 'Devolver Q ' + diferenciaEfectivo(metodo).toFixed(2)">
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!metodo.permite_montos">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-app-text-secondary"
                                            :for="`referencia-${indice}`">Referencia (opcional)</label>
                                        <input :id="`referencia-${indice}`" type="text" maxlength="100"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][referencia]` : null"
                                            x-model="metodo.referencia"
                                            @focus="activarMetodo(metodo)"
                                            @input="activarMetodo(metodo)"
                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-base text-app-text-primary shadow-sm focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <p x-show="!cargandoMetodosPago && metodosPago.length === 0" class="text-base text-app-text-muted">
                        No hay métodos de pago activos. Puedes registrar la venta al crédito.
                    </p>
                </fieldset>

                <label class="flex cursor-pointer items-center gap-2 text-sm text-app-text-secondary">
                    <input type="checkbox" x-model="ventaCredito" data-credit-option="true"
                        class="rounded border-app-border-strong text-app-primary-600 focus:ring-app-primary-500">
                    <span>Venta al crédito (permitir saldo pendiente)</span>
                </label>

                <input type="hidden" form="formulario-venta" name="venta_credito" :value="ventaCredito ? 1 : 0">

                <p x-show="ventaCredito && totalPagadoModal() < totalVenta()" class="text-base text-amber-700">
                    Saldo pendiente: Q <span x-text="Math.max(0, totalVenta() - totalPagadoModal()).toFixed(2)"></span>
                </p>
            </div>

            <div class="mt-4 flex flex-col-reverse gap-3 border-t border-app-border pt-4 sm:flex-row sm:justify-end">
                <button type="button" @click="cerrarModalPago()"
                    class="rounded-md border border-app-border-strong px-4 py-2 text-base font-medium text-app-text-secondary transition hover:bg-app-surface-hover">
                    Cancelar
                </button>
                <button type="submit" form="formulario-venta"
                    :disabled="cargandoMetodosPago || !!errorPago || !pagosValidos()"
                    class="rounded-md bg-app-primary-700 px-4 py-2 text-base font-medium text-white shadow-sm transition hover:bg-app-primary-800 disabled:cursor-not-allowed disabled:opacity-50">
                    Confirmar venta
                </button>
            </div>
        </div>
    </div>
</template>
