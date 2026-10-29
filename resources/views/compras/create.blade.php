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
            errorFormulario: '',

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
                    this.errorFormulario = 'Ingresa una cantidad, un costo unitario y un precio válidos.';
                    return;
                }

                if (this.detalles.some(detalle => detalle.presentacion_id === this.seleccionado.presentacion_id)) {
                    this.errorFormulario = 'Este producto ya está en la compra. Puedes editarlo en la tabla.';
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
                this.errorFormulario = '';
            },

            quitarDetalle(indice) {
                this.detalles.splice(indice, 1);
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
        <form action="{{ $compra ? route('compras.update', $compra) : route('compras.store') }}"
            method="POST" class="space-y-5">
            @csrf
            @if ($compra)
                @method('PUT')
            @endif

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900">
                            {{ $compra ? 'Editar compra pendiente' : 'Nueva compra pendiente' }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Guardar la pendiente no modifica inventario ni precios. Se recalcularán al registrarla.
                        </p>
                    </div>
                    <a href="{{ route('compras.index') }}"
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
                            value="{{ old('fecha_compra', $compra?->fecha_compra?->toDateString() ?? now()->toDateString()) }}"
                            class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('fecha_compra') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="proveedor_id" class="mb-1 block text-sm font-medium text-slate-700">
                            Proveedor
                        </label>
                        <select id="proveedor_id" name="proveedor_id"
                            class="w-full rounded-md border border-slate-300 bg-white p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sin proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}"
                                    @selected((string) old('proveedor_id', $compra?->proveedor_id ?? '') === (string) $proveedor->id)>
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
                            value="{{ old('numero_factura', $compra?->numero_factura ?? '') }}"
                            class="w-full rounded-md border border-slate-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('numero_factura') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="observaciones" class="mb-1 block text-sm font-medium text-slate-700">
                            Observaciones
                        </label>
                        <input id="observaciones" name="observaciones" type="text"
                            value="{{ old('observaciones', $compra?->observaciones ?? '') }}"
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
            <p x-cloak x-show="errorFormulario" x-text="errorFormulario"
                class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                    <p class="font-semibold">No se pudo guardar la compra pendiente:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('compras.index') }}"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit" :disabled="!detallesValidos()"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ $compra ? 'Guardar cambios' : 'Guardar como pendiente' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>