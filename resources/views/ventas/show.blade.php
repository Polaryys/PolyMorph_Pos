<x-app-layout>
    <div class="space-y-8">
        <section class="overflow-hidden">
            <!-- MENSAJES -->
            <x-alert-message />
            <div class="flex flex-col justify-between gap-4 px-5 py-3 sm:flex-row sm:items-start sm:px-6">
                <div class="min-w-0">
                    <p class="mb-1 text-xs font-medium text-app-text-muted">
                        Número de venta
                    </p>

                    <h1
                        class="wrap-break-word text-2xl font-bold tracking-tight
               text-app-text-primary sm:text-3xl">
                        {{ $venta->numero_venta }}
                    </h1>
                </div>
                <div class="grid shrink-0 grid-cols-2 gap-3 text-sm lg:min-w-[280px]">

                    <x-detail-item label="Creada en">
                        {{ $venta->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </x-detail-item>

                    <x-detail-item label="Última edición">
                        {{ $venta->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </x-detail-item>

                </div>
            </div>


            <div class="grid grid-cols-1 gap-3 px-5 sm:grid-cols-2
            xl:grid-cols-3 sm:px-6">

                {{-- Tipo de operación --}}
                <x-detail-item label="Tipo de operación">
                    @php
                        $tipoOperacion =
                            [
                                'VENTA' => 'indigo',
                                'DEVOLUCION' => 'amber',
                                'CAMBIO' => 'blue',
                            ][$venta->tipo_operacion] ?? 'slate';
                    @endphp

                    <span @class([
                        'inline-flex items-center rounded-md border px-2.5 py-1 text-sm font-normal',
                        'bg-app-primary-500/10 text-app-primary-600 border-app-primary-600/20 dark:text-app-primary-400' =>
                            $tipoOperacion === 'indigo',
                        'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:text-amber-300' =>
                            $tipoOperacion === 'amber',
                        'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:text-blue-300' =>
                            $tipoOperacion === 'blue',
                        'bg-slate-500/10 text-slate-700 border-slate-500/20 dark:text-slate-300' =>
                            $tipoOperacion === 'slate',
                    ])>
                        {{ ucfirst(strtolower($venta->tipo_operacion)) }}
                    </span>
                </x-detail-item>

                {{-- Estado de venta --}}
                <x-detail-item label="Estado de venta">
                    <span @class([
                        'inline-flex items-center rounded-md border px-2.5 py-1 text-sm font-normal',
                        'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:text-emerald-400' =>
                            $venta->estado_venta === 'REGISTRADA',
                        'bg-red-500/10 text-red-700 border-red-500/20 dark:text-red-300' =>
                            $venta->estado_venta === 'ANULADA',
                    ])>
                        {{ ucfirst(strtolower($venta->estado_venta)) }}
                    </span>
                </x-detail-item>

                {{-- Estado de pago --}}
                <x-detail-item label="Estado de pago">
                    @php
                        $estadoPago =
                            [
                                'PENDIENTE' => [
                                    'label' => 'Pendiente',
                                    'classes' =>
                                        'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:text-amber-400',
                                ],
                                'PARCIAL' => [
                                    'label' => 'Parcial',
                                    'classes' => 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:text-blue-400',
                                ],
                                'PAGADO' => [
                                    'label' => 'Pagado',
                                    'classes' =>
                                        'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:text-emerald-400',
                                ],
                                'REEMBOLSADO' => [
                                    'label' => 'Reembolsado',
                                    'classes' =>
                                        'bg-violet-500/10 text-violet-700 border-violet-500/20 dark:text-violet-400',
                                ],
                            ][$venta->estado_pago] ?? null;
                    @endphp

                    @if ($estadoPago)
                        <span @class([
                            'inline-flex items-center rounded-md border px-2.5 py-1 text-sm font-normal',
                            $estadoPago['classes'],
                        ])>
                            {{ $estadoPago['label'] }}
                        </span>
                    @else
                        <span class="text-sm font-normal text-app-text-muted">
                            No aplica
                        </span>
                    @endif
                </x-detail-item>

                {{-- Vendedor --}}
                <x-detail-item label="Vendedor">
                    <div class="text-sm font-normal">
                        {{ $venta->vendedor?->nombres ?? '—' }}
                        {{ $venta->vendedor?->apellidos }}

                        @if ($venta->vendedor?->username)
                            <span class="mt-1 block text-sm font-normal text-app-text-muted">
                                {{ '@' . $venta->vendedor->username }}
                            </span>
                        @endif
                    </div>
                </x-detail-item>

                {{-- Cliente --}}
                <x-detail-item label="Cliente">
                    @if ($venta->cliente)
                        <div class="text-sm font-normal">
                            <div class="flex flex-wrap items-center gap-x-2">
                                <span>
                                    {{ trim($venta->cliente->nombres . ' ' . $venta->cliente->apellidos) }}
                                </span>

                                <span class="text-sm font-normal text-app-text-muted">
                                    NIT: {{ $venta->cliente->nit }}
                                </span>
                            </div>

                            @if ($venta->cliente->clasificacion || $venta->cliente->telefono || $venta->cliente->correo)
                                <div
                                    class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-normal text-app-text-muted">

                                    @if ($venta->cliente->clasificacion)
                                        <span>{{ $venta->cliente->clasificacion }}</span>
                                    @endif

                                    @if ($venta->cliente->telefono)
                                        <span>{{ $venta->cliente->telefono }}</span>
                                    @endif

                                    @if ($venta->cliente->correo)
                                        <span class="break-all">{{ $venta->cliente->correo }}</span>
                                    @endif

                                </div>
                            @endif
                        </div>
                    @else
                        <span class="font-normal text-app-text-secondary">
                            Venta sin cliente asociado
                        </span>
                    @endif
                </x-detail-item>

                {{-- Venta de referencia --}}
                <x-detail-item label="Venta de referencia">
                    @if ($venta->ventaReferencia)
                        <a href="{{ route('ventas.show', $venta->ventaReferencia) }}" wire:navigate
                            class="font-semibold text-app-primary-700
                      hover:text-app-primary-900
                      dark:text-app-primary-400
                      dark:hover:text-app-primary-100
                      transition-colors">
                            {{ $venta->ventaReferencia->numero_venta }}
                        </a>
                    @else
                        <span class="font-normal text-app-text-muted">—</span>
                    @endif
                </x-detail-item>

            </div>

        </section>

        <section class="space-y-3">
            <x-section-title>Artículos de la venta</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="whitespace-nowrap px-3 py-3 text-center">Movimiento</th>
                    <th class="whitespace-nowrap px-3 py-3 text-center w-16">Tipo</th>
                    <th class="whitespace-nowrap px-3 py-3 text-center">SKU</th>
                    <th class="whitespace-nowrap px-3 py-3">Artículo</th>
                    <th class="whitespace-nowrap px-3 py-3 text-center">Cantidad</th>
                    <th class="whitespace-nowrap px-3 py-3 text-center">Precio unitario</th>
                    <th class="whitespace-nowrap px-3 py-3 text-center">Descuento</th>
                    <th class="whitespace-nowrap px-3 py-3 text-center">Subtotal</th>
                    @if (auth()->user()->rol === 'Administrador')
                        <th class="whitespace-nowrap px-3 py-3 text-center">Costo</th>
                    @endif
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

                    <tr class="transition-color">
                        <td class="text-center">
                            @if ($detalle->tipo_movimiento === 'DEVOLUCION')
                                Devuelto
                            @elseif ($detalle->tipo_movimiento === 'ENTREGA_CAMBIO')
                                Entrega de cambio
                            @elseif ($detalle->tipo_movimiento === 'VENTA')
                                Entregado
                            @endif

                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center w-16">
                            {{ $detalle->tipo_item }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ $detalle->presentacion?->codigoSKU ?? '—' }}
                        </td>
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
                            }"
                                class="group inline-flex max-w-full flex-wrap items-center gap-1">
                                <span class="font-normal text-[15px]">{{ $nombreProductoCompleto }}</span>
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

                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ number_format($signoDetalle * (float) $detalle->cantidad, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            Q {{ number_format($signoDetalle * (float) $detalle->precio_unitario, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            Q {{ number_format($signoDetalle * (float) $detalle->descuento, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center font-semibold ">
                            Q {{ number_format($signoDetalle * (float) $detalle->subtotal, 2) }}
                        </td>
                        @if (auth()->user()->rol === 'Administrador')
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                {{ $detalle->subtotal_costo !== null
                                    ? 'Q ' . number_format($signoDetalle * (float) $detalle->subtotal_costo, 2)
                                    : '—' }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->rol === 'Administrador' ? 8 : 7 }}"
                            class="px-6 py-8 text-center text-app-border-hover">
                            Esta venta no tiene artículos registrados.
                        </td>
                    </tr>
                @endforelse
            </x-table>
            @if (
                $venta->estado_venta === 'REGISTRADA' &&
                    in_array($venta->tipo_operacion, ['CAMBIO', 'DEVOLUCION'], true) &&
                    filled($venta->motivo_operacion))
                <div
                    class="rounded-lg border border-amber-500/25
                bg-amber-500/10 p-4
                dark:border-amber-400/20
                dark:bg-amber-400/10">

                    <p class="text-sm font-semibold text-amber-800
                  dark:text-amber-300">
                        {{ $venta->tipo_operacion === 'DEVOLUCION' ? 'Motivo de la devolución' : 'Motivo del cambio' }}
                    </p>

                    <p
                        class="mt-1 whitespace-pre-line text-sm
                  text-amber-950/80
                  dark:text-amber-100/80">
                        {{ $venta->motivo_operacion }}
                    </p>

                </div>
            @endif
        </section>

        @include('ventas.partials._modal-cambio')

        <section class="space-y-3">
            <x-section-title>Movimientos de pago</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="whitespace-nowrap px-4 py-3 text-center w-24">Método de pago</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Fecha</th>
                    <th class="whitespace-nowrap px-4 py-3">Referencia</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Monto aplicado</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Monto recibido</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Cambio</th>
                </x-slot:headers>

                @forelse ($venta->detallesPago as $pago)
                    <tr class="transition-colors">
                        <td class="px-4 py-3 font-medium text-center">
                            {{ $pago->metodoPago?->nombre ?? 'Método no disponible' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">{{ $pago->referencia ?: '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            Q {{ number_format((float) $pago->monto, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ $pago->monto_recibido !== null ? 'Q ' . number_format((float) $pago->monto_recibido, 2) : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ $pago->cambio !== null ? 'Q ' . number_format((float) $pago->cambio, 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
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
                                    class="font-medium text-app-primary-600 hover:text-app-primary-500">
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
            class="flex flex-col gap-2 rounded border border-app-border p-5 shadow-sm sm:ml-auto sm:w-96">
            <div class="flex justify-between gap-4 text-sm">
                <span>Total de productos</span>
                <span>Q {{ number_format($totalProductos, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 text-sm">
                <span>Total de descuentos</span>
                <span>Q {{ number_format((float) $venta->total_descuentos, 2) }}</span>
            </div>
            <div
                class="flex justify-between gap-4 border-t border-app-border pt-3 text-lg font-bold text-app-text-primary">
                <span>Total de la operación</span>
                <span>Q {{ number_format((float) $venta->total, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 text-sm font-semibold text-app-text-secondary">
                <span>{{ match ($venta->tipo_operacion) {
                    'DEVOLUCION' => 'Reembolsado',
                    'CAMBIO' => $venta->total < 0 ? 'Reembolsado' : ($venta->total > 0 ? 'Cobrado' : 'Diferencia liquidada'),
                    default => 'Cobrado',
                } }}</span>
                <span>Q {{ number_format($montoPagado, 2) }}</span>
            </div>

            @if ($saldoPendiente !== 0.0 && $venta->estado_venta === 'REGISTRADA')
                <button type="button" @click="abrirModalPago()"
                    class="mt-2 rounded-md bg-app-primary-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-app-primary-800">
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
                        title="La venta no puede anularse tras 5 días hábiles de su registro">
                        Anular venta
                    </x-danger-button>
                @endif
            @endif
            <x-secondary-link href="{{ route('ventas.index') }}">Volver a ventas</x-secondary-link>
        </div>
    </div>
</x-app-layout>
