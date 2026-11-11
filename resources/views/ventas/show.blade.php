<x-app-layout>
    <div class="space-y-8">
        <section class="overflow-hidden bg-white shadow-sm">
            <!-- MENSAJES -->
            <x-alert-message />
            <div
                class="flex flex-col justify-between gap-4 border-b border-slate-100 px-5 py-3 sm:flex-row sm:items-start sm:px-6">
                <div class="min-w-0">
                    <p class="mb-1 text-xs font-medium text-slate-500">Número de venta</p>
                    <h1 class="break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        {{ $venta->numero_venta }}
                    </h1>
                </div>
                <div class="grid shrink-0 grid-cols-2 gap-3 text-sm lg:min-w-[280px]">
                    <x-detail-field label="Creada en" class="bg-slate-50">
                        <span class="tabular-nums">{{ $venta->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </x-detail-field>
                    <x-detail-field label="Última edición" class="bg-slate-50">
                        <span class="tabular-nums">{{ $venta->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </x-detail-field>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2 px-5 sm:grid-cols-2 xl:grid-cols-3 sm:px-6">
                <x-detail-field label="Tipo de operación">
                    {{ ucfirst(strtolower($venta->tipo_operacion)) }}
                </x-detail-field>
                <x-detail-field label="Estado de venta">
                    {{ ucfirst(strtolower($venta->estado_venta)) }}
                </x-detail-field>
                <x-detail-field label="Estado de pago">
                    {{ $estadoPago ? ucfirst(strtolower($estadoPago)) : 'No aplica' }}
                </x-detail-field>
                <x-detail-field label="Vendedor">
                    {{ $venta->vendedor?->nombres ?? '—' }}
                    @if ($venta->vendedor?->apellidos)
                        {{ $venta->vendedor->apellidos }}
                    @endif
                    @if ($venta->vendedor?->username)
                        <span class="block text-xs text-slate-500">{{ '@' . $venta->vendedor->username }}</span>
                    @endif
                </x-detail-field>
                <x-detail-field label="Cliente">
                    @if ($venta->cliente)
                        {{ trim($venta->cliente->nombres . ' ' . $venta->cliente->apellidos) }}
                        <span class="block text-xs text-slate-500">
                            NIT: {{ $venta->cliente->nit }}
                            @if ($venta->cliente->clasificacion)
                                · {{ $venta->cliente->clasificacion }}
                            @endif
                        </span>
                        @if ($venta->cliente->telefono || $venta->cliente->correo)
                            <span class="block text-xs text-slate-500">
                                {{ $venta->cliente->telefono ?? '' }}
                                @if ($venta->cliente->telefono && $venta->cliente->correo)
                                    ·
                                @endif
                                {{ $venta->cliente->correo ?? '' }}
                            </span>
                        @endif
                    @else
                        Venta sin cliente asociado
                    @endif
                </x-detail-field>
                <x-detail-field label="Venta de referencia">
                    @if ($venta->ventaReferencia)
                        <a href="{{ route('ventas.show', $venta->ventaReferencia) }}"
                            class="font-medium text-indigo-700 hover:text-indigo-900">
                            {{ $venta->ventaReferencia->numero_venta }}
                        </a>
                    @else
                        —
                    @endif
                </x-detail-field>
            </div>
        </section>

        <section class="space-y-3">
            <x-section-title>Artículos de la venta</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="whitespace-nowrap px-4 py-3">Artículo</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Cantidad</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio unitario</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio aplicado</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Descuento</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Subtotal</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Costo</th>
                </x-slot:headers>

                @forelse ($venta->detalles as $detalle)
                    @php
                        $signoDetalle = $detalle->tipo_movimiento === 'DEVOLUCION' ? -1 : 1;
                        $nombreProducto = $detalle->presentacion?->nombre ?? 'Presentación no disponible';
                        $marcaProducto = $detalle->presentacion?->inventario?->marca?->nombre;
                        $nombreProductoCompleto = $marcaProducto
                            ? "{$nombreProducto} - {$marcaProducto}"
                            : $nombreProducto;
                    @endphp
                    <tr class="transition-colors hover:bg-indigo-50/50">
                        <td class="px-4 py-3">
                            <div x-data="{
                                copiado: false,
                                errorCopia: false,
                                async copiar(texto) {
                                    try {
                                        await navigator.clipboard.writeText(texto);
                                        this.copiado = true;
                                    } catch (error) {
                                        this.errorCopia = true;
                                    }

                                    setTimeout(() => {
                                        this.copiado = false;
                                        this.errorCopia = false;
                                    }, 1500);
                                }
                            }" class="group inline-flex max-w-full flex-wrap items-center gap-1">
                                <span class="font-semibold text-slate-900">{{ $nombreProductoCompleto }}</span>
                                <button type="button" @click="copiar($el.dataset.copyText)"
                                    data-copy-text="{{ $nombreProductoCompleto }}"
                                    :aria-label="errorCopia ? 'No se pudo copiar' : copiado ? 'Copiado' : 'Copiar nombre y marca'"
                                    :title="errorCopia ? 'No se pudo copiar' : copiado ? 'Copiado' : 'Copiar nombre y marca'"
                                    class="inline-flex rounded p-1 text-slate-400 opacity-0 transition hover:bg-indigo-100 hover:text-indigo-700 focus:opacity-100 group-hover:opacity-100">
                                    <x-lucide-copy x-show="!copiado && !errorCopia" class="h-4 w-4" />
                                    <x-lucide-check x-show="copiado" class="h-4 w-4 text-green-600" />
                                    <x-lucide-circle-alert x-show="errorCopia" class="h-4 w-4 text-red-600" />
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $detalle->tipo_item }}
                                @if ($detalle->tipo_movimiento === 'DEVOLUCION')
                                    <span class="mx-1">·</span>
                                    Devolución
                                @elseif ($detalle->tipo_movimiento === 'ENTREGA_CAMBIO')
                                    <span class="mx-1">·</span>
                                    Entrega de cambio
                                @endif
                                <span class="mx-1">·</span>
                                SKU: {{ $detalle->presentacion?->codigoSKU ?? '—' }}
                            </p>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ number_format($signoDetalle * (float) $detalle->cantidad, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format($signoDetalle * (float) $detalle->precio_unitario, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format($signoDetalle * (float) $detalle->precio_aplicado, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format($signoDetalle * (float) $detalle->descuento, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">
                            Q {{ number_format($signoDetalle * (float) $detalle->subtotal, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->subtotal_costo !== null
                                ? 'Q ' . number_format($signoDetalle * (float) $detalle->subtotal_costo, 2)
                                : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                            Esta venta no tiene artículos registrados.
                        </td>
                    </tr>
                @endforelse
            </x-table>
            @if (
                $venta->estado_venta === 'REGISTRADA' &&
                    in_array($venta->tipo_operacion, ['CAMBIO', 'DEVOLUCION'], true) &&
                    filled($venta->motivo_operacion))
                <div class="rounded border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-900">
                        {{ $venta->tipo_operacion === 'DEVOLUCION' ? 'Motivo de la devolución' : 'Motivo del cambio' }}
                    </p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $venta->motivo_operacion }}</p>
                </div>
            @endif
        </section>

        @include('ventas.partials._modal-cambio')

        <section class="space-y-3">
            <x-section-title>Movimientos de pago</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="whitespace-nowrap px-4 py-3">Método de pago</th>
                    <th class="whitespace-nowrap px-4 py-3">Fecha</th>
                    <th class="whitespace-nowrap px-4 py-3">Referencia</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Monto aplicado</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Monto recibido</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Cambio</th>
                </x-slot:headers>

                @forelse ($venta->detallesPago as $pago)
                    <tr class="transition-colors hover:bg-indigo-50/50">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ $pago->metodoPago?->nombre ?? 'Método no disponible' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            {{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">{{ $pago->referencia ?: '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format((float) $pago->monto, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $pago->monto_recibido !== null ? 'Q ' . number_format((float) $pago->monto_recibido, 2) : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $pago->cambio !== null ? 'Q ' . number_format((float) $pago->cambio, 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            Esta operación aún no tiene movimientos de pago registrados.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </section>

        @if ($venta->operacionesRelacionadas->isNotEmpty())
            <section class="space-y-3">
                <x-section-title>Operaciones relacionadas</x-section-title>
                <x-table>
                    <x-slot:headers>
                        <th class="whitespace-nowrap px-4 py-3">Número</th>
                        <th class="whitespace-nowrap px-4 py-3">Operación</th>
                        <th class="whitespace-nowrap px-4 py-3">Estado</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Total</th>
                    </x-slot:headers>

                    @foreach ($venta->operacionesRelacionadas as $operacion)
                        @php($estadoPagoOperacion = $operacion->total < 0 && $operacion->estado_pago === 'PAGADO' ? 'REEMBOLSADO' : $operacion->estado_pago)
                        <tr class="transition-colors hover:bg-indigo-50/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('ventas.show', $operacion) }}"
                                    class="font-medium text-indigo-700 hover:text-indigo-900">
                                    {{ $operacion->numero_venta }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ ucfirst(strtolower($operacion->tipo_operacion)) }}</td>
                            <td class="px-4 py-3">
                                {{ ucfirst(strtolower($operacion->estado_venta)) }}
                                <span class="text-xs text-slate-500">
                                    ·
                                    {{ $estadoPagoOperacion ? ucfirst(strtolower($estadoPagoOperacion)) : 'No aplica' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                Q {{ number_format((float) $operacion->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </section>
        @endif

        <section x-data="{
            modalPagoAbierto: false,
            cargandoMetodosPago: false,
            errorPago: '',
            metodosPago: [],
            saldoPendiente: {{ json_encode($saldoPendiente) }},
        
            async abrirModalPago() {
                this.modalPagoAbierto = true;
                this.cargandoMetodosPago = true;
                this.errorPago = '';
        
                try {
                    const respuesta = await fetch('{{ route('ventas.metodos-pago') }}', {
                        headers: { Accept: 'application/json' },
                    });
                    if (!respuesta.ok) throw new Error('No se pudieron cargar los métodos de pago.');
        
                    const metodos = await respuesta.json();
                    this.metodosPago = metodos.map(metodo => ({
                        ...metodo,
                        permite_montos: metodo.tipo_captura === 'PERMITIR MONTOS',
                        activo: false,
                        monto: '',
                        monto_recibido: '',
                        referencia: '',
                    }));
                } catch (error) {
                    this.errorPago = error.message;
                } finally {
                    this.cargandoMetodosPago = false;
                }
            },
        
            activarMetodo(metodo) {
                metodo.activo = true;
                const montoDisponible = this.montoDisponiblePara(metodo);
                if (metodo.permite_montos && this.saldoPendiente > 0 && !Number(metodo.monto_recibido)) {
                    metodo.monto_recibido = montoDisponible.toFixed(2);
                } else if ((!metodo.permite_montos || this.saldoPendiente < 0) && !Number(metodo.monto)) {
                    metodo.monto = montoDisponible.toFixed(2);
                }
            },
        
            montoAplicado(metodo) {
                let montoAsignado = 0;
                const saldo = Math.abs(this.saldoPendiente);
        
                for (const pago of this.metodosPago.filter(item => item.activo)) {
                    const montoIngresado = pago.permite_montos && this.saldoPendiente > 0 ?
                        Number(pago.monto_recibido) || 0 :
                        Number(pago.monto) || 0;
                    const montoAplicado = Math.min(
                        montoIngresado,
                        Math.max(0, saldo - montoAsignado)
                    );
        
                    if (pago === metodo) {
                        return this.saldoPendiente < 0 ? -montoAplicado : montoAplicado;
                    }
        
                    montoAsignado += montoAplicado;
                }
        
                return 0;
            },
        
            montoDisponiblePara(metodoActual) {
                let montoAsignado = 0;
                const otrosPagos = this.metodosPago
                    .filter(metodo => metodo !== metodoActual && metodo.activo);
        
                for (const metodo of otrosPagos) {
                    const montoIngresado = metodo.permite_montos && this.saldoPendiente > 0 ?
                        Number(metodo.monto_recibido) || 0 :
                        Number(metodo.monto) || 0;
                    montoAsignado += Math.min(
                        montoIngresado,
                        Math.max(0, Math.abs(this.saldoPendiente) - montoAsignado)
                    );
                }
        
                return Math.max(0, Math.round((Math.abs(this.saldoPendiente) - montoAsignado) * 100) / 100);
            },
        
            actualizarImporte(metodo, campo, event) {
                const valor = event.target.value;
                const [entero, ...decimales] = valor.replace(/[^\d.]/g, '').split('.');
                const valorValidado = decimales.length ?
                    `${entero}.${decimales.join('').slice(0, 2)}` :
                    entero;
        
                metodo[campo] = valorValidado;
                event.target.value = valorValidado;
                metodo.activo = true;
            },
        
            pagosValidos() {
                const pagos = this.metodosPago.filter(metodo => metodo.activo);
                const total = pagos.reduce((suma, metodo) => suma + Math.abs(this.montoAplicado(metodo)), 0);
        
                return pagos.length > 0 &&
                    pagos.every(metodo => {
                        const recibeEfectivo = metodo.permite_montos && this.saldoPendiente > 0;
                        const montoIngresado = recibeEfectivo ? metodo.monto_recibido : metodo.monto;
                        return /^\d+(\.\d{1,2})?$/.test(String(montoIngresado || '')) &&
                            Math.abs(this.montoAplicado(metodo)) > 0 &&
                            (recibeEfectivo ||
                                Number(montoIngresado) === Math.abs(this.montoAplicado(metodo))) &&
                            (!recibeEfectivo || Number(metodo.monto_recibido) >= Math.abs(this.montoAplicado(metodo)));
                    }) &&
                    Math.round(total * 100) <= Math.round(Math.abs(this.saldoPendiente) * 100);
            },
        }"
            class="flex flex-col gap-2 rounded border border-slate-200 bg-white p-5 shadow-sm sm:ml-auto sm:w-96">
            <div class="flex justify-between gap-4 text-sm text-slate-600">
                <span>Total de productos</span>
                <span>Q {{ number_format($totalProductos, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 text-sm text-slate-600">
                <span>Total de descuentos</span>
                <span>Q {{ number_format((float) $venta->total_descuentos, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 border-t border-slate-200 pt-3 text-lg font-bold text-slate-900">
                <span>Total de la operación</span>
                <span>Q {{ number_format((float) $venta->total, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 text-sm font-semibold text-slate-700">
                <span>{{ match ($venta->tipo_operacion) {
                    'DEVOLUCION' => 'Reembolsado',
                    'CAMBIO' => $venta->total < 0 ? 'Reembolsado' : ($venta->total > 0 ? 'Cobrado' : 'Diferencia liquidada'),
                    default => 'Cobrado',
                } }}</span>
                <span>Q {{ number_format($montoPagado, 2) }}</span>
            </div>

            @if ($saldoPendiente !== 0.0 && $venta->estado_venta === 'REGISTRADA')
                <button type="button" @click="abrirModalPago()"
                    class="mt-2 rounded-md bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
                    {{ $saldoPendiente < 0 ? 'Registrar reembolso' : 'Completar cobro' }}
                </button>
                @include('ventas.partials._modal-completar-pago')
            @endif
        </section>

        <div class="flex justify-end gap-3">
            @if (
                $venta->tipo_operacion === 'VENTA' &&
                    $venta->estado_venta === 'REGISTRADA' &&
                    $venta->operacionesRelacionadas->isEmpty())
                @if ($puedeCambiarOAnular)
                    <form action="{{ route('ventas.anular', $venta) }}" method="POST"
                        onsubmit="return confirm('¿Anular esta venta? Se devolverá el stock de sus productos al inventario.');">
                        @csrf
                        <x-danger-button type="submit">Anular venta</x-danger-button>
                    </form>
                @else
                    <x-danger-button type="button" disabled class="cursor-not-allowed opacity-50"
                        title="El plazo de 5 días hábiles, excluyendo domingos, venció.">
                        Anular venta
                    </x-danger-button>
                @endif
            @endif
            <x-secondary-link href="{{ route('ventas.index') }}">Volver a ventas</x-secondary-link>
        </div>
    </div>
</x-app-layout>
