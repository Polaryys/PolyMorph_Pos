<x-app-layout>
    <div x-data="{
        buscar: '',
        resultados: [],
        buscarCliente: '',
        resultadosClientes: [],
        clienteSeleccionado: null,
        detalles: [],
        cargando: false,
        cargandoClientes: false,
        errorBusqueda: false,
        errorBusquedaClientes: false,
        errorFormulario: '',
        versionBusqueda: 0,
        versionBusquedaClientes: 0,
        modalPagoAbierto: false,
        cargandoMetodosPago: false,
        metodosPago: [],
        ventaCredito: false,
        errorPago: '',
        omitirActivacionEnfoqueInicial: false,

        async buscarProductos() {
            const version = ++this.versionBusqueda;
            const termino = this.buscar.trim();

            if (!termino) {
                this.resultados = [];
                this.cargando = false;
                return;
            }

            this.cargando = true;
            this.errorBusqueda = false;

            try {
                const url = new URL('{{ route('ventas.buscar-productos') }}', window.location.origin);
                url.searchParams.set('buscar', termino);
                const respuesta = await fetch(url, { headers: { Accept: 'application/json' } });

                if (!respuesta.ok) throw new Error('No se pudo realizar la búsqueda.');

                const resultados = await respuesta.json();
                if (version === this.versionBusqueda) this.resultados = resultados;
            } catch (error) {
                if (version === this.versionBusqueda) {
                    this.resultados = [];
                    this.errorBusqueda = true;
                }
            } finally {
                if (version === this.versionBusqueda) this.cargando = false;
            }
        },

        async buscarClientes() {
            const version = ++this.versionBusquedaClientes;
            const termino = this.buscarCliente.trim();

            if (!termino) {
                this.resultadosClientes = [];
                this.cargandoClientes = false;
                return;
            }

            this.cargandoClientes = true;
            this.errorBusquedaClientes = false;

            try {
                const url = new URL('{{ route('ventas.buscar-clientes') }}', window.location.origin);
                url.searchParams.set('buscar', termino);
                const respuesta = await fetch(url, { headers: { Accept: 'application/json' } });

                if (!respuesta.ok) throw new Error('No se pudo realizar la búsqueda.');

                const resultados = await respuesta.json();
                if (version === this.versionBusquedaClientes) this.resultadosClientes = resultados;
            } catch (error) {
                if (version === this.versionBusquedaClientes) {
                    this.resultadosClientes = [];
                    this.errorBusquedaClientes = true;
                }
            } finally {
                if (version === this.versionBusquedaClientes) this.cargandoClientes = false;
            }
        },

        seleccionarCliente(cliente) {
            this.versionBusquedaClientes++;
            this.clienteSeleccionado = cliente;
            this.buscarCliente = '';
            this.resultadosClientes = [];
            this.cargandoClientes = false;
        },

        quitarCliente() {
            this.clienteSeleccionado = null;
        },

        seleccionar(producto) {
            this.versionBusqueda++;
            this.buscar = '';
            this.resultados = [];
            this.cargando = false;
            if (this.detalles.some(detalle => detalle.presentacion_id === producto.presentacion_id)) {
                this.errorFormulario = 'Este producto ya está agregado. Puedes cambiar su cantidad en la tabla.';
                return;
            }

            const detalle = {
                ...producto,
                cantidad: 1,
                tipo_precio: 'unitario',
            };
            detalle.descuento = this.descuentoAutomatico(detalle).toFixed(2);

            if (!this.detalleValido(detalle)) {
                this.errorFormulario = 'Este producto no tiene existencias suficientes para agregarlo.';
                return;
            }

            this.detalles.push(detalle);
            this.errorFormulario = '';
        },

        decimalValido(valor, requerido = true) {
            const texto = String(valor ?? '').trim();
            return texto === '' ? !requerido : /^\d+(\.\d{1,2})?$/.test(texto);
        },

        precioActual(producto, tipoPrecio, cantidad) {
            if (!producto) return 0;
            const cantidadBase = Number(cantidad) * Number(producto.factor || 1);
            if (tipoPrecio === 'docena' && cantidadBase >= 12 && producto.precio_docena_actual !== null) {
                return Number(producto.precio_docena_actual);
            }
            if (tipoPrecio === 'ciento' && cantidadBase >= 100 && producto.precio_ciento_actual !== null) {
                return Number(producto.precio_ciento_actual);
            }
            return Number(producto.precio_unitario_actual);
        },

        cantidadDisponible(producto) {
            if (producto.tipo_item !== 'PRODUCTO') return Infinity;
            return Math.floor(Number(producto.cantidad_actual || 0) / Number(producto.factor || 1));
        },

        subtotalDetalle(detalle) {
            return Math.max(
                0,
                Number(detalle.cantidad) * Number(detalle.precio_unitario_actual)
                    - (Number(detalle.descuento) || 0)
            );
        },

        totalImporte() {
            return this.detalles.reduce(
                (total, detalle) => total
                    + Number(detalle.cantidad) * Number(detalle.precio_unitario_actual),
                0
            );
        },

        totalDescuento() {
            return this.detalles.reduce((total, detalle) => total + (Number(detalle.descuento) || 0), 0);
        },

        descuentoAutomatico(detalle) {
            const ahorroUnitario = Number(detalle.precio_unitario_actual)
                - this.precioActual(detalle, detalle.tipo_precio, detalle.cantidad);
            return Math.max(0, Math.round((Number(detalle.cantidad) * ahorroUnitario + Number.EPSILON) * 100) / 100);
        },

        actualizarDescuentoAutomatico(detalle, conservarDescuento = false) {
            const cantidad = Number(detalle.cantidad);

            if (
                (detalle.tipo_precio === 'docena' && cantidad * Number(detalle.factor || 1) < 12)
                || (detalle.tipo_precio === 'ciento' && cantidad * Number(detalle.factor || 1) < 100)
            ) {
                detalle.tipo_precio = 'unitario';
            }

            if (!conservarDescuento) {
                detalle.descuento = this.descuentoAutomatico(detalle).toFixed(2);
            }
        },

        ajustarCantidad(detalle, cambio) {
            const cantidadActual = Number(detalle.cantidad) || 1;
            const cantidadMaxima = detalle.tipo_item === 'PRODUCTO'
                ? this.cantidadDisponible(detalle)
                : Infinity;
            detalle.cantidad = Math.min(cantidadMaxima, Math.max(1, cantidadActual + cambio));
            this.actualizarDescuentoAutomatico(detalle, true);
        },

        totalVenta() {
            return this.detalles.reduce((total, detalle) => total + this.subtotalDetalle(detalle), 0);
        },

        detalleValido(detalle) {
            const cantidad = Number(detalle.cantidad);
            const precio = this.precioActual(detalle, detalle.tipo_precio, cantidad);
            const descuento = Number(detalle.descuento);
            const precioPorVolumenValido = detalle.tipo_precio === 'unitario'
                || (detalle.tipo_precio === 'docena'
                    && cantidad * Number(detalle.factor || 1) >= 12
                    && detalle.precio_docena_actual !== null)
                || (detalle.tipo_precio === 'ciento'
                    && cantidad * Number(detalle.factor || 1) >= 100
                    && detalle.precio_ciento_actual !== null);

            return Number.isInteger(cantidad)
                && cantidad > 0
                && cantidad <= this.cantidadDisponible(detalle)
                && (detalle.tipo_item !== 'PRODUCTO'
                    || Number.isInteger(cantidad * Number(detalle.factor || 1)))
                && precioPorVolumenValido
                && precio >= 0
                && this.decimalValido(detalle.descuento)
                && descuento >= 0
                && descuento <= cantidad * Number(detalle.precio_unitario_actual);
        },

        quitarDetalle(indice) {
            this.detalles.splice(indice, 1);
        },

        limpiarCarrito() {
            if (this.detalles.length && !confirm('¿Limpiar todos los productos del carrito?')) return;
            this.detalles = [];
            this.errorFormulario = '';
        },

        async abrirModalPago() {
            if (!this.formularioValido()) {
                this.errorFormulario = 'Agrega al menos un artículo disponible antes de registrar la venta.';
                return;
            }

            this.modalPagoAbierto = true;
            this.cargandoMetodosPago = true;
            this.errorPago = '';
            this.ventaCredito = false;

            try {
                const respuesta = await fetch('{{ route('ventas.metodos-pago') }}', {
                    headers: { Accept: 'application/json' },
                });
                if (!respuesta.ok) throw new Error('No se pudieron cargar los métodos de pago.');

                const metodos = await respuesta.json();
                this.metodosPago = metodos.map(metodo => ({
                    ...metodo,
                    efectivo: metodo.nombre.trim().toLowerCase() === 'efectivo',
                    activo: false,
                    monto: '',
                    monto_recibido: '',
                    referencia: '',
                }));
            } catch (error) {
                this.errorPago = error.message;
            } finally {
                this.cargandoMetodosPago = false;
                this.$nextTick(() => {
                    this.omitirActivacionEnfoqueInicial = true;
                    requestAnimationFrame(() => {
                        this.$el.querySelector('[data-cash-received]')?.focus({ preventScroll: true });
                        this.omitirActivacionEnfoqueInicial = false;
                    });
                });
            }
        },

        activarMetodo(metodo) {
            if (this.omitirActivacionEnfoqueInicial) return;
            metodo.activo = true;
            if (!metodo.efectivo && !Number(metodo.monto)) {
                const montoDisponible = this.montoDisponiblePara(metodo);
                metodo.monto = montoDisponible > 0 ? montoDisponible.toFixed(2) : '';
            }
        },

        actualizarImporte(metodo, campo, event) {
            const valor = event.target.value;

            if (valor.includes(',')) {
                event.target.value = metodo[campo] || '';
                return;
            }

            const [entero, ...decimales] = valor.replace(/[^\d.]/g, '').split('.');
            const valorValidado = decimales.length
                ? `${entero}.${decimales.join('').slice(0, 2)}`
                : entero;

            metodo[campo] = valorValidado;
            event.target.value = valorValidado;
            this.activarMetodo(metodo);
        },

        montoAplicado(metodo) {
            if (!metodo.efectivo) return Number(metodo.monto) || 0;
            return Math.min(Number(metodo.monto_recibido) || 0, this.montoDisponiblePara(metodo));
        },

        montoDisponiblePara(metodoActual) {
            const otrosPagos = this.metodosPago
                .filter(metodo => metodo !== metodoActual && metodo.activo)
                .reduce((total, metodo) => total + this.montoAplicado(metodo), 0);

            return Math.max(0, Math.round((this.totalVenta() - otrosPagos) * 100) / 100);
        },

        pagosValidos() {
            const pagos = this.metodosPago.filter(metodo => metodo.activo);
            const montoPagado = pagos.reduce((total, metodo) => total + this.montoAplicado(metodo), 0);
            const pagosIndividualesValidos = pagos.every(metodo =>
                this.decimalValido(metodo.efectivo ? metodo.monto_recibido : metodo.monto)
                && this.montoAplicado(metodo) > 0
                && (!metodo.efectivo || Number(metodo.monto_recibido) > 0)
            );

            return pagosIndividualesValidos
                && Math.round(montoPagado * 100) <= Math.round(this.totalVenta() * 100)
                && (this.ventaCredito
                    || Math.round(montoPagado * 100) >= Math.round(this.totalVenta() * 100));
        },

        totalPagadoModal() {
            return this.metodosPago
                .filter(metodo => metodo.activo)
                .reduce((total, metodo) => total + this.montoAplicado(metodo), 0);
        },

        diferenciaEfectivo(metodo) {
            return Math.round(
                ((Number(metodo.monto_recibido) || 0) - this.montoAplicado(metodo) + Number.EPSILON) * 100
            ) / 100;
        },

        confirmarVenta(event) {
            if (!this.modalPagoAbierto || !this.pagosValidos()) return;
            event.target.submit();
        },

        enviarConEnter() {
            if (this.modalPagoAbierto && this.pagosValidos()) {
                this.$refs.formularioVenta.requestSubmit();
            }
        },

        formularioValido() {
            return this.detalles.length > 0
                && this.detalles.every(detalle => this.detalleValido(detalle));
        },
    }" class="space-y-3">
        <x-section-title>
            Registrar venta
        </x-section-title>

        <form action="{{ route('ventas.store') }}" method="POST" class="space-y-5"
            x-ref="formularioVenta" @submit.prevent="confirmarVenta($event)">
            @csrf

            <input type="hidden" name="cliente_id" :value="clienteSeleccionado?.id ?? ''">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <p class="font-semibold">No se pudo registrar la venta:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @include('ventas.partials._buscador-producto')
                @include('ventas.partials._buscador-cliente')
            </div>
            @include('ventas.partials._detalles')
            @include('ventas.partials._modal-pago')

            <p x-cloak x-show="errorFormulario" x-text="errorFormulario"
                class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert"></p>

            @error('detalles')
                <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>
            @enderror

        </form>
    </div>
</x-app-layout>