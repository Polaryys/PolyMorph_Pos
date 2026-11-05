<x-app-layout>
    <div x-data="{
        buscar: '',
        resultados: [],
        seleccionado: null,
        cantidad: 1,
        tipoPrecio: 'unitario',
        descuento: '0.00',
        detalles: [],
        pagos: [],
        cargando: false,
        errorBusqueda: false,
        errorFormulario: '',
        versionBusqueda: 0,

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
                const url = new URL(@js(route('compras.buscar-productos')), window.location.origin);
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

        seleccionar(producto) {
            this.versionBusqueda++;
            this.seleccionado = producto;
            this.buscar = producto.nombre;
            this.resultados = [];
            this.cargando = false;
            this.cantidad = 1;
            this.tipoPrecio = 'unitario';
            this.descuento = '0.00';
            this.errorFormulario = '';
        },

        cambiarProducto() {
            this.versionBusqueda++;
            this.seleccionado = null;
            this.buscar = '';
            this.resultados = [];
            this.cargando = false;
        },

        decimalValido(valor, requerido = true) {
            const texto = String(valor ?? '').trim();
            return texto === '' ? !requerido : /^\d+(\.\d{1,2})?$/.test(texto);
        },

        precioActual(producto, tipoPrecio, cantidad) {
            if (!producto) return 0;
            if (tipoPrecio === 'docena' && Number(cantidad) >= 12 && producto.precio_docena_actual !== null) {
                return Number(producto.precio_docena_actual);
            }
            if (tipoPrecio === 'ciento' && Number(cantidad) >= 100 && producto.precio_ciento_actual !== null) {
                return Number(producto.precio_ciento_actual);
            }
            return Number(producto.precio_unitario_actual);
        },

        subtotalDetalle(detalle) {
            return Math.max(0, Number(detalle.cantidad) * this.precioActual(detalle, detalle.tipo_precio, detalle.cantidad) - Number(detalle.descuento || 0));
        },

        totalVenta() {
            return this.detalles.reduce((total, detalle) => total + this.subtotalDetalle(detalle), 0);
        },

        totalPagado() {
            return this.pagos.reduce((total, pago) => total + (Number(pago.monto) || 0), 0);
        },

        saldoPendiente() {
            return Math.max(0, this.totalVenta() - this.totalPagado());
        },

        detalleValido(detalle) {
            const cantidad = Number(detalle.cantidad);
            const precio = this.precioActual(detalle, detalle.tipo_precio, cantidad);
            const bruto = cantidad * precio;
            const precioPorVolumenValido = detalle.tipo_precio === 'unitario'
                || (detalle.tipo_precio === 'docena' && cantidad >= 12 && detalle.precio_docena_actual !== null)
                || (detalle.tipo_precio === 'ciento' && cantidad >= 100 && detalle.precio_ciento_actual !== null);

            return Number.isInteger(cantidad)
                && cantidad > 0
                && cantidad <= Number(detalle.cantidad_actual)
                && precioPorVolumenValido
                && this.decimalValido(detalle.descuento)
                && Number(detalle.descuento || 0) <= bruto;
        },

        pagosValidos() {
            return this.pagos.every(pago =>
                pago.metodo_pago_id !== ''
                && this.decimalValido(pago.monto)
                && Number(pago.monto) > 0
                && (pago.monto_recibido === '' || (
                    this.decimalValido(pago.monto_recibido)
                    && Number(pago.monto_recibido) >= Number(pago.monto)
                ))
            ) && this.totalPagado() <= this.totalVenta();
        },

        agregarDetalle() {
            if (!this.seleccionado) return;
            if (this.detalles.some(detalle => detalle.presentacion_id === this.seleccionado.presentacion_id)) {
                this.errorFormulario = 'Este producto ya está agregado. Puedes cambiar su cantidad en la tabla.';
                return;
            }

            const detalle = {
                ...this.seleccionado,
                cantidad: Number(this.cantidad),
                tipo_precio: this.tipoPrecio,
                descuento: this.descuento,
            };

            if (!this.detalleValido(detalle)) {
                this.errorFormulario = 'Revisa la cantidad, el precio y el descuento del artículo.';
                return;
            }

            this.detalles.push(detalle);
            this.cambiarProducto();
            this.errorFormulario = '';
        },

        quitarDetalle(indice) {
            this.detalles.splice(indice, 1);
        },

        agregarPago() {
            this.pagos.push({ metodo_pago_id: '', monto: '', monto_recibido: '', referencia: '' });
        },

        formularioValido() {
            return this.detalles.length > 0
                && this.detalles.every(detalle => this.detalleValido(detalle))
                && this.pagosValidos();
        },
    }" class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="pl-3">
                <p class="text-sm font-medium text-slate-500">Punto de venta</p>
                <h1 class="mt-1 text-2xl font-semibold text-slate-900">Registrar venta</h1>
            </div>
            <x-secondary-link href="{{ route('ventas.index') }}">Volver a ventas</x-secondary-link>
        </div>

        <form action="{{ route('ventas.store') }}" method="POST" class="space-y-5">
            @csrf

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

            @include('ventas.partials._buscador-producto')
            @include('ventas.partials._detalles')

            <p x-cloak x-show="errorFormulario" x-text="errorFormulario"
                class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert"></p>

            @error('detalles')
                <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>
            @enderror

            @include('ventas.partials._pagos')

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-secondary-link href="{{ route('ventas.index') }}">Cancelar</x-secondary-link>
                <button type="submit" :disabled="!formularioValido()"
                    onclick="return confirm('¿Registrar esta venta? Se actualizarán las existencias del inventario.');"
                    class="rounded-md bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-50">
                    Registrar venta
                </button>
            </div>
        </form>
    </div>
</x-app-layout>