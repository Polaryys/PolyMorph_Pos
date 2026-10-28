<x-app-layout>
    <div
        x-data="{
            buscar: '',
            resultados: [],
            seleccionado: null,
            detalles: @js($detalles),
            cantidad: 1,
            costoUnitario: '',
            precioNuevo: '',
            precioDocenaNuevo: '',
            precioCientoNuevo: '',
            cargando: false,
            errorBusqueda: false,
            guardandoBorrador: false,
            errorBorrador: '',

            async buscarProductos() {
                const termino = this.buscar.trim();
                if (!termino) {
                    this.resultados = [];
                    return;
                }

                this.cargando = true;
                this.errorBusqueda = false;

                try {
                    const respuesta = await fetch(
                        '{{ route('compras.buscar-productos') }}?buscar=' + encodeURIComponent(termino),
                        { headers: { 'Accept': 'application/json' } }
                    );
                    if (!respuesta.ok) throw new Error('No se pudo realizar la búsqueda.');
                    this.resultados = await respuesta.json();
                } catch (error) {
                    this.resultados = [];
                    this.errorBusqueda = true;
                } finally {
                    this.cargando = false;
                }
            },

            seleccionar(producto) {
                this.seleccionado = producto;
                this.buscar = producto.nombre;
                this.resultados = [];
                this.cantidad = 1;
                this.costoUnitario = Number(producto.costo_promedio_actual).toFixed(2);
                this.precioNuevo = Number(producto.precio_unitario_actual).toFixed(2);
                this.precioDocenaNuevo = producto.precio_docena_actual === null
                    ? ''
                    : Number(producto.precio_docena_actual).toFixed(2);
                this.precioCientoNuevo = producto.precio_ciento_actual === null
                    ? ''
                    : Number(producto.precio_ciento_actual).toFixed(2);
            },

            cambiarProducto() {
                this.seleccionado = null;
                this.buscar = '';
                this.resultados = [];
            },

            async agregarDetalle() {
                if (!this.seleccionado) return;
                if (
                    !Number.isInteger(Number(this.cantidad)) ||
                    Number(this.cantidad) < 1 ||
                    !this.decimalValido(this.costoUnitario, true, true) ||
                    !this.decimalValido(this.precioNuevo) ||
                    Number(this.precioNuevo) < 0 ||
                    !this.decimalValido(this.precioDocenaNuevo, false) ||
                    !this.decimalValido(this.precioCientoNuevo, false)
                ) {
                    this.errorBorrador = 'Ingresa una cantidad, un costo unitario y un precio válidos.';
                    return;
                }

                if (this.detalles.some(detalle => detalle.presentacion_id === this.seleccionado.presentacion_id)) {
                    this.errorBorrador = 'Este producto ya está en la compra. Puedes editarlo en la tabla.';
                    return;
                }

                this.detalles.push({
                    ...this.seleccionado,
                    cantidad: Number(this.cantidad),
                    costo_unitario: this.costoUnitario,
                    precio_unitario_nuevo: this.precioNuevo,
                    precio_docena_nuevo: this.precioDocenaNuevo,
                    precio_ciento_nuevo: this.precioCientoNuevo,
                });
                this.cambiarProducto();
                this.errorBorrador = '';
                await this.guardarBorrador();
            },

            async guardarBorrador() {
                this.guardandoBorrador = true;
                this.errorBorrador = '';

                const formulario = this.$refs.form;
                const respuesta = await fetch('{{ route('compras.borrador.guardar') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _token: formulario.querySelector('[name=_token]').value,
                        proveedor_id: formulario.querySelector('[name=proveedor_id]').value || null,
                        numero_factura: formulario.querySelector('[name=numero_factura]').value || null,
                        fecha_compra: formulario.querySelector('[name=fecha_compra]').value || null,
                        observaciones: formulario.querySelector('[name=observaciones]').value || null,
                        detalles: this.detalles.map(detalle => ({
                            presentacion_id: detalle.presentacion_id,
                            cantidad: detalle.cantidad,
                            costo_unitario: detalle.costo_unitario,
                            precio_unitario_nuevo: detalle.precio_unitario_nuevo,
                            precio_docena_nuevo: detalle.precio_docena_nuevo || null,
                            precio_ciento_nuevo: detalle.precio_ciento_nuevo || null,
                        })),
                    }),
                }).catch(() => null);

                this.guardandoBorrador = false;

                if (!respuesta || !respuesta.ok) {
                    const errores = respuesta
                        ? await respuesta.json().catch(() => ({}))
                        : {};
                    this.errorBorrador = Object.values(errores.errors ?? {})
                        .flat()
                        .join(' ') || errores.message ||
                        'No se pudo guardar el borrador. Revisa los datos e intenta nuevamente.';
                    return false;
                }

                return true;
            },

            async navegar(url) {
                if (await this.guardarBorrador()) window.location.assign(url);
            },

            async quitarDetalle(indice) {
                this.detalles.splice(indice, 1);
                await this.guardarBorrador();
            },

            async vaciarBorrador() {
                this.detalles = [];
                await this.guardarBorrador();
            },

            totalCompra() {
                return this.detalles.reduce(
                    (total, detalle) => total + Number(detalle.cantidad) * Number(detalle.costo_unitario),
                    0
                );
            },

            decimalValido(valor, requerido = true, positivo = false) {
                const texto = String(valor ?? '').trim();
                if (!texto) return !requerido;
                if (!/^\d+(\.\d{1,2})?$/.test(texto)) return false;
                return !positivo || Number(texto) > 0;
            },

            detallesValidos() {
                return this.detalles.length > 0 && this.detalles.every(detalle =>
                    Number.isInteger(Number(detalle.cantidad)) &&
                    Number(detalle.cantidad) >= 1 &&
                    this.decimalValido(detalle.costo_unitario, true, true) &&
                    this.decimalValido(detalle.precio_unitario_nuevo) &&
                    Number(detalle.precio_unitario_nuevo) >= 0 &&
                    this.decimalValido(detalle.precio_docena_nuevo, false) &&
                    this.decimalValido(detalle.precio_ciento_nuevo, false)
                );
            },
        }"
        class="space-y-5"
    >
        <form x-ref="form" action="{{ route('compras.store') }}" method="POST" class="space-y-5">
            @csrf

            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900">Registrar ingreso de compra</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            El número consecutivo se asignará automáticamente al guardar.
                        </p>
                    </div>
                    <a href="{{ route('compras.index') }}" @click.prevent="navegar($el.href)"
                        class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                        Volver a compras
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="fecha_compra" class="mb-1 block text-sm font-medium text-slate-700">
                            Fecha de compra <span class="text-red-500">*</span>
                        </label>
                        <input id="fecha_compra" name="fecha_compra" type="date" required
                            value="{{ old('fecha_compra', $borrador['fecha_compra'] ?? now()->toDateString()) }}"
                            @change="guardarBorrador()"
                            class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('fecha_compra') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="proveedor_id" class="mb-1 block text-sm font-medium text-slate-700">
                            Proveedor
                        </label>
                        <select id="proveedor_id" name="proveedor_id" @change="guardarBorrador()"
                            class="w-full rounded-md border border-slate-300 bg-white p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sin proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}"
                                    @selected((string) old('proveedor_id', $borrador['proveedor_id'] ?? '') === (string) $proveedor->id)>
                                    {{ $proveedor->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="numero_factura" class="mb-1 block text-sm font-medium text-slate-700">
                            Número de factura
                        </label>
                        <input id="numero_factura" name="numero_factura" type="text" maxlength="50"
                            value="{{ old('numero_factura', $borrador['numero_factura'] ?? '') }}"
                            @change="guardarBorrador()"
                            class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('numero_factura') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="observaciones" class="mb-1 block text-sm font-medium text-slate-700">
                            Observaciones
                        </label>
                        <input id="observaciones" name="observaciones" type="text"
                            value="{{ old('observaciones', $borrador['observaciones'] ?? '') }}"
                            @change="guardarBorrador()"
                            class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('observaciones') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            @include('compras.partials._buscador-producto')
            @include('compras.partials._detalles')

            @error('detalles')
                <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>
            @enderror
            <p x-cloak x-show="errorBorrador" x-text="errorBorrador"
                class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                    <p class="font-semibold">No se pudo registrar la compra:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" @click="vaciarBorrador()" :disabled="detalles.length === 0 || guardandoBorrador"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">
                    Vaciar compra
                </button>
                <a href="{{ route('compras.index') }}" @click.prevent="navegar($el.href)"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit" :disabled="detalles.length === 0"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-text="guardandoBorrador ? 'Guardando borrador...' : 'Registrar compra'"></span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>