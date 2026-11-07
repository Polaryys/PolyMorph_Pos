<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\MetodoPago;
use App\Models\Presentacion;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VentasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::query()
            ->with([
                'vendedor:id,username',
                'cliente:id,nombres,apellidos',
            ])
            ->latest()
            ->paginate(15);

        return view('ventas.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ventas.create');
    }

    /**
     * Buscar presentaciones activas de productos y servicios para una venta.
     */
    public function buscarProductos(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'buscar' => ['required', 'string', 'min:1', 'max:100'],
        ]);
        $buscar = trim($datos['buscar']);

        if ($buscar === '') {
            return response()->json([]);
        }

        $presentaciones = Presentacion::query()
            ->select([
                'id',
                'inventario_id',
                'nombre',
                'codigoSKU',
                'factor',
                'precioUnitario',
                'precioUnitarioDocena',
                'precioUnitarioCiento',
                'imagen_id',
            ])
            ->where('estado', true)
            ->whereHas('inventario', function ($query) {
                $query->where('estado', true);
            })
            ->with([
                'inventario:id,marca_id,cantidad,costoPromedioUnitario,tipo,estado',
                'inventario.marca:id,nombre',
                'imagen:id,nombre_archivo',
            ])
            ->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigoSKU', 'like', "%{$buscar}%");
            })
            ->orderByRaw(
                'CASE WHEN codigoSKU = ? THEN 0 WHEN codigoSKU LIKE ? THEN 1 WHEN nombre LIKE ? THEN 2 ELSE 3 END',
                [$buscar, "{$buscar}%", "{$buscar}%"]
            )
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(fn (Presentacion $presentacion) => [
                'presentacion_id' => $presentacion->id,
                'nombre' => $presentacion->nombre,
                'sku' => $presentacion->codigoSKU,
                'marca' => $presentacion->inventario?->marca?->nombre,
                'tipo_item' => $presentacion->inventario?->tipo,
                'factor' => (int) $presentacion->factor,
                'imagen' => $presentacion->imagen
                    ? asset('storage/imagenes/'.$presentacion->imagen->nombre_archivo)
                    : null,
                'cantidad_actual' => $presentacion->inventario?->cantidad,
                'costo_promedio_actual' => (float) ($presentacion->inventario?->costoPromedioUnitario ?? 0),
                'precio_unitario_actual' => (float) $presentacion->precioUnitario,
                'precio_docena_actual' => $presentacion->precioUnitarioDocena === null
                    ? null
                    : (float) $presentacion->precioUnitarioDocena,
                'precio_ciento_actual' => $presentacion->precioUnitarioCiento === null
                    ? null
                    : (float) $presentacion->precioUnitarioCiento,
            ])
            ->values();

        return response()->json($presentaciones);
    }

    public function buscarClientes(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'buscar' => ['required', 'string', 'min:1', 'max:100'],
        ]);
        $buscar = trim($datos['buscar']);

        if ($buscar === '') {
            return response()->json([]);
        }

        $clientes = Cliente::query()
            ->select(['id', 'nit', 'nombres', 'apellidos', 'telefono'])
            ->where(function ($query) use ($buscar) {
                $query->where('nit', 'like', "%{$buscar}%")
                    ->orWhere('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%");
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->limit(10)
            ->get()
            ->map(fn (Cliente $cliente) => [
                'id' => $cliente->id,
                'nit' => $cliente->nit,
                'nombre' => trim($cliente->nombres.' '.$cliente->apellidos),
                'telefono' => $cliente->telefono,
            ])
            ->values();

        return response()->json($clientes);
    }

    public function metodosPago(): JsonResponse
    {
        return response()->json(
            MetodoPago::query()
                ->where('estado', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'tipo_operacion' => ['sometimes', 'required', 'string', 'in:VENTA,DEVOLUCION,CAMBIO'],
            'venta_credito' => ['sometimes', 'boolean'],
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'venta_referencia_id' => ['required_if:tipo_operacion,DEVOLUCION,CAMBIO', 'nullable', 'integer', 'exists:ventas,id'],
            'motivo_operacion' => ['required_if:tipo_operacion,DEVOLUCION,CAMBIO', 'nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.presentacion_id' => ['nullable', 'integer', 'exists:presentaciones,id'],
            'detalles.*.tipo_movimiento' => ['sometimes', 'string', 'in:VENTA,DEVOLUCION,ENTREGA_CAMBIO'],
            'detalles.*.detalle_referencia_id' => ['nullable', 'integer', 'distinct', 'exists:venta_detalles,id'],
            'detalles.*.reingresa_inventario' => ['nullable', 'boolean'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'detalles.*.tipo_precio' => ['sometimes', 'string', 'in:unitario,docena,ciento'],
            'detalles.*.descuento' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos' => ['sometimes', 'array'],
            'pagos.*.metodo_pago_id' => [
                'required',
                'integer',
                Rule::exists('metodos_pago', 'id')->where('estado', true),
            ],
            'pagos.*.monto' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos.*.referencia' => ['nullable', 'string', 'max:100'],
            'reembolso' => ['sometimes', 'array'],
            'reembolso.metodo_pago_id' => [
                'required_with:reembolso',
                'integer',
                Rule::exists('metodos_pago', 'id')->where('estado', true),
            ],
            'reembolso.referencia' => ['nullable', 'string', 'max:100'],
        ]);

        $tipoOperacion = $datos['tipo_operacion'] ?? 'VENTA';
        $movimientosDetalle = collect($datos['detalles'])->map(
            fn (array $detalle) => $detalle['tipo_movimiento']
                ?? ($tipoOperacion === 'VENTA' ? 'VENTA' : null)
        );

        if (
            ($tipoOperacion === 'VENTA' && $movimientosDetalle->contains(fn ($tipo) => $tipo !== 'VENTA'))
            || ($tipoOperacion === 'DEVOLUCION' && $movimientosDetalle->contains(fn ($tipo) => $tipo !== 'DEVOLUCION'))
            || ($tipoOperacion === 'CAMBIO' && (
                ! $movimientosDetalle->contains('DEVOLUCION')
                || ! $movimientosDetalle->contains('ENTREGA_CAMBIO')
                || $movimientosDetalle->contains(fn ($tipo) => ! in_array($tipo, ['DEVOLUCION', 'ENTREGA_CAMBIO'], true))
            ))
        ) {
            throw ValidationException::withMessages([
                'detalles' => 'Los artículos seleccionados no corresponden al tipo de operación.',
            ]);
        }

        if ($tipoOperacion !== 'VENTA' && ! empty($datos['pagos']) && ! empty($datos['reembolso'])) {
            throw ValidationException::withMessages([
                'pagos' => 'Una operación no puede cobrar y reembolsar al mismo tiempo.',
            ]);
        }

        if ($tipoOperacion === 'VENTA' && (! empty($datos['venta_referencia_id']) || ! empty($datos['reembolso']))) {
            throw ValidationException::withMessages([
                'tipo_operacion' => 'Una venta normal no puede tener una venta de referencia ni registrar reembolsos.',
            ]);
        }

        $venta = DB::transaction(function () use ($datos, $tipoOperacion, $movimientosDetalle) {
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

                $totalDescuentos = round($totalDescuentos + $descuento, 2);
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

            if ($montoPagado > max($total, 0)) {
                throw ValidationException::withMessages([
                    'pagos' => 'La suma de los pagos no puede superar el saldo a cobrar.',
                ]);
            }

            if (
                $tipoOperacion === 'VENTA'
                && empty($datos['venta_credito'])
                && $montoPagado < max($total, 0)
            ) {
                throw ValidationException::withMessages([
                    'pagos' => 'El pago debe cubrir el total o marcar la venta al crédito.',
                ]);
            }

            if ($tipoOperacion !== 'VENTA' && $total < 0 && empty($datos['reembolso'])) {
                throw ValidationException::withMessages([
                    'reembolso' => 'Debes indicar el método para registrar el reembolso al cliente.',
                ]);
            }

            if ($tipoOperacion !== 'VENTA' && $total >= 0 && ! empty($datos['reembolso'])) {
                throw ValidationException::withMessages([
                    'reembolso' => 'Solo se registra un reembolso cuando el saldo favorece al cliente.',
                ]);
            }

            $estadoPago = $total < 0
                ? 'PAGADO'
                : ($montoPagado <= 0
                    ? ($total <= 0 ? 'PAGADO' : 'PENDIENTE')
                    : ($montoPagado >= $total ? 'PAGADO' : 'PARCIAL'));

            $venta = Venta::create([
                'numero_venta' => 'TMP-'.Str::random(16),
                'vendedor_id' => Auth::id(),
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
                    'cambio' => $montoRecibido === null
                        ? null
                        : round((float) $montoRecibido - (float) $pago['monto'], 2),
                    'referencia' => $pago['referencia'] ?? null,
                    'fecha_pago' => now(),
                ]);
            }

            if ($total < 0) {
                $venta->reembolsos()->create([
                    'metodo_pago_id' => $datos['reembolso']['metodo_pago_id'],
                    'usuario_id' => Auth::id(),
                    'monto' => abs($total),
                    'estado' => 'PROCESADO',
                    'referencia' => $datos['reembolso']['referencia'] ?? null,
                    'fecha_reembolso' => now(),
                ]);
            }

            foreach ($cambiosInventario as $inventarioId => $cambioCantidad) {
                $inventario = $inventarios->get($inventarioId);
                $inventario->cantidad = (int) $inventario->cantidad + $cambioCantidad;

                DB::statement('SET @usuario_id = ?, @motivo = ?', [
                    Auth::id(),
                    ucfirst(strtolower($tipoOperacion)).' '.$venta->numero_venta,
                ]);

                try {
                    $inventario->save();
                } finally {
                    DB::statement('SET @usuario_id = NULL, @motivo = NULL');
                }
            }

            return $venta;
        });

        return redirect()
            ->back()
            ->with('success', "La operación {$venta->numero_venta} se registró correctamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        // Carga todas las relaciones necesarias para mostrar la venta en una sola consulta por relación.
        $venta->load([
            'vendedor:id,nombres,apellidos,username',
            'cliente:id,nit,nombres,apellidos,telefono,correo,clasificacion',
            'detalles.presentacion.inventario.marca',
            'detallesPago.metodoPago',
            'ventaReferencia:id,numero_venta',
            'operacionesRelacionadas:id,numero_venta,venta_referencia_id,tipo_operacion,estado_venta,estado_pago,total',
        ]);

        $totalProductos = round((float) $venta->detalles->sum(
            fn (VentaDetalle $detalle) => (float) $detalle->subtotal + (float) $detalle->descuento
        ), 2);
        $montoPagado = round((float) $venta->detallesPago->sum('monto'), 2);
        $saldoPendiente = max(0, round((float) $venta->total - $montoPagado, 2));

        return view('ventas.show', compact('venta', 'totalProductos', 'montoPagado', 'saldoPendiente'));
    }

    public function registrarPago(Request $request, Venta $venta)
    {
        $datos = $request->validate([
            'pagos' => ['required', 'array', 'min:1'],
            'pagos.*.metodo_pago_id' => [
                'required',
                'integer',
                Rule::exists('metodos_pago', 'id')->where('estado', true),
            ],
            'pagos.*.monto' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos.*.referencia' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($datos, $venta) {
            $venta = Venta::query()
                ->whereKey($venta->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venta->tipo_operacion !== 'VENTA' || $venta->estado_venta !== 'REGISTRADA') {
                throw ValidationException::withMessages([
                    'pagos' => 'Solo se pueden registrar pagos para ventas registradas.',
                ]);
            }

            $pagos = $datos['pagos'];
            $montoNuevo = round(array_sum(array_column($pagos, 'monto')), 2);
            $montoPagado = round((float) $venta->detallesPago()->sum('monto'), 2);
            $saldoPendiente = round((float) $venta->total - $montoPagado, 2);

            if ($saldoPendiente <= 0) {
                throw ValidationException::withMessages([
                    'pagos' => 'Esta venta ya no tiene saldo pendiente.',
                ]);
            }

            if ($montoNuevo > $saldoPendiente) {
                throw ValidationException::withMessages([
                    'pagos' => 'La suma de los pagos no puede superar el saldo pendiente.',
                ]);
            }

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
                    'cambio' => $montoRecibido === null
                        ? null
                        : round((float) $montoRecibido - (float) $pago['monto'], 2),
                    'referencia' => $pago['referencia'] ?? null,
                    'fecha_pago' => now(),
                ]);
            }

            $venta->estado_pago = $montoPagado + $montoNuevo >= (float) $venta->total
                ? 'PAGADO'
                : 'PARCIAL';
            $venta->save();
        });

        return redirect()
            ->route('ventas.show', $venta)
            ->with('success', 'El pago se registró correctamente.');
    }

    /**
     * Anula una venta registrada y devuelve al inventario las unidades vendidas.
     */
    public function anular(Venta $venta)
    {
        DB::transaction(function () use ($venta) {
            // Bloquea la venta para impedir anulaciones simultáneas o repetidas.
            $venta = Venta::query()
                ->whereKey($venta->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venta->estado_venta !== 'REGISTRADA' || $venta->tipo_operacion !== 'VENTA') {
                throw ValidationException::withMessages([
                    'venta' => 'Solo se pueden anular ventas registradas.',
                ]);
            }

            if ($venta->operacionesRelacionadas()->exists()) {
                throw ValidationException::withMessages([
                    'venta' => 'No se puede anular una venta que ya tiene operaciones relacionadas.',
                ]);
            }

            // Bloquea detalles, presentaciones e inventarios para devolver el stock exacto.
            $detalles = $venta->detalles()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'venta' => 'No se puede anular una venta sin detalles.',
                ]);
            }

            $detallesProducto = $detalles->where('tipo_item', 'PRODUCTO');
            $presentaciones = Presentacion::query()
                ->whereIn('id', $detallesProducto->pluck('presentacion_id')->unique())
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

            $entradasInventario = [];

            foreach ($detallesProducto as $detalle) {
                $presentacion = $presentaciones->get($detalle->presentacion_id);
                $inventario = $presentacion
                    ? $inventarios->get($presentacion->inventario_id)
                    : null;
                $cantidadBase = (float) $detalle->cantidad_base;

                if (
                    ! $inventario
                    || $inventario->tipo !== 'PRODUCTO'
                    || $cantidadBase <= 0
                    || abs($cantidadBase - round($cantidadBase)) > 0.00001
                ) {
                    throw ValidationException::withMessages([
                        'venta' => 'No se pudo verificar el inventario de los productos de esta venta.',
                    ]);
                }

                $entradasInventario[$inventario->id] = ($entradasInventario[$inventario->id] ?? 0)
                    + (int) round($cantidadBase);
            }

            // Repone el stock y el trigger registra cada entrada con la venta como motivo.
            foreach ($entradasInventario as $inventarioId => $cantidadEntrada) {
                $inventario = $inventarios->get($inventarioId);

                if ($inventario->cantidad === null) {
                    throw ValidationException::withMessages([
                        'venta' => 'No se pudo verificar la existencia actual de un producto.',
                    ]);
                }

                $inventario->cantidad = (int) $inventario->cantidad + $cantidadEntrada;

                DB::statement('SET @usuario_id = ?, @motivo = ?', [
                    Auth::id(),
                    'Anulación de venta '.$venta->numero_venta,
                ]);

                try {
                    $inventario->save();
                } finally {
                    DB::statement('SET @usuario_id = NULL, @motivo = NULL');
                }
            }

            // Marca la venta como anulada dentro de la misma transacción del inventario.
            $venta->estado_venta = 'ANULADA';
            // Conserva los pagos registrados como historial; la venta ya no tiene estado de pago vigente.
            $venta->estado_pago = null;
            $venta->save();
        });

        return redirect()
            ->route('ventas.show', $venta)
            ->with('success', "La venta {$venta->numero_venta} fue anulada y el inventario fue restaurado.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
