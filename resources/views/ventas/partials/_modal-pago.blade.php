<template x-teleport="body">
    <div x-cloak x-show="modalPagoAbierto" x-transition.opacity @click.self="cerrarModalPago()"
        @keydown.escape.window="if (modalPagoAbierto) cerrarModalPago()" @keydown.enter.prevent="enviarConEnter()"
        class="fixed inset-0 z-100 flex items-start justify-center
            overflow-y-auto overflow-x-hidden bg-black/40
            p-2 sm:items-center sm:p-4"
        role="dialog" aria-modal="true" aria-labelledby="titulo-modal-pago">

        <div x-show="modalPagoAbierto" x-transition
            class="my-0 flex max-h-[calc(100dvh-1rem)] w-full
                min-w-0 max-w-3xl flex-col overflow-hidden
                rounded-xl border border-app-border
                bg-app-surface p-4 shadow-xl
                sm:my-auto sm:max-h-[calc(100dvh-2rem)] sm:p-6"
            @click.stop>

            {{-- Encabezado --}}
            <div
                class="mb-4 flex shrink-0 items-start justify-between
                gap-3 border-b border-app-border pb-3">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
                        <h2 id="titulo-modal-pago" class="text-2xl font-bold text-app-primary-700">
                            Registrar venta
                        </h2>

                        <p class="text-lg font-semibold text-app-text-secondary sm:text-xl">
                            Total:
                            <strong class="text-xl font-bold text-app-text-primary"
                                x-text="'Q ' + totalVenta().toFixed(2)">
                            </strong>
                        </p>
                    </div>
                </div>

                <button type="button" @click="cerrarModalPago()" aria-label="Cerrar"
                    class="shrink-0 rounded-md p-1 text-app-text-muted transition
                        hover:bg-app-surface-hover hover:text-app-text-primary">
                    <x-lucide-x class="h-5 w-5" />
                </button>
            </div>

            {{-- Contenido --}}
            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto">

                <p x-show="cargandoMetodosPago" class="text-base text-app-text-muted">
                    Cargando métodos de pago...
                </p>

                <p x-show="errorPago" x-text="errorPago" role="alert"
                    class="rounded-md border border-red-200 bg-red-50 p-3 text-base text-red-700">
                </p>

                <fieldset class="space-y-3" :disabled="cargandoMetodosPago">

                    <legend class="mb-2 text-base font-semibold text-app-text-primary">
                        Métodos de pago
                    </legend>

                    <template x-for="(metodo, indice) in metodosPago" :key="metodo.id">

                        <div class="grid min-w-0 grid-cols-1 gap-3 rounded-lg border p-3
                            sm:grid-cols-[minmax(100px,max-content)_minmax(0,1fr)]
                            sm:items-center sm:gap-x-2"
                            :class="metodo.activo ?
                                'border-app-primary-500/40 bg-app-primary-500/10' :
                                'border-app-border bg-app-surface'">

                            {{-- Nombre del método --}}
                            <button type="button"
                                @click="metodo.activo ? metodo.activo = false : activarMetodo(metodo)"
                                :aria-pressed="metodo.activo"
                                class="flex w-fit items-center gap-2 whitespace-nowrap rounded
                                    text-left text-base font-semibold text-app-text-primary
                                    focus:border-app-border-strong focus:outline-none
                                    focus:ring-1 focus:ring-app-border-strong">

                                <span x-text="metodo.nombre"></span>
                            </button>

                            <input type="hidden" form="formulario-venta"
                                :name="metodo.activo ? `pagos[${indice}][metodo_pago_id]` : null"
                                :value="metodo.id">

                            {{-- Campos --}}
                            <div class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2">

                                {{-- Monto --}}
                                <template x-if="!metodo.permite_montos">
                                    <div class="min-w-0">

                                        <label class="mb-1 block text-sm font-medium text-app-text-secondary"
                                            :for="`monto-pago-${indice}`">
                                            Monto
                                        </label>

                                        <input :id="`monto-pago-${indice}`" type="text" inputmode="decimal"
                                            pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15" data-payment-amount="true"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                            :value="metodo.monto" @focus="activarMetodo(metodo)"
                                            @input="actualizarImporte(metodo, 'monto', $event)"
                                            class="h-10 block w-full rounded-md border border-app-border-strong
                                                bg-app-background px-3 text-right text-base
                                                text-app-text-primary shadow-sm
                                                focus:border-app-primary-500 focus:outline-none
                                                focus:ring-1 focus:ring-app-primary-500">
                                    </div>
                                </template>

                                {{-- Efectivo recibido --}}
                                <template x-if="metodo.permite_montos">
                                    <div class="min-w-0">

                                        <label class="mb-1 block text-sm font-medium text-app-text-secondary"
                                            :for="`recibido-${indice}`">
                                            Monto recibido
                                        </label>

                                        <input :id="`recibido-${indice}`" type="text" inputmode="decimal"
                                            pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15" data-cash-received="true"
                                            x-init="$nextTick(() => $el.focus({ preventScroll: true }))" form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto_recibido]` : null"
                                            :value="metodo.monto_recibido" @focus="activarMetodo(metodo)"
                                            @input="actualizarImporte(metodo, 'monto_recibido', $event)"
                                            class="h-10 block w-full rounded-md border border-app-border-strong
                                                bg-app-background px-3 text-right text-base font-medium
                                                text-app-text-primary shadow-sm transition-colors
                                                focus:border-app-primary-500 focus:outline-none
                                                focus:ring-1 focus:ring-app-primary-500">

                                        <input type="hidden" form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                            :value="montoAplicado(metodo).toFixed(2)">
                                    </div>
                                </template>

                                {{-- Cambio / faltante --}}
                                <template x-if="metodo.permite_montos">
                                    <div class="min-w-0" aria-live="polite">

                                        <span class="mb-1 block text-sm font-medium text-app-text-secondary">
                                            Cambio / faltante
                                        </span>

                                        <div class="flex h-10 items-center justify-end rounded-md
                                            border border-app-border px-3
                                            text-right text-base font-medium"
                                            :class="diferenciaEfectivo(metodo) < 0 ?
                                                'text-red-500' :
                                                diferenciaEfectivo(metodo) > 0 ?
                                                'text-green-500' :
                                                'text-app-primary-600'"
                                            x-text="diferenciaEfectivo(metodo) < 0
                                            ? 'Faltante Q ' + Math.abs(diferenciaEfectivo(metodo)).toFixed(2)
                                            : diferenciaEfectivo(metodo) > 0
                                            ? 'Cambio Q ' + diferenciaEfectivo(metodo).toFixed(2)
                                            : 'Completo'">
                                        </div>
                                    </div>
                                </template>

                                {{-- Referencia --}}
                                <template x-if="!metodo.permite_montos">
                                    <div class="min-w-0">

                                        <label class="mb-1 block text-sm font-medium text-app-text-secondary"
                                            :for="`referencia-${indice}`">
                                            Referencia (opcional)
                                        </label>

                                        <input :id="`referencia-${indice}`" type="text" maxlength="100"
                                            form="formulario-venta"
                                            :name="metodo.activo ? `pagos[${indice}][referencia]` : null"
                                            x-model="metodo.referencia" @focus="activarMetodo(metodo)"
                                            @input="activarMetodo(metodo)"
                                            class="h-10 block w-full rounded-md border border-app-border-strong
                                                bg-app-background px-3 text-base text-app-text-primary
                                                shadow-sm focus:border-app-primary-500 focus:outline-none
                                                focus:ring-1 focus:ring-app-primary-500">
                                    </div>
                                </template>

                            </div>
                        </div>
                    </template>

                    <p x-show="!cargandoMetodosPago && metodosPago.length === 0" class="text-base text-app-text-muted">
                        No hay métodos de pago activos. Puedes registrar la venta al crédito.
                    </p>

                </fieldset>

                {{-- Venta al crédito --}}
                <div class="space-y-2">

                    <label class="flex cursor-pointer items-center gap-2 text-sm text-app-text-secondary">
                        <input type="checkbox" x-model="ventaCredito" data-credit-option="true"
                            class="h-4 w-4 rounded border-app-border-strong
                                text-app-primary-600 focus:ring-app-primary-500">

                        <span>Venta al crédito (permitir saldo pendiente)</span>
                    </label>

                    <input type="hidden" form="formulario-venta" name="venta_credito" :value="ventaCredito ? 1 : 0">

                    <p x-show="ventaCredito && totalPagadoModal() < totalVenta()"
                        class="rounded-md border border-amber-300/50 bg-amber-50/50
                            px-3 py-2 text-base text-amber-700 dark:bg-amber-950/20">
                        Saldo pendiente: Q
                        <span x-text="Math.max(0, totalVenta() - totalPagadoModal()).toFixed(2)"></span>
                    </p>

                </div>
            </div>

            {{-- Acciones --}}
            <div
                class="mt-4 flex shrink-0 flex-col-reverse gap-3
                border-t border-app-border pt-4 sm:flex-row sm:justify-end">

                <button type="button" @click="cerrarModalPago()"
                    class="h-10 rounded-md border border-app-border-strong px-4
                        text-base font-medium text-app-text-secondary transition
                        hover:bg-app-surface-hover">
                    Cancelar
                </button>

                <button type="submit" form="formulario-venta"
                    :disabled="cargandoMetodosPago || !!errorPago || !pagosValidos()"
                    class="h-10 rounded-md bg-app-primary-700 px-4
                        text-base font-medium text-white shadow-sm transition
                        hover:bg-app-primary-800 disabled:cursor-not-allowed disabled:opacity-50">
                    Confirmar venta
                </button>

            </div>
        </div>
    </div>
</template>
