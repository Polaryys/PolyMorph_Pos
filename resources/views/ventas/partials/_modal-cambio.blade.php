@if (
    $venta->tipo_operacion === 'VENTA' &&
        $venta->estado_venta === 'REGISTRADA' &&
        $detallesDisponiblesCambio->isNotEmpty())
    <section x-data="{
        detallesDisponibles: @js($detallesDisponiblesCambio),
        devolucionesCambio: [],
        productosCambio: [],
        mostrarModalCambio: false,
        carritoCambioAbierto: false,
        buscarArticulo: '',
        resultadosCambio: [],
        cargandoBusquedaCambio: false,
        errorBusquedaCambio: false,
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
            const cantidadNueva = cantidadActual === 0 && ajuste > 0 ?
                Math.min(1, Number(detalle.cantidad_disponible)) :
                Math.min(
                    Math.max(0, Math.round((cantidadActual + ajuste) * 100) / 100),
                    Number(detalle.cantidad_disponible)
                );
    
            detalle.cantidad = cantidadNueva > 0 ? String(cantidadNueva) : '';
            detalle.seleccionada = cantidadNueva > 0;
        },
    
        actualizarCantidadDevolucion(detalle, event) {
            const valor = event.target.value;
            const [entero, ...decimales] = valor.replace(/[^\d.]/g, '').split('.');
            const valorValidado = decimales.length ?
                `${entero}.${decimales.join('').slice(0, 2)}` :
                entero;
    
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
                return !Number.isFinite(cantidad) ||
                    cantidad <= 0 ||
                    cantidad > Number(detalle.cantidad_disponible) ||
                    (detalle.tipo_item === 'PRODUCTO' &&
                        !Number.isInteger(cantidad * Number(detalle.factor || 1)));
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
    
        cantidadDisponible(producto) {
            if (producto.tipo_item !== 'PRODUCTO') return Infinity;
            return Math.floor(Number(producto.cantidad_actual || 0) / Number(producto.factor || 1));
        },
    
        async buscarArticulosCambio() {
            const version = ++this.versionBusquedaCambio;
            const termino = this.buscarArticulo.trim();
    
            if (!termino) {
                this.resultadosCambio = [];
                this.cargandoBusquedaCambio = false;
                this.errorBusquedaCambio = false;
                return;
            }
    
            this.cargandoBusquedaCambio = true;
            this.errorBusquedaCambio = false;
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
                    this.errorBusquedaCambio = true;
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
                descuento_adicional: '0.00',
            });
            this.versionBusquedaCambio++;
            this.buscarArticulo = '';
            this.resultadosCambio = [];
            this.errorBusquedaCambio = false;
            this.errorCambio = '';
            this.carritoCambioAbierto = true;
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
    
        descuentoVolumenProductoCambio(detalle) {
            const cantidad = Number(detalle.cantidad) || 0;
            return Math.max(0, Math.round(
                (cantidad * (Number(detalle.precio_unitario_actual || 0) - this.precioAplicadoCambio(detalle)) +
                    Number.EPSILON) * 100
            ) / 100);
        },
    
        maxDescuentoAdicionalProductoCambio(detalle) {
            const subtotalBruto = (Number(detalle.cantidad) || 0) * Number(detalle.precio_unitario_actual || 0);
            return Math.max(0, Math.round(
                (subtotalBruto - this.descuentoVolumenProductoCambio(detalle)) * 100
            ) / 100);
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
            return Math.round(
                (this.descuentoVolumenProductoCambio(detalle) +
                    (Number(detalle.descuento_adicional) || 0)) * 100
            ) / 100;
        },
    
        actualizarDescuentoProductoCambio(detalle, event) {
            const valor = event.target.value;
            const [entero, ...decimales] = valor.replace(/[^\d.]/g, '').split('.');
            let valorValidado = decimales.length ?
                `${entero}.${decimales.join('').slice(0, 2)}` :
                entero;
    
            if (valorValidado !== '' && Number(valorValidado) > this.maxDescuentoAdicionalProductoCambio(detalle)) {
                valorValidado = this.maxDescuentoAdicionalProductoCambio(detalle).toFixed(2);
            }
    
            detalle.descuento_adicional = valorValidado;
            event.target.value = valorValidado;
        },
    
        actualizarPrecioProductoCambio(detalle) {
            const cantidadBase = Number(detalle.cantidad || 0) * Number(detalle.factor || 1);
            if (
                (detalle.tipo_precio === 'docena' && cantidadBase < 12) ||
                (detalle.tipo_precio === 'ciento' && cantidadBase < 100)
            ) {
                detalle.tipo_precio = 'unitario';
            }
    
            if (Number(detalle.descuento_adicional) > this.maxDescuentoAdicionalProductoCambio(detalle)) {
                detalle.descuento_adicional = this.maxDescuentoAdicionalProductoCambio(detalle).toFixed(2);
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
                const montoIngresado = recibeEfectivo ?
                    Number(metodo.monto_recibido) || 0 :
                    Number(metodo.monto) || 0;
                aplicado += Math.min(montoIngresado, Math.max(0, totalDisponible - aplicado));
            }
    
            return Math.max(0, Math.round((totalDisponible - aplicado) * 100) / 100);
        },
    
        montoAplicadoPagoCambio(metodo) {
            const total = this.totalCambio();
            let aplicado = 0;
    
            for (const pago of this.metodosPagoCambio.filter(item => item.activo)) {
                const recibeEfectivo = pago.permite_montos && total > 0;
                const montoIngresado = recibeEfectivo ?
                    Number(pago.monto_recibido) || 0 :
                    Number(pago.monto) || 0;
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
            const valorValidado = decimales.length ?
                `${entero}.${decimales.join('').slice(0, 2)}` :
                entero;
    
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
    
            return pagos.length > 0 &&
                pagos.every(metodo => {
                    const recibeEfectivo = metodo.permite_montos && total > 0;
                    const montoIngresado = recibeEfectivo ? metodo.monto_recibido : metodo.monto;
    
                    return /^\d+(\.\d{1,2})?$/.test(String(montoIngresado || '')) &&
                        Math.abs(this.montoAplicadoPagoCambio(metodo)) > 0 &&
                        (recibeEfectivo ||
                            Number(montoIngresado) === Math.abs(this.montoAplicadoPagoCambio(metodo))) &&
                        (!recibeEfectivo ||
                            Number(metodo.monto_recibido) >= Math.abs(this.montoAplicadoPagoCambio(metodo)));
                }) &&
                Math.round(sumaAplicada * 100) === Math.round(Math.abs(total) * 100);
        },
    
        formularioCambioValido() {
            return this.devolucionesCambio.length > 0 &&
                this.motivoCambio.trim().length > 0 &&
                this.devolucionesCambio.every(detalle => {
                    const cantidad = Number(detalle.cantidad);
                    return Number.isFinite(cantidad) &&
                        cantidad > 0 &&
                        cantidad <= Number(detalle.cantidad_disponible) &&
                        (detalle.tipo_item !== 'PRODUCTO' ||
                            Number.isInteger(cantidad * Number(detalle.factor || 1)));
                }) &&
                this.productosCambio.every(detalle => {
                    const cantidad = Number(detalle.cantidad);
                    const cantidadBase = cantidad * Number(detalle.factor || 1);
                    const descuentoAdicional = Number(detalle.descuento_adicional || 0);
                    const precioPorVolumenValido = detalle.tipo_precio === 'unitario' ||
                        (detalle.tipo_precio === 'docena' &&
                            cantidadBase >= 12 &&
                            detalle.precio_docena_actual !== null) ||
                        (detalle.tipo_precio === 'ciento' &&
                            cantidadBase >= 100 &&
                            detalle.precio_ciento_actual !== null);
                    return Number.isInteger(cantidad) &&
                        cantidad > 0 &&
                        Number.isFinite(descuentoAdicional) &&
                        descuentoAdicional >= 0 &&
                        this.descuentoProductoCambio(detalle) <=
                        cantidad * Number(detalle.precio_unitario_actual || 0) &&
                        precioPorVolumenValido &&
                        (detalle.tipo_item !== 'PRODUCTO' || Number.isInteger(cantidadBase));
                });
        },
    
        confirmarEnvioCambio(event) {
            if (
                this.enviandoCambio ||
                !this.modalPagoCambioAbierto ||
                this.cargandoMetodosPagoCambio ||
                this.errorPagoCambio ||
                !this.formularioCambioValido() ||
                !this.pagosCambioValidos()
            ) {
                event.preventDefault();
                return;
            }
    
            this.enviandoCambio = true;
        },
    }"
        class="rounded border border-app-border bg-app-surface p-5 shadow-sm transition-colors"
        :class="devolucionesCambio.length || productosCambio.length || detallesDisponibles.some(detalle => detalle
                .seleccionada) ?
            'border-amber-400' :
            'border-app-border'">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="font-semibold text-lg text-app-text-primary">Cambios y Devoluciones</h2>
            </div>
            @if ($puedeCambiar)
                <button type="button" @click="abrirSelectorCambio()"
                    class="rounded-md bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
                    Cambiar
                </button>
            @else
                <span title="{{ $razonBloqueoCambio }}" class="inline-flex cursor-not-allowed">
                    <button type="button" disabled
                        class="pointer-events-none rounded-md bg-app-border-hover px-4 py-2.5 text-sm font-semibold text-white">
                        Cambiar
                    </button>
                </span>
            @endif
        </div>

        <div x-cloak x-show="devolucionesCambio.length > 0" class="relative mt-4 max-w-xl">

            <label for="buscar-articulo-cambio" class="mb-2 block text-sm font-medium text-app-text-secondary">
                Agregar productos para efectuar el cambio (Opcional)
            </label>
            <div class="flex items-center gap-2">
                <x-lucide-search class="h-5 w-5 shrink-0 text-slate-400" />
                <x-text-input id="buscar-articulo-cambio" type="search" x-model="buscarArticulo"
                    @input.debounce.300ms="buscarArticulosCambio()" placeholder="Buscar por nombre, marca o SKU..."
                    autocomplete="off" />
            </div>
            <div x-cloak x-show="buscarArticulo.trim().length > 0"
                class="absolute z-30 mt-1 max-h-80 w-full overflow-y-auto rounded-xl border border-app-border bg-app-background shadow-lg">
                <p x-show="cargandoBusquedaCambio" class="px-4 py-3 text-sm text-border-focus">Buscando...</p>
                <template x-for="producto in resultadosCambio" :key="producto.presentacion_id">
                    <button type="button" @click="agregarProductoCambio(producto)"
                        class="flex w-full items-center justify-between gap-4 border-b border-app-border px-4 py-3 text-left transition">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="h-12 w-12 shrink-0 overflow-hidden rounded-md">
                                <template x-if="producto.imagen">
                                    <img :src="producto.imagen" :alt="producto.nombre"
                                        class="h-full w-full object-cover">
                                </template>
                                <template x-if="!producto.imagen">
                                    <span class="flex h-full w-full items-center justify-center">
                                        <x-lucide-image class="h-6 w-6 text-app-border-hover" />
                                    </span>
                                </template>
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-[15px] font-semibold text-app-text-primary"
                                    x-text="producto.marca ? `${producto.nombre} - ${producto.marca}` : producto.nombre"></span>
                                <span class="mt-1 block text-xs text-app-text-muted">
                                    SKU: <span x-text="producto.sku"></span>
                                </span>
                            </span>
                        </span>
                        <span class="shrink-0 text-right">
                            <span class="block text-lg font-bold text-app-primary-500">
                                Q <span x-text="Number(producto.precio_unitario_actual).toFixed(2)"></span>
                            </span>
                            <template x-if="producto.tipo_item === 'PRODUCTO'">
                                <span class="mt-1 block text-sm text-app-text-primary">
                                    <span x-text="cantidadDisponible(producto)"></span>
                                    <span
                                        x-text="cantidadDisponible(producto) === 1 ? 'disponible' : 'disponibles'"></span>
                                </span>
                            </template>
                            <template x-if="producto.tipo_item === 'SERVICIO'">
                                <span class="font-medium text-app-primary-700">Servicio</span>
                            </template>
                        </span>
                    </button>
                </template>
                <p x-show="!cargandoBusquedaCambio && !errorBusquedaCambio && resultadosCambio.length === 0"
                    class="px-4 py-3 text-sm text-app-text-muted">No se encontraron productos coincidentes.</p>
                <p x-show="errorBusquedaCambio" x-cloak class="px-4 py-3 text-sm text-red-600">
                    No se pudo realizar la búsqueda. Inténtalo nuevamente.
                </p>
            </div>
        </div>

        <div x-cloak x-show="carritoCambioAbierto" x-transition class="mt-5 space-y-4 ">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-semibold text-app-primary">Artículos de la operación</h3>
                <button type="button" @click="abrirSelectorCambio()"
                    class="inline-flex items-center rounded-lg border border-app-primary-500/25
                    bg-app-primary-500/10 px-3 py-2 text-sm font-medium
                    text-app-primary-700 transition-colors
                    hover:bg-app-primary-500/15 hover:border-app-primary-500/40
                    dark:text-app-primary-300">
                    Cambiar artículos devueltos
                </button>
            </div>

            <form id="formulario-cambio" x-ref="formularioCambio" action="{{ route('ventas.store') }}" method="POST"
                @submit="confirmarEnvioCambio($event)" class="space-y-4">
                @csrf
                <input type="hidden" name="idempotency_key"
                    value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">
                <input type="hidden" name="tipo_operacion" :value="productosCambio.length ? 'CAMBIO' : 'DEVOLUCION'">
                <input type="hidden" name="venta_referencia_id" value="{{ $venta->id }}">

                <template x-for="(detalle, indice) in devolucionesCambio" :key="`dev-${detalle.id}`">
                    <div class="hidden">
                        <input type="hidden" :name="`detalles[${indice}][tipo_movimiento]`" value="DEVOLUCION">
                        <input type="hidden" :name="`detalles[${indice}][detalle_referencia_id]`"
                            :value="detalle.id">
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
                        <input type="hidden" :name="`detalles[${devolucionesCambio.length + indice}][cantidad]`"
                            :value="detalle.cantidad">
                        <input type="hidden" :name="`detalles[${devolucionesCambio.length + indice}][tipo_precio]`"
                            :value="detalle.tipo_precio">
                        <input type="hidden"
                            :name="`detalles[${devolucionesCambio.length + indice}][descuento_adicional]`"
                            :value="Number(detalle.descuento_adicional || 0).toFixed(2)">
                    </div>
                </template>

                <div class="overflow-hidden rounded-xl border border-app-border bg-app-surface shadow-sm">
                    <div class="overflow-x-auto scrollbar-app">
                        <table class="w-full min-w-[1050px] table-fixed border-collapse text-left text-sm">
                            <colgroup>
                                <col class="w-[24%]">
                                <col class="w-[12%]">
                                <col class="w-[17%]">
                                <col class="w-[16%]">
                                <col class="w-[16%]">
                                <col class="w-[15%]">
                            </colgroup>
                            <thead
                                class="bg-app-table-header text-xs font-semibold uppercase tracking-wider text-app-table-header-text">
                                <tr>
                                    <th class="px-3 py-3 text-left">Artículo</th>
                                    <th class="px-3 py-3 text-right">Cantidad</th>
                                    <th class="px-3 py-3 text-right">Precio aplicado</th>
                                    <th class="px-3 py-3 text-right">Descuento extra</th>
                                    <th class="px-3 py-3 text-right">Importe</th>
                                    <th class="px-3 py-3 text-right"></th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-app-border text-app-text-secondary
                                [&_tr]:bg-app-surface
                                [&_tr]:transition-colors [&_tr]:duration-150">
                                <template x-for="detalle in devolucionesCambio" :key="`fila-dev-${detalle.id}`">
                                    <tr class="bg-app-surface transition-colors duration-150">
                                        <td class="px-3 py-2">
                                            <span class="font-normal text-[15px] text-app-text-primary"
                                                x-text="detalle.marca ? `${detalle.nombre} - ${detalle.marca}` : detalle.nombre"></span>
                                            <span class="block text-xs text-rose-600">Devolución</span>
                                        </td>
                                        <td class="px-3 py-2 text-right text-rose-600"
                                            x-text="'-' + Number(detalle.cantidad).toFixed(2)"></td>
                                        <td class="px-3 py-2 text-right text-rose-600"
                                            x-text="'Q -' + Number(detalle.precio_aplicado).toFixed(2)"></td>
                                        <td class="px-3 py-2 text-right text-app-text-muted">—</td>
                                        <td class="px-3 py-2 text-right font-semibold text-rose-600"
                                            x-text="'Q -' + subtotalDevolucion(detalle).toFixed(2)"></td>
                                        <td class="px-3 py-2">
                                            <label x-show="detalle.tipo_item === 'PRODUCTO'"
                                                class="inline-flex items-center gap-2 text-sm text-app-text-secondary">
                                                <input type="checkbox" x-model="detalle.reingresa_inventario"
                                                    class="rounded border-app-border-strong text-app-primary-600 focus:ring-app-primary-500">
                                                Reingresar
                                            </label>
                                            <span x-show="detalle.tipo_item !== 'PRODUCTO'"
                                                class="text-sm text-app-text-muted">
                                                No aplica
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="detalle in productosCambio"
                                    :key="`fila-prod-${detalle.presentacion_id}`">
                                    <tr class="bg-app-surface align-middle transition-colors duration-150">
                                        {{-- Artículo --}}
                                        <td class="px-3 py-2 text-left">
                                            <span class="font-normal text-[15px] text-app-text-primary"
                                                x-text="detalle.marca ? `${detalle.nombre} - ${detalle.marca}` : detalle.nombre"></span>
                                            <span class="block text-xs text-emerald-700">Entrega</span>
                                        </td>

                                        {{-- Cantidad --}}
                                        <td class="px-3 py-2 text-right">
                                            <div class="flex justify-end">
                                                <input type="number" min="1" step="1"
                                                    x-model="detalle.cantidad"
                                                    @input="actualizarPrecioProductoCambio(detalle)"
                                                    class="block w-24 rounded-md border border-app-border-strong
                                                    bg-app-background p-2 text-right text-sm text-app-text-primary
                                                    shadow-sm focus:border-app-primary-500 focus:outline-none
                                                    focus:ring-1 focus:ring-app-primary-500
                                                    focus-visible:border-app-primary-500
                                                    focus-visible:ring-app-primary-500
                                                    [appearance:textfield]
                                                    [&::-webkit-inner-spin-button]:appearance-none
                                                    [&::-webkit-outer-spin-button]:appearance-none" />
                                            </div>
                                        </td>

                                        {{-- Precio aplicado --}}
                                        <td class="px-3 py-2">
                                            <div class="flex items-center justify-end gap-2">


                                                <x-select-input x-model="detalle.tipo_precio" class="w-24 text-sm"
                                                    @change="actualizarPrecioProductoCambio(detalle)">
                                                    <option value="unitario">Unitario</option>
                                                    <option value="docena"
                                                        :disabled="Number(detalle.cantidad) * Number(detalle.factor) < 12 ||
                                                            detalle.precio_docena_actual === null">
                                                        Docena
                                                    </option>
                                                    <option value="ciento"
                                                        :disabled="Number(detalle.cantidad) * Number(detalle.factor) < 100 ||
                                                            detalle.precio_ciento_actual === null">
                                                        Ciento
                                                    </option>
                                                </x-select-input>
                                                <span class="whitespace-nowrap"
                                                    x-text="'Q ' + precioAplicadoCambio(detalle).toFixed(2)">
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Descuento adicional --}}
                                        <td class="px-3 py-2">
                                            <label class="sr-only"
                                                :for="`descuento-cambio-${detalle.presentacion_id}`">
                                                Descuento adicional para <span x-text="detalle.nombre"></span>
                                            </label>
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="text-app-text-muted">Q</span>
                                                <input type="text" inputmode="decimal"
                                                    pattern="[0-9]+([.][0-9]{1,2})?" maxlength="15"
                                                    :id="`descuento-cambio-${detalle.presentacion_id}`"
                                                    :value="detalle.descuento_adicional"
                                                    @input="actualizarDescuentoProductoCambio(detalle, $event)"
                                                    class="w-24 rounded-md border border-app-border-strong bg-app-background p-2 text-right text-sm text-app-text-primary shadow-sm focus:border-app-primary-500 focus:ring-app-primary-500">
                                            </div>
                                        </td>

                                        {{-- Importe --}}
                                        <td class="px-3 py-2 text-right font-semibold"
                                            x-text="'Q ' + subtotalProductoCambio(detalle).toFixed(2)">
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="px-3 py-2 text-left">
                                            <button type="button"
                                                class="text-[15px] font-medium text-rose-600 hover:text-rose-800"
                                                @click="productosCambio = productosCambio.filter(item => item.presentacion_id !== detalle.presentacion_id)">
                                                Quitar
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <p x-show="errorCambio" x-text="errorCambio" role="alert"
                    class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>

                <div class="grid gap-3 lg:grid-cols-[auto_minmax(0,1fr)] lg:items-end">

                    {{-- Diferencia --}}
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-semibold text-app-text-secondary">
                            Diferencia:
                        </p>
                        <span :class="totalCambio() < 0 ? 'text-rose-600' : 'text-app-text-primary'"
                            x-text="'Q ' + totalCambio().toFixed(2)">
                        </span>
                    </div>

                    {{-- Motivo y botón --}}
                    <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                        <div class="flex min-w-0 items-center gap-3">
                            <label for="motivo-cambio" class="shrink-0 text-sm font-medium text-app-text-secondary">
                                Motivo <span class="text-red-600">*</span>
                            </label>

                            <textarea id="motivo-cambio" name="motivo_operacion" x-model="motivoCambio" maxlength="1000" required
                                rows="1"
                                class="block min-w-0 flex-1 rounded-md border border-app-border-strong
                bg-app-background p-2 text-sm text-app-text-primary shadow-sm
                focus:border-app-primary-500 focus:outline-none focus:ring-1
                focus:ring-app-primary-500">
            </textarea>
                        </div>

                        <button type="button" @click="abrirModalPagoCambio()" :disabled="!formularioCambioValido()"
                            class="rounded-md bg-app-primary-700 px-5 py-2.5 text-sm font-semibold
                            text-white hover:bg-app-primary-800
                            disabled:cursor-not-allowed disabled:opacity-50">
                            Continuar al pago o reembolso
                        </button>
                    </div>
                </div>

                <template x-teleport="body">
                    <div x-cloak x-show="modalPagoCambioAbierto" x-transition.opacity
                        @click.self="modalPagoCambioAbierto = false"
                        @keydown.escape.window="modalPagoCambioAbierto = false"
                        class="fixed inset-0 z-100 flex items-center justify-center overflow-y-auto bg-black/40 p-4"
                        role="dialog" aria-modal="true" aria-labelledby="titulo-modal-pago-cambio">
                        <div class="my-auto w-full max-w-3xl overflow-hidden rounded-lg border border-app-border bg-app-surface p-6 shadow-xl"
                            @click.stop>
                            <div class="mb-5 flex items-start justify-between border-b border-app-border pb-3">
                                <div>
                                    <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
                                        <h2 id="titulo-modal-pago-cambio"
                                            class="text-xl font-bold text-app-primary-500 sm:text-xl"
                                            x-text="totalCambio() < 0 ? 'Registrar reembolso' : (totalCambio() > 0 ? 'Registrar cobro' : 'Confirmar operación')">
                                        </h2>
                                        <p class="text-lg font-semibold text-app-text-secondary">
                                            <span
                                                x-text="totalCambio() < 0 ? 'Monto a reembolsar:' : (totalCambio() > 0 ? 'Monto a cobrar:' : 'Diferencia:')"></span>
                                            <strong class="text-xl font-normal text-app-text-primary sm:text-2xl"
                                                x-text="'Q ' + Math.abs(totalCambio()).toFixed(2)"></strong>
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="modalPagoCambioAbierto = false"
                                    aria-label="Volver al carrito"
                                    class="rounded p-1 text-app-text-muted transition hover:bg-app-surface-hover hover:text-app-text-primary">
                                    <x-lucide-x class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="max-h-[70vh] space-y-4 overflow-y-auto">
                                <p x-show="cargandoMetodosPagoCambio" class="text-base text-app-text-muted">
                                    Cargando métodos de pago...
                                </p>
                                <p x-show="errorPagoCambio" x-text="errorPagoCambio" role="alert"
                                    class="rounded-md border border-red-200 bg-red-50 p-3 text-base text-red-700">
                                </p>
                                <p x-show="!cargandoMetodosPagoCambio && totalCambio() === 0"
                                    class="rounded-md border border-app-primary-300 bg-app-primary-100/40 p-3 text-base text-app-primary-800">
                                    La diferencia es cero; la operación quedará liquidada sin movimiento de pago.
                                </p>

                                <fieldset x-show="totalCambio() !== 0" class="space-y-3"
                                    :disabled="cargandoMetodosPagoCambio || !!errorPagoCambio">
                                    <legend class="mb-2 text-base font-semibold text-app-text-primary">
                                        Método para liquidar la diferencia
                                    </legend>
                                    <template x-for="(metodo, indice) in metodosPagoCambio" :key="metodo.id">
                                        <div class="grid grid-cols-1 items-center gap-3 rounded-md border p-3
                                            sm:grid-cols-[100px_minmax(0,1fr)] sm:gap-x-2 border-app-border bg-app-surface">
                                        
                                            <span class="text-base font-semibold text-app-text-primary"
                                                x-text="metodo.nombre">
                                            </span>

                                            <input type="hidden" form="formulario-cambio"
                                                :name="metodo.activo ? `pagos[${indice}][metodo_pago_id]` : null"
                                                :value="metodo.id">

                                            <div class="grid grid-cols-1 gap-2 sm:col-span-1 sm:grid-cols-2">
                                                <template x-if="!metodo.permite_montos">
                                                    <div>
                                                        <label
                                                            class="mb-1 block text-sm font-medium text-app-text-secondary"
                                                            :for="`monto-cambio-${indice}`"
                                                            x-text="totalCambio() < 0 ? 'Monto a reembolsar' : 'Monto a cobrar'"></label>
                                                        <input :id="`monto-cambio-${indice}`" type="text"
                                                            inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                                            maxlength="15" :value="metodo.monto"
                                                            @focus="activarMetodoPagoCambio(metodo)"
                                                            @input="actualizarImportePagoCambio(metodo, 'monto', $event)"
                                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-right text-base text-app-text-primary shadow-sm focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                                        <input type="hidden" form="formulario-cambio"
                                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                                            :value="montoAplicadoPagoCambio(metodo).toFixed(2)">
                                                    </div>
                                                </template>

                                                <template x-if="metodo.permite_montos && totalCambio() > 0">
                                                    <div>
                                                        <label
                                                            class="mb-1 block text-sm font-medium text-app-text-secondary"
                                                            :for="`monto-recibido-cambio-${indice}`">Monto
                                                            recibido</label>
                                                        <input :id="`monto-recibido-cambio-${indice}`" type="text"
                                                            inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                                            maxlength="15" form="formulario-cambio"
                                                            :name="metodo.activo ? `pagos[${indice}][monto_recibido]` :
                                                                null"
                                                            :value="metodo.monto_recibido"
                                                            @focus="activarMetodoPagoCambio(metodo)"
                                                            @input="actualizarImportePagoCambio(metodo, 'monto_recibido', $event)"
                                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-right text-base text-app-text-primary shadow-sm focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                                        <input type="hidden" form="formulario-cambio"
                                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                                            :value="montoAplicadoPagoCambio(metodo).toFixed(2)">
                                                    </div>
                                                </template>

                                                <template x-if="metodo.permite_montos && totalCambio() < 0">
                                                    <div>
                                                        <label
                                                            class="mb-1 block text-sm font-medium text-app-text-secondary"
                                                            :for="`monto-reembolso-cambio-${indice}`">Monto a
                                                            reembolsar</label>
                                                        <input :id="`monto-reembolso-cambio-${indice}`" type="text"
                                                            inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?"
                                                            maxlength="15" :value="metodo.monto"
                                                            @focus="activarMetodoPagoCambio(metodo)"
                                                            @input="actualizarImportePagoCambio(metodo, 'monto', $event)"
                                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-right text-base text-app-text-primary shadow-sm focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                                        <input type="hidden" form="formulario-cambio"
                                                            :name="metodo.activo ? `pagos[${indice}][monto]` : null"
                                                            :value="montoAplicadoPagoCambio(metodo).toFixed(2)">
                                                    </div>
                                                </template>

                                                <template x-if="metodo.permite_montos && totalCambio() > 0">
                                                    <div>
                                                        <span
                                                            class="mb-1 block text-sm font-medium text-app-text-secondary">Cambio</span>
                                                        <div class="rounded-md border border-app-border bg-app-surface-alt px-3 py-2 text-right text-base text-app-text-primary"
                                                            x-text="'Q ' + Math.max(0, Number(metodo.monto_recibido || 0) - montoAplicadoPagoCambio(metodo)).toFixed(2)">
                                                        </div>
                                                    </div>
                                                </template>

                                                <template x-if="!metodo.permite_montos">
                                                    <div>
                                                        <label
                                                            class="mb-1 block text-sm font-medium text-app-text-secondary"
                                                            :for="`referencia-cambio-${indice}`">Referencia
                                                            (opcional)</label>
                                                        <input :id="`referencia-cambio-${indice}`" type="text"
                                                            maxlength="100" form="formulario-cambio"
                                                            :name="metodo.activo ? `pagos[${indice}][referencia]` : null"
                                                            x-model="metodo.referencia"
                                                            class="block w-full rounded-md border border-app-border-strong bg-app-background p-2 text-base text-app-text-primary shadow-sm focus:outline-none focus:ring-1 focus:border-app-primary-500 focus:ring-app-primary-500 focus-visible:border-app-primary-500 focus-visible:ring-app-primary-500">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <p x-show="!cargandoMetodosPagoCambio && metodosPagoCambio.length === 0"
                                        class="text-base text-app-text-muted">No hay métodos de pago activos.</p>
                                </fieldset>

                                <p class="text-base font-semibold text-app-text-secondary" aria-live="polite">
                                    <span x-text="totalCambio() < 0 ? 'Reembolso:' : 'Cobro:'"></span>
                                    Q <span
                                        x-text="Math.abs(metodosPagoCambio
                                    .filter(metodo => metodo.activo)
                                    .reduce((suma, metodo) => suma + montoAplicadoPagoCambio(metodo), 0)).toFixed(2)"></span>
                                </p>
                            </div>

                            <div
                                class="mt-4 flex flex-col-reverse gap-3 border-t border-app-border pt-4 sm:flex-row sm:justify-end">
                                <button type="button" @click="modalPagoCambioAbierto = false"
                                    class="rounded-md border border-app-border-strong px-4 py-2 text-base font-medium text-app-text-secondary transition hover:bg-app-surface-hover">
                                    Volver al carrito
                                </button>
                                <button type="submit" form="formulario-cambio"
                                    :disabled="enviandoCambio || cargandoMetodosPagoCambio || !!errorPagoCambio || !
                                        pagosCambioValidos()"
                                    class="rounded-md bg-app-primary-700 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-app-primary-800 disabled:cursor-not-allowed disabled:opacity-50">
                                    <span
                                        x-text="enviandoCambio
                                    ? 'Registrando...'
                                    : (totalCambio() < 0
                                        ? 'Registrar operación y reembolso'
                                        : (totalCambio() > 0 ? 'Registrar operación y cobro' : 'Registrar operación'))"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </form>
        </div>

        <!-- MODAL DE SELECCIÓN DE PRODUCTOS A CAMBIAR -->
        <div x-cloak x-show="mostrarModalCambio" x-transition.opacity @click.self="mostrarModalCambio = false"
            @keydown.escape.window="mostrarModalCambio = false"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4" role="dialog"
            aria-modal="true" aria-labelledby="titulo-modal-cambio">
            <div
                class="my-auto w-full max-w-3xl overflow-hidden rounded-xl border border-app-border bg-app-surface shadow-2xl">
                <div class="flex items-start justify-between border-b border-app-border px-5 py-4 sm:px-6">
                    <div>
                        <h2 id="titulo-modal-cambio" class="text-lg font-semibold text-app-text-primary">
                            Seleccionar artículos para devolver
                        </h2>
                        <p class="mt-1 text-sm text-app-text-muted">Solo puedes devolver cantidades aún disponibles de
                            la
                            venta.</p>
                    </div>
                    <button type="button" @click="mostrarModalCambio = false" aria-label="Cerrar"
                        class="rounded p-1 text-app-text-muted hover:bg-app-surface-hover hover:text-app-text-primary">
                        <x-lucide-x class="h-5 w-5" />
                    </button>
                </div>

                <div class="max-h-[65vh] space-y-3 overflow-y-auto px-5 py-5 sm:px-6">
                    <template x-for="detalle in detallesDisponibles" :key="detalle.id">
                        <label
                            class="flex flex-col gap-3 rounded-lg border border-app-border p-3 sm:flex-row sm:items-center">
                            <input type="checkbox" x-model="detalle.seleccionada"
                                class="rounded border-app-border-strong text-app-primary-600 focus:ring-app-primary-500">
                            <span class="min-w-0 flex-1">
                                <span class="block font-medium text-[15px] text-app-text-primary"
                                    x-text="detalle.marca ? `${detalle.nombre} - ${detalle.marca}` : detalle.nombre"></span>
                                <span class="block text-sm text-app-text-muted">
                                    Disponible:
                                    <span x-text="Number(detalle.cantidad_disponible).toFixed(2)"></span>
                                    <span x-show="detalle.sku" x-text="' · SKU: ' + detalle.sku"></span>
                                </span>
                            </span>
                            <span class="inline-flex items-center gap-1" @click.stop>
                                <button type="button" @click="ajustarCantidadDevolucion(detalle, -1)"
                                    :disabled="!detalle.seleccionada || Number(detalle.cantidad) <= 0"
                                    aria-label="Disminuir cantidad"
                                    class="flex h-9 w-9 items-center justify-center rounded-md border border-app-border-strong text-lg font-semibold text-app-text-secondary hover:bg-app-surface-hover disabled:cursor-not-allowed disabled:opacity-40">
                                    -
                                </button>
                                <input type="text" inputmode="decimal" x-model="detalle.cantidad"
                                    :disabled="!detalle.seleccionada"
                                    @input="actualizarCantidadDevolucion(detalle, $event)"
                                    class="w-20 py-2.5 rounded-md border border-app-border-strong bg-app-background text-center text-sm text-app-text-primary disabled:bg-app-surface-alt"
                                    aria-label="Cantidad a devolver">
                                <button type="button" @click="ajustarCantidadDevolucion(detalle, 1)"
                                    :disabled="Number(detalle.cantidad || 0) >= Number(detalle.cantidad_disponible)"
                                    aria-label="Aumentar cantidad"
                                    class="flex h-9 w-9 items-center justify-center rounded-md border border-app-border-strong text-lg font-semibold text-app-text-secondary hover:bg-app-surface-hover disabled:cursor-not-allowed disabled:opacity-40">
                                    +
                                </button>
                            </span>
                        </label>
                    </template>
                    <p x-show="errorCambio" x-text="errorCambio" role="alert"
                        class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-app-border px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" @click="mostrarModalCambio = false"
                        class="rounded-md border border-app-border-strong px-4 py-2.5 text-sm font-semibold text-app-text-secondary hover:bg-app-surface-hover">
                        Cancelar
                    </button>
                    <button type="button" @click="agregarDevoluciones()"
                        class="rounded-md bg-app-primary-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-app-primary-800">
                        Continuar al carrito
                    </button>
                </div>
            </div>
        </div>
    </section>
@endif
