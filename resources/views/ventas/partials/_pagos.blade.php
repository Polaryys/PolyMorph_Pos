<section class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Pagos</h2>
            <p class="text-sm text-slate-500">Puedes registrar uno o varios métodos; el saldo restante queda pendiente.</p>
        </div>
        <button type="button" @click="agregarPago()"
            class="self-start rounded-md border border-indigo-200 px-3 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50">
            Agregar pago
        </button>
    </div>

    <div class="space-y-4 p-4 sm:p-6">
        <template x-for="(pago, indice) in pagos" :key="indice">
            <div class="grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-3 sm:grid-cols-2 lg:grid-cols-5">
                <div class="space-y-1">
                    <x-input-label>Forma de pago</x-input-label>
                    <select :name="`pagos[${indice}][metodo_pago_id]`" x-model="pago.metodo_pago_id" required
                        class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Selecciona un método</option>
                        @foreach ($metodosPago as $metodo)
                            <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <x-input-label>Monto aplicado</x-input-label>
                    <input type="number" min="0.01" step="0.01" :max="totalVenta()"
                        :name="`pagos[${indice}][monto]`" x-model="pago.monto" required
                        class="w-full rounded-md border-slate-300 text-right text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <x-input-label>Monto recibido (opcional)</x-input-label>
                    <input type="number" min="0" step="0.01" :name="`pagos[${indice}][monto_recibido]`"
                        x-model="pago.monto_recibido"
                        class="w-full rounded-md border-slate-300 text-right text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <x-input-label>Referencia (opcional)</x-input-label>
                    <input type="text" maxlength="100" :name="`pagos[${indice}][referencia]`"
                        x-model="pago.referencia"
                        class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-end">
                    <button type="button" @click="pagos.splice(indice, 1)"
                        class="w-full rounded-md px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                        Quitar pago
                    </button>
                </div>
            </div>
        </template>

        <p x-show="pagos.length === 0" class="text-sm text-slate-500">
            No se han agregado pagos. La venta quedará pendiente de pago.
        </p>

        <div class="flex flex-col gap-2 border-t border-slate-200 pt-4 text-sm sm:ml-auto sm:max-w-xs">
            <div class="flex justify-between gap-4 text-slate-600">
                <span>Total aplicado</span>
                <strong class="text-slate-900" x-text="'Q ' + totalPagado().toFixed(2)"></strong>
            </div>
            <div class="flex justify-between gap-4 text-slate-600">
                <span>Saldo pendiente</span>
                <strong class="text-slate-900" x-text="'Q ' + saldoPendiente().toFixed(2)"></strong>
            </div>
        </div>
        <p x-show="!pagosValidos()" x-cloak class="text-sm text-red-600">
            Revisa los métodos y montos: el pago no puede exceder el total, y el monto recibido debe cubrir lo aplicado.
        </p>
    </div>
</section>
