<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MetodoPago;
use App\Models\Presentacion;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VentasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $ventas = Venta::query()
            ->with([
                'vendedor:id,nombres',
                'cliente:id,nombres,apellidos',
            ])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('numero_venta', 'like', "%{$buscar}%")
                        ->orWhereHas('vendedor', function ($vendedor) use ($buscar) {
                            $vendedor->where('username', 'like', "%{$buscar}%")
                                ->orWhere('nombres', 'like', "%{$buscar}%")
                                ->orWhere('apellidos', 'like', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('ventas.index', compact('ventas', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $metodosPago = MetodoPago::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('ventas.create', compact('metodosPago'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida cliente, productos y pagos; los precios y totales se calculan con datos del servidor.
        $datos = $request->validate([
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.presentacion_id' => ['required', 'integer', 'distinct', 'exists:presentaciones,id'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'detalles.*.tipo_precio' => ['sometimes', 'string', 'in:unitario,docena,ciento'],
            'detalles.*.descuento' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos' => ['sometimes', 'array'],
            'pagos.*.metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'pagos.*.monto' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos.*.referencia' => ['nullable', 'string', 'max:100'],
        ]);

        // Bloquea presentaciones e inventarios para evitar usar precios o existencias desactualizados.
        $venta = DB::transaction(function () use ($datos) {
            $presentaciones = Presentacion::query()
                ->whereIn('id', collect($datos['detalles'])->pluck('presentacion_id'))
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
            $salidasInventario = [];
            $totalDescuentos = 0;
            $costoTotal = 0;
            $total = 0;

            // Determina precio, costo y subtotales de cada artículo con los datos vigentes.
            foreach ($datos['detalles'] as $indice => $detalle) {
                $presentacion = $presentaciones->get($detalle['presentacion_id']);
                $inventario = $presentacion
                    ? $inventarios->get($presentacion->inventario_id)
                    : null;

                if (
                    ! $presentacion
                    || ! $presentacion->estado
                    || ! $inventario?->estado
                ) {
                    throw ValidationException::withMessages([
                        "detalles.{$indice}.presentacion_id" => 'La presentación seleccionada no está disponible.',
                    ]);
                }

                if ($presentacion->factor < 1) {
                    throw ValidationException::withMessages([
                        "detalles.{$indice}.presentacion_id" => 'La presentación tiene un factor inválido.',
                    ]);
                }

                $cantidad = (float) $detalle['cantidad'];
                $factor = (int) $presentacion->factor;
                $cantidadBase = $cantidad * $factor;
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

                $descuento = (float) ($detalle['descuento'] ?? 0);
                $subtotalBruto = round($cantidad * $precioAplicado, 2);

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

                // Acumula solo productos y exige unidades base enteras para descontar el stock.
                if ($inventario->tipo === 'PRODUCTO') {
                    if (abs($cantidadBase - round($cantidadBase)) > 0.00001) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.cantidad" => 'La cantidad base de un producto debe ser un número entero.',
                        ]);
                    }

                    $salidasInventario[$inventario->id] = ($salidasInventario[$inventario->id] ?? 0)
                        + (int) round($cantidadBase);
                }

                $detallesVenta[] = [
                    'presentacion_id' => $presentacion->id,
                    'tipo_item' => $inventario->tipo,
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
                $costoTotal = round($costoTotal + ($subtotalCosto ?? 0), 2);
                $total = round($total + $subtotal, 2);
            }

            // Verifica las existencias antes de registrar la venta.
            foreach ($salidasInventario as $inventarioId => $cantidadSalida) {
                $inventario = $inventarios->get($inventarioId);

                if ($inventario->cantidad === null || (int) $inventario->cantidad < $cantidadSalida) {
                    $nombreProducto = $presentaciones->firstWhere('inventario_id', $inventarioId)?->nombre
                        ?? 'artículo';

                    throw ValidationException::withMessages([
                        'detalles' => "Existencias insuficientes para {$nombreProducto}.",
                    ]);
                }
            }

            // Rechaza pagos que excedan el total y determina el estado de pago.
            $pagos = $datos['pagos'] ?? [];
            $montoPagado = round(array_sum(array_column($pagos, 'monto')), 2);

            if ($montoPagado > $total) {
                throw ValidationException::withMessages([
                    'pagos' => 'La suma de los pagos no puede superar el total de la venta.',
                ]);
            }

            $estadoPago = $montoPagado <= 0
                ? ($total <= 0 ? 'PAGADO' : 'PENDIENTE')
                : ($montoPagado >= $total ? 'PAGADO' : 'PARCIAL');

            // Crea la venta con sus totales y genera el número definitivo usando el ID.
            $venta = Venta::create([
                'numero_venta' => 'TMP-'.Str::random(16),
                'vendedor_id' => Auth::id(),
                'cliente_id' => $datos['cliente_id'] ?? null,
                'tipo_operacion' => 'VENTA',
                'estado_venta' => 'REGISTRADA',
                'estado_pago' => $estadoPago,
                'total_descuentos' => $totalDescuentos,
                'costo_total' => $costoTotal,
                'total' => $total,
            ]);

            $venta->update([
                'numero_venta' => 'VTA-'.str_pad(
                    (string) $venta->id,
                    16,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            // Guarda los renglones y los pagos asociados dentro de la misma transacción.
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

            // Descuenta las unidades base y registra una salida por inventario mediante el trigger.
            foreach ($salidasInventario as $inventarioId => $cantidadSalida) {
                $inventario = $inventarios->get($inventarioId);
                $inventario->cantidad = (int) $inventario->cantidad - $cantidadSalida;

                DB::statement('SET @usuario_id = ?, @motivo = ?', [
                    Auth::id(),
                    'Venta '.$venta->numero_venta,
                ]);

                try {
                    $inventario->save();
                } finally {
                    DB::statement('SET @usuario_id = NULL, @motivo = NULL');
                }
            }

            return $venta;
        });

        // Confirma el registro al usuario.
        return redirect()
            ->back()
            ->with('success', "La venta {$venta->numero_venta} se registró correctamente.");
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

        return view('ventas.show', compact('venta'));
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
