<x-app-layout>
    <div class="space-y-8">
        <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
            @if (session('success'))
                <div class="m-5 rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700" role="status">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="m-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:p-6">
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

            <div class="grid grid-cols-1 gap-2 p-5 sm:grid-cols-2 xl:grid-cols-3 sm:p-6">
                <x-detail-field label="Tipo de operación">
                    {{ ucfirst(strtolower($venta->tipo_operacion)) }}
                </x-detail-field>
                <x-detail-field label="Estado de venta">
                    {{ ucfirst(strtolower($venta->estado_venta)) }}
                </x-detail-field>
                <x-detail-field label="Estado de pago">
                    {{ ucfirst(strtolower($venta->estado_pago)) }}
                </x-detail-field>
                <x-detail-field label="Vendedor">
                    {{ $venta->vendedor?->nombres ?? '—' }}
                    @if ($venta->vendedor?->apellidos)
                        {{ $venta->vendedor->apellidos }}
                    @endif
                    @if ($venta->vendedor?->username)
                        <span class="block text-xs text-slate-500">{{ '@'.$venta->vendedor->username }}</span>
                    @endif
                </x-detail-field>
                <x-detail-field label="Cliente">
                    @if ($venta->cliente)
                        {{ trim($venta->cliente->nombres.' '.$venta->cliente->apellidos) }}
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
                        <a href="{{ route('ventas.show', $venta->ventaReferencia) }}" class="font-medium text-indigo-700 hover:text-indigo-900">
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
                    <th class="whitespace-nowrap px-4 py-3 text-center">Factor</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Cantidad base</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio unitario</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio aplicado</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Descuento</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Subtotal</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Costo</th>
                </x-slot:headers>

                @forelse ($venta->detalles as $detalle)
                    <tr class="transition-colors hover:bg-indigo-50/50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-900">
                                {{ $detalle->presentacion?->nombre ?? 'Presentación no disponible' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $detalle->tipo_item }}
                                <span class="mx-1">·</span>
                                SKU: {{ $detalle->presentacion?->codigoSKU ?? '—' }}
                                @if ($marca = $detalle->presentacion?->inventario?->marca?->nombre)
                                    <span class="mx-1">·</span>
                                    Marca: {{ $marca }}
                                @endif
                            </p>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ number_format((float) $detalle->cantidad, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">{{ $detalle->factor }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ number_format((float) $detalle->cantidad_base, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format((float) $detalle->precio_unitario, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format((float) $detalle->precio_aplicado, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format((float) $detalle->descuento, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">
                            Q {{ number_format((float) $detalle->subtotal, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->subtotal_costo !== null
                                ? 'Q '.number_format((float) $detalle->subtotal_costo, 2)
                                : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                            Esta venta no tiene artículos registrados.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </section>

        <section class="space-y-3">
            <x-section-title>Pagos recibidos</x-section-title>
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
                            {{ $pago->monto_recibido !== null
                                ? 'Q '.number_format((float) $pago->monto_recibido, 2)
                                : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $pago->cambio !== null ? 'Q '.number_format((float) $pago->cambio, 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            Esta venta aún no tiene pagos registrados.
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
                        <tr class="transition-colors hover:bg-indigo-50/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('ventas.show', $operacion) }}" class="font-medium text-indigo-700 hover:text-indigo-900">
                                    {{ $operacion->numero_venta }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ ucfirst(strtolower($operacion->tipo_operacion)) }}</td>
                            <td class="px-4 py-3">
                                {{ ucfirst(strtolower($operacion->estado_venta)) }}
                                <span class="text-xs text-slate-500">
                                    · {{ ucfirst(strtolower($operacion->estado_pago)) }}
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

        <section class="flex flex-col gap-2 rounded border border-slate-200 bg-white p-5 shadow-sm sm:ml-auto sm:w-96">
            <div class="flex justify-between gap-4 text-sm text-slate-600">
                <span>Descuentos</span>
                <span>Q {{ number_format((float) $venta->total_descuentos, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 text-sm text-slate-600">
                <span>Costo total</span>
                <span>Q {{ number_format((float) $venta->costo_total, 2) }}</span>
            </div>
            <div class="flex justify-between gap-4 border-t border-slate-200 pt-3 text-lg font-bold text-slate-900">
                <span>Total de la venta</span>
                <span>Q {{ number_format((float) $venta->total, 2) }}</span>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            @if ($venta->tipo_operacion === 'VENTA' && $venta->estado_venta === 'REGISTRADA' && $venta->operacionesRelacionadas->isEmpty())
                <form action="{{ route('ventas.anular', $venta) }}" method="POST"
                    onsubmit="return confirm('¿Anular esta venta? Se devolverá el stock de sus productos al inventario.');">
                    @csrf
                    <x-danger-button type="submit">Anular venta</x-danger-button>
                </form>
            @endif
            <x-secondary-link href="{{ route('ventas.index') }}">Volver a ventas</x-secondary-link>
        </div>
    </div>
</x-app-layout>
