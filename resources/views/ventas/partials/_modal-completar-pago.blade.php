<div x-cloak x-show="modalPagoAbierto" x-transition.opacity
    @click.self="modalPagoAbierto = false" @keydown.escape.window="modalPagoAbierto = false"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4"
    role="dialog" aria-modal="true" aria-labelledby="titulo-modal-abono">
    <form action="{{ route('ventas.pagos.store', $venta) }}" method="POST"
        class="my-auto w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-2xl">
        @csrf
        <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
            <div>
                <h2 id="titulo-modal-abono" class="text-lg font-semibold text-slate-900">Completar pago</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Saldo pendiente: <strong class="text-slate-800"
                        x-text="'Q ' + saldoPendiente.toFixed(2)"></strong>
                </p>
            </div>
            <button type="button" @click="modalPagoAbierto = false" aria-label="Cerrar"
                class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                <x-lucide-x class="h-5 w-5" />
            </button>
        </div>

        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-5 py-5 sm:px-6">
            <p x-show="cargandoMetodosPago" class="text-sm text-slate-500">Cargando métodos de pago...</p>
            <p x-show="errorPago" x-text="errorPago" role="alert"
                class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>

            <fieldset class="space-y-3" :disabled="cargandoMetodosPago">
                <legend class="mb-2 text-sm font-semibold text-slate-800">Métodos de pago</legend>
                <template x-for="(metodo, indice) in metodosPago" :key="metodo.id">
                    <div class="rounded-lg border border-slate-200 p-3"
                        :class="metodo.activo ? 'border-indigo-300 bg-indigo-50/40' : 'bg-white'">
                        <button type="button"
                            @click="metodo.activo ? metodo.activo = false : activarMetodo(metodo)"
                            :aria-pressed="metodo.activo"
                            class="flex items-center gap-2 rounded font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span x-text="metodo.nombre"></span>
                            <span x-show="metodo.activo" class="text-xs font-normal text-indigo-700">Seleccionado</span>
                        </button>

                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input type="hidden" :name="metodo.activo ? `pagos[${indice}][metodo_pago_id]` : null"
                                :value="metodo.id">

                            <template x-if="!metodo.efectivo">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                        :for="`abono-${indice}`">Monto a abonar</label>
                                    <input :id="`abono-${indice}`" type="text" inputmode="decimal"
                                        pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                        :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                        :value="metodo.monto"
                                        @focus="activarMetodo(metodo)"
                                        @input="actualizarImporte(metodo, 'monto', $event)"
                                        class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </template>

                            <template x-if="metodo.efectivo">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                        :for="`abono-efectivo-${indice}`">Monto recibido</label>
                                    <input :id="`abono-efectivo-${indice}`" type="text" inputmode="decimal"
                                        pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                        :name="metodo.activo ? `pagos[${indice}][monto_recibido]` : null"
                                        :value="metodo.monto_recibido"
                                        @focus="activarMetodo(metodo)"
                                        @input="actualizarImporte(metodo, 'monto_recibido', $event)"
                                        class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="hidden" :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                        :value="montoAplicado(metodo).toFixed(2)">
                                </div>
                            </template>

                            <template x-if="metodo.efectivo">
                                <div aria-live="polite">
                                    <span class="mb-1 block text-xs font-medium text-slate-600">Cambio</span>
                                    <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-right text-sm"
                                        x-text="'Q ' + Math.max(0, Number(metodo.monto_recibido || 0) - montoAplicado(metodo)).toFixed(2)">
                                    </div>
                                </div>
                            </template>

                            <template x-if="!metodo.efectivo">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                        :for="`referencia-abono-${indice}`">Referencia (opcional)</label>
                                    <input :id="`referencia-abono-${indice}`" type="text" maxlength="100"
                                        :name="metodo.activo ? `pagos[${indice}][referencia]` : null"
                                        x-model="metodo.referencia"
                                        @focus="activarMetodo(metodo)"
                                        class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <p x-show="!cargandoMetodosPago && metodosPago.length === 0" class="text-sm text-slate-500">
                    No hay métodos de pago activos.
                </p>
            </fieldset>

            <p class="text-sm text-slate-600" aria-live="polite">
                Abono: Q <span x-text="metodosPago.filter(metodo => metodo.activo)
                    .reduce((total, metodo) => total + montoAplicado(metodo), 0).toFixed(2)"></span>
            </p>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
            <button type="button" @click="modalPagoAbierto = false"
                class="rounded-md border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Cancelar
            </button>
            <button type="submit" :disabled="cargandoMetodosPago || !!errorPago || !pagosValidos()"
                class="rounded-md bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-50">
                Registrar abono
            </button>
        </div>
    </form>
</div>
