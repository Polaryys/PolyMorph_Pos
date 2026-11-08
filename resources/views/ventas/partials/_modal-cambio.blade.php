@if ($venta->tipo_operacion === 'VENTA' && $venta->estado_venta === 'REGISTRADA' && $detallesDisponiblesCambio->isNotEmpty())
    <section x-data="{
        detallesDisponibles: @js($detallesDisponiblesCambio),
        devolucionesCambio: [],
        productosCambio: [],
        mostrarModalCambio: false,
        carritoCambioAbierto: false,
        buscarArticulo: '',
        resultadosCambio: [],
        cargandoBusquedaCambio: false,
        errorCambio: '',
        motivoCambio: '',
        versionBusquedaCambio: 0,
        modalPagoCambioAbierto: false,
        cargandoMetodosPagoCambio: false,
        errorPagoCambio: '',
        metodosPagoCambio: [],
        enviandoCambio: false,

        abrirSelectorCambio() {
            this.detallesDisponibles = this.detallesDisponibles.map(detalle => ({
                ...detalle,
                seleccionada: false,
                cantidad: '',
            }));
            this.errorCambio = '';
            this.mostrarModalCambio = true;
        },

        ajustarCantidadDevolucion(detalle, ajuste) {
            const cantidadActual = Number(detalle.cantidad) || 0;
            const cantidadNueva = cantidadActual === 0 && ajuste > 0
                ? Math.min(1, Number(detalle.cantidad_disponible))
                : Math.min(
                    Math.max(0, Math.round((cantidadActual + ajuste) * 100) / 100),
                    Number(detalle.cantidad_disponible)
                );

            detalle.cantidad = cantidadNueva > 0 ? String(cantidadNueva) : '';
            detalle.seleccionada = cantidadNueva > 0;
        },

        actualizarCantidadDevolucion(detalle, event) {
            const valor = event.target.value;
            const [entero, ...decimales] = valor.replace(/[^\d.]/g, '').split('.');
            const valorValidado = decimales.length
                ? `${entero}.${decimales.join('').slice(0, 2)}`
                : entero;

            detalle.cantidad = valorValidado;
            event.target.value = valorValidado;

            const cantidad = Number(valorValidado);
            if (cantidad > 0) {
                detalle.seleccionada = true;
                if (cantidad > Number(detalle.cantidad_disponible)) {
                    detalle.cantidad = String(detalle.cantidad_disponible);
                    event.target.value = detalle.cantidad;
                }
            }
        },

        agregarDevoluciones() {
            const seleccionados = this.detallesDisponibles.filter(detalle => detalle.seleccionada);

            if (seleccionados.length === 0) {
                this.errorCambio = 'Selecciona al menos un artículo y una cantidad para devolver.';
                return;
            }

            const cantidadInvalida = seleccionados.some(detalle => {
                const cantidad = Number(detalle.cantidad);
                return !Number.isFinite(cantidad)
                    || cantidad <= 0
                    || cantidad > Number(detalle.cantidad_disponible)
                    || (detalle.tipo_item === 'PRODUCTO'
                        && !Number.isInteger(cantidad * Number(detalle.factor || 1)));
            });

            if (cantidadInvalida) {
                this.errorCambio = 'Verifica que cada cantidad sea válida y respete la presentación del artículo.';
                return;
            }

            this.devolucionesCambio = seleccionados.map(detalle => ({
                ...detalle,
                reingresa_inventario: detalle.reingresa_inventario,
            }));
            this.carritoCambioAbierto = true;
            this.mostrarModalCambio = false;
            this.errorCambio = '';
        },

        async buscarArticulosCambio() {
            const version = ++this.versionBusquedaCambio;
            const termino = this.buscarArticulo.trim();

            if (!termino) {
                this.resultadosCambio = [];
                this.cargandoBusquedaCambio = false;
                return;
            }

            this.cargandoBusquedaCambio = true;
            this.errorCambio = '';

            try {
                const url = new URL('{{ route('ventas.buscar-productos') }}', window.location.origin);
                url.searchParams.set('buscar', termino);
                const respuesta = await fetch(url, { headers: { Accept: 'application/json' } });

                if (!respuesta.ok) throw new Error('No se pudieron buscar artículos.');

                const resultados = await respuesta.json();
                if (version === this.versionBusquedaCambio) this.resultadosCambio = resultados;
            } catch (error) {
                if (version === this.versionBusquedaCambio) {
                    this.resultadosCambio = [];
                    this.errorCambio = error.message;
                }
            } finally {
                if (version === this.versionBusquedaCambio) this.cargandoBusquedaCambio = false;
            }
        },

        agregarProductoCambio(producto) {
            if (this.productosCambio.some(detalle => detalle.presentacion_id === producto.presentacion_id)) {
                this.errorCambio = 'Este artículo ya está agregado al cambio.';
                return;
            }

            this.productosCambio.push({
                ...producto,
                cantidad: 1,
                tipo_precio: 'unitario',
            });
            this.versionBusquedaCambio++;
            this.buscarArticulo = '';
            this.resultadosCambio = [];
            this.errorCambio = '';
        },

        subtotalDevolucion(detalle) {
            const cantidad = Number(detalle.cantidad) || 0;
            if (Math.abs(cantidad - Number(detalle.cantidad_disponible)) < 0.00001) {
                return Number(detalle.subtotal_disponible);
            }

            return Math.round(Number(detalle.subtotal_original) * cantidad / Number(detalle.cantidad_original) * 100) / 100;
        },

        subtotalProductoCambio(detalle) {
            const cantidad = Number(detalle.cantidad) || 0;
            const descuento = this.descuentoProductoCambio(detalle);
            return Math.round((cantidad * Number(detalle.precio_unitario_actual || 0) - descuento) * 100) / 100;
        },

        precioAplicadoCambio(detalle) {
            const cantidadBase = Number(detalle.cantidad || 0) * Number(detalle.factor || 1);
            if (detalle.tipo_precio === 'docena' && cantidadBase >= 12 && detalle.precio_docena_actual !== null) {
                return Number(detalle.precio_docena_actual);
            }
            if (detalle.tipo_precio === 'ciento' && cantidadBase >= 100 && detalle.precio_ciento_actual !== null) {
                return Number(detalle.precio_ciento_actual);
            }

            return Number(detalle.precio_unitario_actual || 0);
        },

        descuentoProductoCambio(detalle) {
            const cantidad = Number(detalle.cantidad) || 0;
            return Math.max(0, Math.round(
                (cantidad * (Number(detalle.precio_unitario_actual || 0) - this.precioAplicadoCambio(detalle))
                    + Number.EPSILON) * 100
            ) / 100);
        },

        actualizarPrecioProductoCambio(detalle) {
            const cantidadBase = Number(detalle.cantidad || 0) * Number(detalle.factor || 1);
            if (
                (detalle.tipo_precio === 'docena' && cantidadBase < 12)
                || (detalle.tipo_precio === 'ciento' && cantidadBase < 100)
            ) {
                detalle.tipo_precio = 'unitario';
            }
        },

        totalCambio() {
            const devuelto = this.devolucionesCambio.reduce((total, detalle) => total + this.subtotalDevolucion(detalle), 0);
            const entregado = this.productosCambio.reduce((total, detalle) => total + this.subtotalProductoCambio(detalle), 0);
            return Math.round((entregado - devuelto) * 100) / 100;
        },

        async abrirModalPagoCambio() {
            this.modalPagoCambioAbierto = true;
            this.errorPagoCambio = '';
            this.metodosPagoCambio = [];

            if (this.totalCambio() === 0) return;

            this.cargandoMetodosPagoCambio = true;

            try {
                const respuesta = await fetch('{{ route('ventas.metodos-pago') }}', {
                    headers: { Accept: 'application/json' },
                });
                if (!respuesta.ok) throw new Error('No se pudieron cargar los métodos de pago.');

                const metodos = await respuesta.json();
                this.metodosPagoCambio = metodos.map(metodo => ({
                    ...metodo,
                    permite_montos: metodo.tipo_captura === 'PERMITIR MONTOS',
                    activo: false,
                    monto: '',
                    monto_recibido: '',
                    referencia: '',
                }));
            } catch (error) {
                this.errorPagoCambio = error.message;
            } finally {
                this.cargandoMetodosPagoCambio = false;
            }
        },

        activarMetodoPagoCambio(metodo) {
            if (metodo.activo) return;

            metodo.activo = true;
            const montoDisponible = this.montoDisponiblePagoCambio(metodo);

            if (metodo.permite_montos && this.totalCambio() > 0) {
                metodo.monto_recibido = montoDisponible.toFixed(2);
            } else {
                metodo.monto = montoDisponible.toFixed(2);
            }
        },

        montoDisponiblePagoCambio(metodoActual) {
            let aplicado = 0;
            const totalDisponible = Math.abs(this.totalCambio());
            const otrosPagos = this.metodosPagoCambio.filter(
                metodo => metodo !== metodoActual && metodo.activo
            );

            for (const metodo of otrosPagos) {
                const recibeEfectivo = metodo.permite_montos && this.totalCambio() > 0;
                const montoIngresado = recibeEfectivo
                    ? Number(metodo.monto_recibido) || 0
                    : Number(metodo.monto) || 0;
                aplicado += Math.min(montoIngresado, Math.max(0, totalDisponible - aplicado));
            }

            return Math.max(0, Math.round((totalDisponible - aplicado) * 100) / 100);
        },

        montoAplicadoPagoCambio(metodo) {
            const total = this.totalCambio();
            let aplicado = 0;

            for (const pago of this.metodosPagoCambio.filter(item => item.activo)) {
                const recibeEfectivo = pago.permite_montos && total > 0;
                const montoIngresado = recibeEfectivo
                    ? Number(pago.monto_recibido) || 0
                    : Number(pago.monto) || 0;
                const montoAplicado = Math.min(
                    montoIngresado,
                    Math.max(0, Math.abs(total) - aplicado)
                );

                if (pago === metodo) {
                    return total < 0 ? -montoAplicado : montoAplicado;
                }

                aplicado += montoAplicado;
            }

            return 0;
        },

        actualizarImportePagoCambio(metodo, campo, event) {
            const valor = event.target.value;
            const [entero, ...decimales] = valor.replace(/[^\d.]/g, '').split('.');
            const valorValidado = decimales.length
                ? `${entero}.${decimales.join('').slice(0, 2)}`
                : entero;

            metodo[campo] = valorValidado;
            event.target.value = valorValidado;
            metodo.activo = true;
        },

        pagosCambioValidos() {
            const total = this.totalCambio();
            const pagos = this.metodosPagoCambio.filter(metodo => metodo.activo);

            if (total === 0) return pagos.length === 0;

            const sumaAplicada = pagos.reduce(
                (suma, metodo) => suma + Math.abs(this.montoAplicadoPagoCambio(metodo)),
                0
            );

            return pagos.length > 0
                && pagos.every(metodo => {
                    const recibeEfectivo = metodo.permite_montos && total > 0;
                    const montoIngresado = recibeEfectivo ? metodo.monto_recibido : metodo.monto;

                    return /^\d+(\.\d{1,2})?$/.test(String(montoIngresado || ''))
                        && Math.abs(this.montoAplicadoPagoCambio(metodo)) > 0
                        && (recibeEfectivo
                            || Number(montoIngresado) === Math.abs(this.montoAplicadoPagoCambio(metodo)))
                        && (!recibeEfectivo
                            || Number(metodo.monto_recibido) >= Math.abs(this.montoAplicadoPagoCambio(metodo)));
                })
                && Math.round(sumaAplicada * 100) === Math.round(Math.abs(total) * 100);
        },

        formularioCambioValido() {
            return this.devolucionesCambio.length > 0
                && this.motivoCambio.trim().length > 0
                && this.devolucionesCambio.every(detalle => {
                    const cantidad = Number(detalle.cantidad);
                    return Number.isFinite(cantidad)
                        && cantidad > 0
                        && cantidad <= Number(detalle.cantidad_disponible)
                        && (detalle.tipo_item !== 'PRODUCTO'
                            || Number.isInteger(cantidad * Number(detalle.factor || 1)));
                })
                && this.productosCambio.every(detalle => {
                    const cantidad = Number(detalle.cantidad);
                    const cantidadBase = cantidad * Number(detalle.factor || 1);
                    const precioPorVolumenValido = detalle.tipo_precio === 'unitario'
                        || (detalle.tipo_precio === 'docena'
                            && cantidadBase >= 12
                            && detalle.precio_docena_actual !== null)
                        || (detalle.tipo_precio === 'ciento'
                            && cantidadBase >= 100
                            && detalle.precio_ciento_actual !== null);
                    return Number.isInteger(cantidad)
                        && cantidad > 0
                        && precioPorVolumenValido
                        && (detalle.tipo_item !== 'PRODUCTO' || Number.isInteger(cantidadBase));
                });
        },

        confirmarEnvioCambio(event) {
            if (
                this.enviandoCambio
                || !this.modalPagoCambioAbierto
                || this.cargandoMetodosPagoCambio
                || this.errorPagoCambio
                || !this.formularioCambioValido()
                || !this.pagosCambioValidos()
            ) {
                event.preventDefault();
                return;
            }

            this.enviandoCambio = true;
        },
    }" class="rounded border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="font-semibold text-slate-900">Cambios y devoluciones</h2>
                <p class="mt-1 text-sm text-slate-500">Registra una operación relacionada con esta venta.</p>
            </div>
            @if ($puedeCambiar)
                <button type="button" @click="abrirSelectorCambio()"
                    class="rounded-md bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
                    Cambiar
                </button>
            @else
                <span title="{{ $razonBloqueoCambio }}" class="inline-flex cursor-not-allowed">
                    <button type="button" disabled
                        class="pointer-events-none rounded-md bg-slate-400 px-4 py-2.5 text-sm font-semibold text-white">
                        Cambiar
                    </button>
                </span>
            @endif
        </div>

        <div x-cloak x-show="carritoCambioAbierto" x-transition class="mt-5 space-y-4 border-t border-slate-200 pt-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-semibold text-slate-900">Artículos de la operación</h3>
                <button type="button" @click="abrirSelectorCambio()"
                    class="text-sm font-medium text-indigo-700 hover:text-indigo-900">
                    Cambiar artículos devueltos
                </button>
            </div>

            <form x-ref="formularioCambio" action="{{ route('ventas.store') }}" method="POST"
                @submit="confirmarEnvioCambio($event)"
                class="space-y-4">
                @csrf
                <input type="hidden" name="tipo_operacion"
                    :value="productosCambio.length ? 'CAMBIO' : 'DEVOLUCION'">
                <input type="hidden" name="venta_referencia_id" value="{{ $venta->id }}">

                <template x-for="(detalle, indice) in devolucionesCambio" :key="`dev-${detalle.id}`">
                    <div class="hidden">
                        <input type="hidden" :name="`detalles[${indice}][tipo_movimiento]`" value="DEVOLUCION">
                        <input type="hidden" :name="`detalles[${indice}][detalle_referencia_id]`" :value="detalle.id">
                        <input type="hidden" :name="`detalles[${indice}][cantidad]`" :value="detalle.cantidad">
                        <input type="hidden" :name="`detalles[${indice}][reingresa_inventario]`"
                            :value="detalle.reingresa_inventario ? 1 : 0">
                    </div>
                </template>
                <template x-for="(detalle, indice) in productosCambio" :key="`prod-${detalle.presentacion_id}`">
                    <div class="hidden">
                        <input type="hidden"
                            :name="`detalles[${devolucionesCambio.length + indice}][tipo_movimiento]`"
                            value="ENTREGA_CAMBIO">
                        <input type="hidden"
                            :name="`detalles[${devolucionesCambio.length + indice}][presentacion_id]`"
                            :value="detalle.presentacion_id">
                        <input type="hidden"
                            :name="`detalles[${devolucionesCambio.length + indice}][cantidad]`"
                            :value="detalle.cantidad">
                        <input type="hidden"
                            :name="`detalles[${devolucionesCambio.length + indice}][tipo_precio]`"
                            :value="detalle.tipo_precio">
                        <input type="hidden"
                            :name="`detalles[${devolucionesCambio.length + indice}][descuento]`"
                            :value="descuentoProductoCambio(detalle).toFixed(2)">
                    </div>
                </template>

                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-2">Artículo</th>
                                <th class="px-3 py-2 text-right">Cantidad</th>
                                <th class="px-3 py-2 text-right">Precio aplicado</th>
                                <th class="px-3 py-2 text-right">Importe</th>
                                <th class="px-3 py-2">Inventario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="detalle in devolucionesCambio" :key="`fila-dev-${detalle.id}`">
                                <tr>
                                    <td class="px-3 py-2">
                                        <span class="font-medium text-slate-900" x-text="detalle.nombre"></span>
                                        <span class="block text-xs text-rose-700">Devolución</span>
                                    </td>
                                    <td class="px-3 py-2 text-right text-rose-700"
                                        x-text="'-' + Number(detalle.cantidad).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right text-rose-700"
                                        x-text="'Q -' + Number(detalle.precio_aplicado).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right font-semibold text-rose-700"
                                        x-text="'Q -' + subtotalDevolucion(detalle).toFixed(2)"></td>
                                    <td class="px-3 py-2">
                                        <label x-show="detalle.tipo_item === 'PRODUCTO'"
                                            class="inline-flex items-center gap-2 text-xs text-slate-600">
                                            <input type="checkbox" x-model="detalle.reingresa_inventario"
                                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            Reingresar
                                        </label>
                                        <span x-show="detalle.tipo_item !== 'PRODUCTO'" class="text-xs text-slate-400">
                                            No aplica
                                        </span>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="detalle in productosCambio" :key="`fila-prod-${detalle.presentacion_id}`">
                                <tr>
                                    <td class="px-3 py-2">
                                        <span class="font-medium text-slate-900" x-text="detalle.nombre"></span>
                                        <span class="block text-xs text-emerald-700">Entrega</span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <input type="number" min="1" step="1" x-model="detalle.cantidad"
                                            @input="actualizarPrecioProductoCambio(detalle)"
                                            class="w-24 rounded-md border-slate-300 text-right text-sm">
                                    </td>
                                    <td class="px-3 py-2 text-right"
                                        x-text="'Q ' + precioAplicadoCambio(detalle).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right font-semibold"
                                        x-text="'Q ' + subtotalProductoCambio(detalle).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right">
                                        <select x-model="detalle.tipo_precio" @change="actualizarPrecioProductoCambio(detalle)"
                                            class="mb-1 w-full rounded-md border-slate-300 text-xs">
                                            <option value="unitario">Unitario</option>
                                            <option value="docena" :disabled="Number(detalle.cantidad) * Number(detalle.factor) < 12 || detalle.precio_docena_actual === null">
                                                Docena
                                            </option>
                                            <option value="ciento" :disabled="Number(detalle.cantidad) * Number(detalle.factor) < 100 || detalle.precio_ciento_actual === null">
                                                Ciento
                                            </option>
                                        </select>
                                        <button type="button" @click="productosCambio = productosCambio.filter(item => item.presentacion_id !== detalle.presentacion_id)"
                                            class="text-xs font-medium text-rose-700 hover:text-rose-900">
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 sm:items-end">
                    <div>
                        <label for="buscar-articulo-cambio" class="mb-1 block text-sm font-medium text-slate-700">
                            Agregar artículos
                        </label>
                        <input id="buscar-articulo-cambio" type="search" x-model="buscarArticulo"
                            @input.debounce.300ms="buscarArticulosCambio()"
                            placeholder="Buscar por nombre o SKU"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <div x-show="cargandoBusquedaCambio" class="mt-2 text-xs text-slate-500">Buscando...</div>
                        <div x-show="resultadosCambio.length" class="mt-2 max-h-48 overflow-y-auto rounded-md border border-slate-200">
                            <template x-for="producto in resultadosCambio" :key="producto.presentacion_id">
                                <button type="button" @click="agregarProductoCambio(producto)"
                                    class="flex w-full items-center justify-between gap-3 border-b border-slate-100 px-3 py-2 text-left text-sm last:border-0 hover:bg-indigo-50">
                                    <span>
                                        <span class="block font-medium text-slate-900" x-text="producto.nombre"></span>
                                        <span class="text-xs text-slate-500" x-text="producto.sku || producto.tipo_item"></span>
                                    </span>
                                    <span class="whitespace-nowrap text-slate-700"
                                        x-text="'Q ' + Number(producto.precio_unitario_actual).toFixed(2)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label for="motivo-cambio" class="mb-1 block text-sm font-medium text-slate-700">
                            Motivo <span class="text-red-600">*</span>
                        </label>
                        <textarea id="motivo-cambio" name="motivo_operacion" x-model="motivoCambio"
                            maxlength="1000" required rows="2"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <p x-show="errorCambio" x-text="errorCambio" role="alert"
                    class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-slate-700">
                        Diferencia:
                        <span :class="totalCambio() < 0 ? 'text-rose-700' : 'text-slate-900'"
                            x-text="'Q ' + totalCambio().toFixed(2)"></span>
                    </p>
                    <button type="button" @click="abrirModalPagoCambio()" :disabled="!formularioCambioValido()"
                        class="rounded-md bg-indigo-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-800 disabled:cursor-not-allowed disabled:opacity-50">
                        Continuar al pago o reembolso
                    </button>
                </div>

                <div x-cloak x-show="modalPagoCambioAbierto" x-transition.opacity
                    @click.self="modalPagoCambioAbierto = false" @keydown.escape.window="modalPagoCambioAbierto = false"
                    class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4"
                    role="dialog" aria-modal="true" aria-labelledby="titulo-modal-pago-cambio">
                    <div class="my-auto w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-2xl">
                        <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                            <div>
                                <h2 id="titulo-modal-pago-cambio" class="text-lg font-semibold text-slate-900"
                                    x-text="totalCambio() < 0 ? 'Registrar reembolso' : (totalCambio() > 0 ? 'Registrar cobro' : 'Confirmar operación')"></h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    <span x-text="totalCambio() < 0 ? 'Monto a reembolsar:' : (totalCambio() > 0 ? 'Monto a cobrar:' : 'Diferencia:')"></span>
                                    <strong class="text-slate-800" x-text="'Q ' + Math.abs(totalCambio()).toFixed(2)"></strong>
                                </p>
                            </div>
                            <button type="button" @click="modalPagoCambioAbierto = false" aria-label="Volver al carrito"
                                class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                                <x-lucide-x class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-5 py-5 sm:px-6">
                            <p x-show="cargandoMetodosPagoCambio" class="text-sm text-slate-500">
                                Cargando métodos de pago...
                            </p>
                            <p x-show="errorPagoCambio" x-text="errorPagoCambio" role="alert"
                                class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
                            <p x-show="!cargandoMetodosPagoCambio && totalCambio() === 0"
                                class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                                La diferencia es cero; la operación quedará liquidada sin movimiento de pago.
                            </p>

                            <fieldset x-show="totalCambio() !== 0" class="space-y-3"
                                :disabled="cargandoMetodosPagoCambio || !!errorPagoCambio">
                                <legend class="mb-2 text-sm font-semibold text-slate-800">
                                    Método para liquidar la diferencia
                                </legend>
                                <template x-for="(metodo, indice) in metodosPagoCambio" :key="metodo.id">
                                    <div class="rounded-lg border border-slate-200 p-3"
                                        :class="metodo.activo ? 'border-indigo-300 bg-indigo-50/40' : 'bg-white'">
                                        <button type="button"
                                            @click="metodo.activo ? metodo.activo = false : activarMetodoPagoCambio(metodo)"
                                            :aria-pressed="metodo.activo"
                                            class="flex items-center gap-2 rounded font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <span x-text="metodo.nombre"></span>
                                            <span x-show="metodo.activo"
                                                class="text-xs font-normal text-indigo-700">Seleccionado</span>
                                        </button>

                                        <div x-show="metodo.activo" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <input type="hidden"
                                                :name="metodo.activo ? `pagos[${indice}][metodo_pago_id]` : null"
                                                :value="metodo.id">

                                            <template x-if="!metodo.permite_montos">
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                                        :for="`monto-cambio-${indice}`"
                                                        x-text="totalCambio() < 0 ? 'Monto a reembolsar' : 'Monto a cobrar'"></label>
                                                    <input :id="`monto-cambio-${indice}`" type="text" inputmode="decimal"
                                                        pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                                        :value="metodo.monto"
                                                        @focus="activarMetodoPagoCambio(metodo)"
                                                        @input="actualizarImportePagoCambio(metodo, 'monto', $event)"
                                                        class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <input type="hidden"
                                                        :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                                        :value="montoAplicadoPagoCambio(metodo).toFixed(2)">
                                                </div>
                                            </template>

                                            <template x-if="metodo.permite_montos && totalCambio() > 0">
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                                        :for="`monto-recibido-cambio-${indice}`">Monto recibido</label>
                                                    <input :id="`monto-recibido-cambio-${indice}`" type="text"
                                                        inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                                        maxlength="15"
                                                        :name="metodo.activo ? `pagos[${indice}][monto_recibido]` : null"
                                                        :value="metodo.monto_recibido"
                                                        @focus="activarMetodoPagoCambio(metodo)"
                                                        @input="actualizarImportePagoCambio(metodo, 'monto_recibido', $event)"
                                                        class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <input type="hidden"
                                                        :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                                        :value="montoAplicadoPagoCambio(metodo).toFixed(2)">
                                                </div>
                                            </template>

                                            <template x-if="metodo.permite_montos && totalCambio() < 0">
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                                        :for="`monto-reembolso-cambio-${indice}`">Monto a reembolsar</label>
                                                    <input :id="`monto-reembolso-cambio-${indice}`" type="text"
                                                        inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                                        maxlength="15" :value="metodo.monto"
                                                        @focus="activarMetodoPagoCambio(metodo)"
                                                        @input="actualizarImportePagoCambio(metodo, 'monto', $event)"
                                                        class="w-full rounded-md border-slate-300 text-right text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <input type="hidden"
                                                        :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                                        :value="montoAplicadoPagoCambio(metodo).toFixed(2)">
                                                </div>
                                            </template>

                                            <template x-if="metodo.permite_montos && totalCambio() > 0">
                                                <div>
                                                    <span class="mb-1 block text-xs font-medium text-slate-600">Cambio</span>
                                                    <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-right text-sm"
                                                        x-text="'Q ' + Math.max(0, Number(metodo.monto_recibido || 0) - montoAplicadoPagoCambio(metodo)).toFixed(2)">
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="!metodo.permite_montos">
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-600"
                                                        :for="`referencia-cambio-${indice}`">Referencia (opcional)</label>
                                                    <input :id="`referencia-cambio-${indice}`" type="text" maxlength="100"
                                                        :name="metodo.activo ? `pagos[${indice}][referencia]` : null"
                                                        x-model="metodo.referencia"
                                                        class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <p x-show="!cargandoMetodosPagoCambio && metodosPagoCambio.length === 0"
                                    class="text-sm text-slate-500">No hay métodos de pago activos.</p>
                            </fieldset>

                            <p class="text-sm font-semibold text-slate-700" aria-live="polite">
                                <span x-text="totalCambio() < 0 ? 'Reembolso:' : 'Cobro:'"></span>
                                Q <span x-text="Math.abs(metodosPagoCambio
                                    .filter(metodo => metodo.activo)
                                    .reduce((suma, metodo) => suma + montoAplicadoPagoCambio(metodo), 0)).toFixed(2)"></span>
                            </p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                            <button type="button" @click="modalPagoCambioAbierto = false"
                                class="rounded-md border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Volver al carrito
                            </button>
                            <button type="submit"
                                :disabled="enviandoCambio || cargandoMetodosPagoCambio || !!errorPagoCambio || !pagosCambioValidos()"
                                class="rounded-md bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-50">
                                <span x-text="enviandoCambio
                                    ? 'Registrando...'
                                    : (totalCambio() < 0
                                        ? 'Registrar operación y reembolso'
                                        : (totalCambio() > 0 ? 'Registrar operación y cobro' : 'Registrar operación'))"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div x-cloak x-show="mostrarModalCambio" x-transition.opacity
            @click.self="mostrarModalCambio = false" @keydown.escape.window="mostrarModalCambio = false"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4"
            role="dialog" aria-modal="true" aria-labelledby="titulo-modal-cambio">
            <div class="my-auto w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <h2 id="titulo-modal-cambio" class="text-lg font-semibold text-slate-900">
                            Seleccionar artículos para devolver
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">Solo puedes devolver cantidades aún disponibles de la venta.</p>
                    </div>
                    <button type="button" @click="mostrarModalCambio = false" aria-label="Cerrar"
                        class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                        <x-lucide-x class="h-5 w-5" />
                    </button>
                </div>

                <div class="max-h-[65vh] space-y-3 overflow-y-auto px-5 py-5 sm:px-6">
                    <template x-for="detalle in detallesDisponibles" :key="detalle.id">
                        <label class="flex flex-col gap-3 rounded-lg border border-slate-200 p-3 sm:flex-row sm:items-center">
                            <input type="checkbox" x-model="detalle.seleccionada"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="min-w-0 flex-1">
                                <span class="block font-medium text-slate-900" x-text="detalle.nombre"></span>
                                <span class="block text-xs text-slate-500">
                                    Disponible:
                                    <span x-text="Number(detalle.cantidad_disponible).toFixed(2)"></span>
                                    <span x-show="detalle.sku" x-text="' · SKU: ' + detalle.sku"></span>
                                </span>
                            </span>
                            <span class="inline-flex items-center gap-1" @click.stop>
                                <button type="button" @click="ajustarCantidadDevolucion(detalle, -1)"
                                    :disabled="!detalle.seleccionada || Number(detalle.cantidad) <= 0"
                                    aria-label="Disminuir cantidad"
                                    class="flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 text-lg font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                                    -
                                </button>
                                <input type="text" inputmode="decimal" x-model="detalle.cantidad"
                                    :disabled="!detalle.seleccionada"
                                    @input="actualizarCantidadDevolucion(detalle, $event)"
                                    class="w-20 rounded-md border-slate-300 text-center text-sm disabled:bg-slate-100"
                                    aria-label="Cantidad a devolver">
                                <button type="button" @click="ajustarCantidadDevolucion(detalle, 1)"
                                    :disabled="Number(detalle.cantidad || 0) >= Number(detalle.cantidad_disponible)"
                                    aria-label="Aumentar cantidad"
                                    class="flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 text-lg font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                                    +
                                </button>
                            </span>
                        </label>
                    </template>
                    <p x-show="errorCambio" x-text="errorCambio" role="alert"
                        class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" @click="mostrarModalCambio = false"
                        class="rounded-md border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="button" @click="agregarDevoluciones()"
                        class="rounded-md bg-indigo-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-800">
                        Continuar al carrito
                    </button>
                </div>
            </div>
        </div>
    </section>
@endif
