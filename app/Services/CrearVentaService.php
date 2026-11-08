<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\Presentacion;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CrearVentaService
{
    public function __construct(private IdempotencyService $idempotencyService) {}

    public function crear(array $datos, int $userId): Venta
    {
        $idempotencyKey = $datos['idempotency_key'];
        unset($datos['idempotency_key']);

        $tipoOperacion = $datos['tipo_operacion'] ?? 'VENTA';
        $movimientosDetalle = collect($datos['detalles'])->map(
            fn (array $detalle) => $detalle['tipo_movimiento']
                ?? ($tipoOperacion === 'VENTA' ? 'VENTA' : null)
        );

        $venta = $this->idempotencyService->execute(
            (int) $userId,
            'crear_venta',
            $idempotencyKey,
            $datos,
            function () use ($datos, $tipoOperacion, $movimientosDetalle, $userId) {
                $ventaOriginal = null;
                $detallesOriginales = collect();
                $devolucionesPrevias = collect();

                if ($tipoOperacion !== 'VENTA') {
                    $ventaOriginal = Venta::query()
                        ->whereKey($datos['venta_referencia_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($ventaOriginal->tipo_operacion !== 'VENTA' || $ventaOriginal->estado_venta !== 'REGISTRADA') {
                        throw ValidationException::withMessages([
                            'venta_referencia_id' => 'Solo se pueden devolver artículos de una venta registrada.',
                        ]);
                    }

                    if (! $this->estaDentroDelPlazoDeOperacion($ventaOriginal)) {
                        throw ValidationException::withMessages([
                            'venta_referencia_id' => 'El plazo para realizar cambios o devoluciones venció: son 5 días hábiles, excluyendo domingos.',
                        ]);
                    }

                    $montoPagadoOriginal = round((float) $ventaOriginal->detallesPago()->sum('monto'), 2);
                    $saldoPendienteOriginal = round((float) $ventaOriginal->total - $montoPagadoOriginal, 2);

                    if ($saldoPendienteOriginal > 0) {
                        throw ValidationException::withMessages([
                            'venta_referencia_id' => 'No se puede realizar un cambio o devolución mientras la venta tenga saldo pendiente. Liquida el saldo primero.',
                        ]);
                    }

                    $idsDetallesOriginales = collect($datos['detalles'])
                        ->filter(fn (array $detalle, int $indice) => $movimientosDetalle[$indice] === 'DEVOLUCION')
                        ->pluck('detalle_referencia_id')
                        ->filter()
                        ->unique()
                        ->values();

                    if ($idsDetallesOriginales->count() !== collect($datos['detalles'])
                        ->filter(fn (array $detalle, int $indice) => $movimientosDetalle[$indice] === 'DEVOLUCION')
                        ->count()) {
                        throw ValidationException::withMessages([
                            'detalles' => 'Cada artículo devuelto debe referenciar el detalle de la venta original.',
                        ]);
                    }

                    $detallesOriginales = VentaDetalle::query()
                        ->where('venta_id', $ventaOriginal->id)
                        ->whereIn('id', $idsDetallesOriginales)
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    if ($detallesOriginales->count() !== $idsDetallesOriginales->count()) {
                        throw ValidationException::withMessages([
                            'detalles' => 'Uno o más artículos no pertenecen a la venta original.',
                        ]);
                    }

                    $devolucionesPrevias = VentaDetalle::query()
                        ->whereIn('detalle_referencia_id', $idsDetallesOriginales)
                        ->where('tipo_movimiento', 'DEVOLUCION')
                        ->whereHas('venta', function ($query) use ($ventaOriginal) {
                            $query->where('venta_referencia_id', $ventaOriginal->id)
                                ->where('estado_venta', 'REGISTRADA');
                        })
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get()
                        ->groupBy('detalle_referencia_id');

                    foreach ($detallesOriginales as $detalleOriginal) {
                        if ($detalleOriginal->tipo_movimiento !== 'VENTA') {
                            throw ValidationException::withMessages([
                                'detalles' => 'Solo se pueden devolver artículos vendidos originalmente.',
                            ]);
                        }
                    }
                }

                $idsPresentaciones = collect($datos['detalles'])
                    ->filter(fn (array $detalle, int $indice) => $movimientosDetalle[$indice] === 'ENTREGA_CAMBIO' || $tipoOperacion === 'VENTA')
                    ->pluck('presentacion_id')
                    ->filter()
                    ->merge($detallesOriginales->pluck('presentacion_id'))
                    ->unique()
                    ->values();

                $presentaciones = Presentacion::query()
                    ->whereIn('id', $idsPresentaciones)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $inventarios = Inventario::query()
                    ->whereIn('id', $presentaciones->pluck('inventario_id')->unique())
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $detallesVenta = [];
                $cambiosInventario = [];
                $totalDescuentos = 0;
                $costoEntregado = 0;
                $costoDevuelto = 0;
                $totalEntregado = 0;
                $totalDevuelto = 0;

                foreach ($datos['detalles'] as $indice => $detalle) {
                    $tipoMovimiento = $movimientosDetalle[$indice];
                    $detalleOriginal = $tipoMovimiento === 'DEVOLUCION'
                        ? $detallesOriginales->get($detalle['detalle_referencia_id'] ?? null)
                        : null;
                    $presentacionId = $detalleOriginal?->presentacion_id ?? ($detalle['presentacion_id'] ?? null);
                    $presentacion = $presentaciones->get($presentacionId);
                    $inventario = $presentacion
                        ? $inventarios->get($presentacion->inventario_id)
                        : null;

                    if (! $presentacion || ! $inventario) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.presentacion_id" => 'La presentación seleccionada no está disponible.',
                        ]);
                    }

                    $esDevolucion = $tipoMovimiento === 'DEVOLUCION';
                    $tipoItem = $esDevolucion
                        ? $detalleOriginal->tipo_item
                        : $inventario->tipo;

                    if ($esDevolucion && $tipoItem !== $inventario->tipo) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.presentacion_id" => 'El tipo de inventario del artículo cambió desde su venta; verifica la devolución manualmente.',
                        ]);
                    }

                    if (! $esDevolucion && (! $presentacion->estado || ! $inventario->estado)) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.presentacion_id" => 'La presentación seleccionada no está disponible.',
                        ]);
                    }

                    $factor = $esDevolucion ? (int) $detalleOriginal->factor : (int) $presentacion->factor;

                    if ($factor < 1) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.presentacion_id" => 'La presentación tiene un factor inválido.',
                        ]);
                    }

                    $cantidad = (float) $detalle['cantidad'];
                    $cantidadBase = $cantidad * $factor;
                    if ($tipoItem === 'PRODUCTO') {
                        if (abs($cantidadBase - round($cantidadBase)) > 0.00001) {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.cantidad" => 'La cantidad base de un producto debe ser un número entero.',
                            ]);
                        }

                        if ($esDevolucion && ! isset($detalle['reingresa_inventario'])) {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.reingresa_inventario" => 'Indica si el artículo devuelto vuelve al inventario.',
                            ]);
                        }
                    }

                    if ($esDevolucion) {
                        $devolucionesPreviasDetalle = $devolucionesPrevias->get($detalleOriginal->id, collect());
                        $cantidadDevueltaAntes = (float) $devolucionesPreviasDetalle->sum('cantidad');
                        $cantidadRestante = (float) $detalleOriginal->cantidad - $cantidadDevueltaAntes;

                        if ($cantidad > $cantidadRestante + 0.00001) {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.cantidad" => 'La cantidad supera lo que queda disponible para devolver de la venta original.',
                            ]);
                        }

                        $esDevolucionCompleta = abs($cantidad - $cantidadRestante) <= 0.00001;
                        $subtotalDevueltoAntes = (float) $devolucionesPreviasDetalle->sum('subtotal');
                        $descuentoDevueltoAntes = (float) $devolucionesPreviasDetalle->sum('descuento');
                        $costoDevueltoAntes = (float) $devolucionesPreviasDetalle->sum('subtotal_costo');
                        $descuento = $esDevolucionCompleta
                            ? round((float) $detalleOriginal->descuento - $descuentoDevueltoAntes, 2)
                            : round((float) $detalleOriginal->descuento * $cantidad / (float) $detalleOriginal->cantidad, 2);
                        $subtotal = $esDevolucionCompleta
                            ? round((float) $detalleOriginal->subtotal - $subtotalDevueltoAntes, 2)
                            : round((float) $detalleOriginal->subtotal * $cantidad / (float) $detalleOriginal->cantidad, 2);
                        $precioAplicado = (float) $detalleOriginal->precio_aplicado;
                        $precioUnitario = (float) $detalleOriginal->precio_unitario;
                        $costoUnitario = $detalleOriginal->costo_unitario;
                        $subtotalCosto = $detalleOriginal->subtotal_costo === null
                            ? null
                            : ($esDevolucionCompleta
                                ? round((float) $detalleOriginal->subtotal_costo - $costoDevueltoAntes, 2)
                                : round((float) $detalleOriginal->subtotal_costo * $cantidad / (float) $detalleOriginal->cantidad, 2));
                        $reingresaInventario = (bool) ($detalle['reingresa_inventario'] ?? false);

                        if ($reingresaInventario && $tipoItem !== 'PRODUCTO') {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.reingresa_inventario" => 'Los servicios no pueden reingresar al inventario.',
                            ]);
                        }

                        if ($tipoItem === 'PRODUCTO' && $reingresaInventario) {
                            $cambiosInventario[$inventario->id] = ($cambiosInventario[$inventario->id] ?? 0)
                                + (int) round($cantidadBase);
                            $costoDevuelto = round($costoDevuelto + ($subtotalCosto ?? 0), 2);
                        }

                        $totalDevuelto = round($totalDevuelto + $subtotal, 2);
                    } else {
                        $tipoPrecio = $detalle['tipo_precio'] ?? 'unitario';
                        $precioUnitario = (float) $presentacion->precioUnitario;
                        $precioAplicado = match ($tipoPrecio) {
                            'docena' => $cantidadBase >= 12 && $presentacion->precioUnitarioDocena !== null
                                ? (float) $presentacion->precioUnitarioDocena
                                : null,
                            'ciento' => $cantidadBase >= 100 && $presentacion->precioUnitarioCiento !== null
                                ? (float) $presentacion->precioUnitarioCiento
                                : null,
                            default => $precioUnitario,
                        };

                        if ($precioAplicado === null) {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.tipo_precio" => 'El precio seleccionado no está disponible para esta cantidad.',
                            ]);
                        }

                        $descuento = array_key_exists('descuento', $detalle)
                            ? (float) $detalle['descuento']
                            : round(max(0, $precioUnitario - $precioAplicado) * $cantidad, 2);
                        $subtotalBruto = round($cantidad * $precioUnitario, 2);

                        if ($descuento > $subtotalBruto) {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.descuento" => 'El descuento no puede superar el subtotal del detalle.',
                            ]);
                        }

                        $subtotal = round($subtotalBruto - $descuento, 2);
                        $costoUnitario = $inventario->costoPromedioUnitario;
                        $subtotalCosto = $costoUnitario === null
                            ? null
                            : round($cantidadBase * (float) $costoUnitario, 2);

                        if ($inventario->tipo === 'PRODUCTO') {
                            $cambiosInventario[$inventario->id] = ($cambiosInventario[$inventario->id] ?? 0)
                                - (int) round($cantidadBase);
                        }

                        $totalEntregado = round($totalEntregado + $subtotal, 2);
                        $costoEntregado = round($costoEntregado + ($subtotalCosto ?? 0), 2);
                    }

                    $detallesVenta[] = [
                        'presentacion_id' => $presentacion->id,
                        'tipo_movimiento' => $tipoMovimiento,
                        'detalle_referencia_id' => $detalleOriginal?->id,
                        'reingresa_inventario' => $esDevolucion ? $reingresaInventario : null,
                        'tipo_item' => $tipoItem,
                        'cantidad' => $cantidad,
                        'factor' => $factor,
                        'cantidad_base' => $cantidadBase,
                        'costo_unitario' => $costoUnitario,
                        'subtotal_costo' => $subtotalCosto,
                        'precio_unitario' => $precioUnitario,
                        'precio_aplicado' => $precioAplicado,
                        'descuento' => $descuento,
                        'subtotal' => $subtotal,
                    ];

                    $totalDescuentos = round(
                        $totalDescuentos + ($esDevolucion ? -$descuento : $descuento),
                        2
                    );
                }

                foreach ($cambiosInventario as $inventarioId => $cambioCantidad) {
                    $inventario = $inventarios->get($inventarioId);
                    $cantidadActual = $inventario->cantidad;
                    $cantidadNueva = (int) $cantidadActual + $cambioCantidad;

                    if ($cantidadActual === null || $cantidadNueva < 0) {
                        $nombreProducto = $presentaciones->firstWhere('inventario_id', $inventarioId)?->nombre
                            ?? 'artículo';

                        throw ValidationException::withMessages([
                            'detalles' => "Existencias insuficientes para {$nombreProducto}.",
                        ]);
                    }
                }

                $total = $tipoOperacion === 'VENTA'
                    ? $totalEntregado
                    : round($totalEntregado - $totalDevuelto, 2);
                $costoTotal = $tipoOperacion === 'VENTA'
                    ? $costoEntregado
                    : round($costoEntregado - $costoDevuelto, 2);
                $pagos = $datos['pagos'] ?? [];
                $montoPagado = round(array_sum(array_column($pagos, 'monto')), 2);
                $pagosConSignoInvalido = $total > 0
                    ? collect($pagos)->contains(fn (array $pago) => (float) $pago['monto'] <= 0)
                    : ($total < 0
                        ? collect($pagos)->contains(fn (array $pago) => (float) $pago['monto'] >= 0)
                        : $pagos !== []);

                if ($pagosConSignoInvalido) {
                    throw ValidationException::withMessages([
                        'pagos' => 'Los cobros deben ser positivos y los reembolsos negativos, según el saldo de la operación.',
                    ]);
                }

                if (abs($montoPagado) > abs($total)) {
                    throw ValidationException::withMessages([
                        'pagos' => 'La suma de los movimientos no puede superar el saldo de la operación.',
                    ]);
                }

                if ($tipoOperacion !== 'VENTA' && $total !== 0.0 && abs($montoPagado) < abs($total)) {
                    throw ValidationException::withMessages([
                        'pagos' => 'El cobro o reembolso debe cubrir por completo la diferencia de la operación.',
                    ]);
                }

                if (
                    $tipoOperacion === 'VENTA'
                    && empty($datos['venta_credito'])
                    && $montoPagado < $total
                ) {
                    throw ValidationException::withMessages([
                        'pagos' => 'El pago debe cubrir el total o marcar la venta al crédito.',
                    ]);
                }

                $estadoPago = $this->calcularEstadoPago($total, $montoPagado);

                $venta = Venta::create([
                    'numero_venta' => 'TMP-'.Str::random(16),
                    'vendedor_id' => $userId,
                    'cliente_id' => $ventaOriginal?->cliente_id ?? ($datos['cliente_id'] ?? null),
                    'tipo_operacion' => $tipoOperacion,
                    'estado_venta' => 'REGISTRADA',
                    'estado_pago' => $estadoPago,
                    'venta_referencia_id' => $ventaOriginal?->id,
                    'motivo_operacion' => $datos['motivo_operacion'] ?? null,
                    'total_descuentos' => $totalDescuentos,
                    'costo_total' => $costoTotal,
                    'total' => $total,
                ]);

                $prefijoNumero = match ($tipoOperacion) {
                    'DEVOLUCION' => 'DEV-',
                    'CAMBIO' => 'CAM-',
                    default => 'VTA-',
                };

                $venta->update([
                    'numero_venta' => $prefijoNumero.str_pad(
                        (string) $venta->id,
                        7,
                        '0',
                        STR_PAD_LEFT
                    ),
                ]);

                $venta->detalles()->createMany($detallesVenta);

                foreach ($pagos as $pago) {
                    $montoRecibido = $pago['monto_recibido'] ?? null;

                    if ($montoRecibido !== null && (float) $montoRecibido < (float) $pago['monto']) {
                        throw ValidationException::withMessages([
                            'pagos' => 'El monto recibido no puede ser menor al monto aplicado.',
                        ]);
                    }

                    $venta->detallesPago()->create([
                        'metodo_pago_id' => $pago['metodo_pago_id'],
                        'monto' => $pago['monto'],
                        'monto_recibido' => $montoRecibido,
                        'cambio' => $montoRecibido === null || (float) $pago['monto'] < 0
                            ? null
                            : round((float) $montoRecibido - (float) $pago['monto'], 2),
                        'referencia' => $pago['referencia'] ?? null,
                        'fecha_pago' => now(),
                    ]);
                }

                foreach ($cambiosInventario as $inventarioId => $cambioCantidad) {
                    $inventario = $inventarios->get($inventarioId);
                    $inventario->cantidad = (int) $inventario->cantidad + $cambioCantidad;

                    DB::statement('SET @usuario_id = ?, @motivo = ?', [
                        $userId,
                        ucfirst(strtolower($tipoOperacion)).' '.$venta->numero_venta,
                    ]);

                    try {
                        $inventario->save();
                    } finally {
                        DB::statement('SET @usuario_id = NULL, @motivo = NULL');
                    }
                }

                return $venta;
            }
        );

        return $venta;
    }

    private function calcularEstadoPago(float $total, float $montoPagado): string
    {
        $total = round($total, 2);
        $montoPagado = round($montoPagado, 2);

        if ($total === 0.0) {
            return 'PAGADO';
        }

        if ($total < 0) {
            if (abs($montoPagado) >= abs($total)) {
                return 'REEMBOLSADO';
            }

            return $montoPagado === 0.0 ? 'PENDIENTE' : 'PARCIAL';
        }

        if ($montoPagado >= $total) {
            return 'PAGADO';
        }

        return $montoPagado === 0.0 ? 'PENDIENTE' : 'PARCIAL';
    }

    private function estaDentroDelPlazoDeOperacion(Venta $venta): bool
    {
        $fechaLimite = $venta->created_at->copy();
        $diasHabilesTranscurridos = 0;

        while ($diasHabilesTranscurridos < 5) {
            $fechaLimite->addDay();

            if ($fechaLimite->dayOfWeek !== Carbon::SUNDAY) {
                $diasHabilesTranscurridos++;
            }
        }

        return now()->lessThanOrEqualTo($fechaLimite);
    }
}
