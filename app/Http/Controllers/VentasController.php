<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuscarClientesRequest;
use App\Http\Requests\BuscarProductosRequest;
use App\Http\Requests\RegistrarPagoVentaRequest;
use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\MetodoPago;
use App\Models\Presentacion;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Services\CrearVentaService;
use App\Services\RegistrarPagoVentaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $carrito = session('ventas.carrito', ['cliente_id' => null, 'detalles' => []]);
        $detallesGuardados = collect($carrito['detalles'] ?? []);
        $presentaciones = Presentacion::query()
            ->with([
                'inventario:id,marca_id,cantidad,costoPromedioUnitario,tipo,estado',
                'inventario.marca:id,nombre',
                'imagen:id,nombre_archivo',
            ])
            ->whereIn('id', $detallesGuardados->pluck('presentacion_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $detalles = $detallesGuardados
            ->map(function (array $detalle) use ($presentaciones) {
                $presentacion = $presentaciones->get($detalle['presentacion_id']);
                if (! $presentacion) {
                    return null;
                }

                return [
                    ...$this->datosProducto($presentacion),
                    'cantidad' => $detalle['cantidad'],
                    'tipo_precio' => $detalle['tipo_precio'],
                    'descuento' => (string) $detalle['descuento'],
                ];
            })
            ->filter()
            ->values()
            ->all();

        $cliente = ! empty($carrito['cliente_id'])
            ? Cliente::query()->find($carrito['cliente_id'])
            : null;
        $clienteSeleccionado = $cliente
            ? [
                'id' => $cliente->id,
                'nit' => $cliente->nit,
                'nombre' => trim($cliente->nombres.' '.$cliente->apellidos),
                'telefono' => $cliente->telefono,
            ]
            : null;

        return view('ventas.create', compact('detalles', 'clienteSeleccionado'));
    }

    public function guardarCarrito(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'detalles' => ['present', 'array', 'max:100'],
            'detalles.*.presentacion_id' => ['required', 'integer', 'distinct', 'exists:presentaciones,id'],
            'detalles.*.cantidad' => ['present', 'nullable', 'numeric', 'decimal:0,2'],
            'detalles.*.tipo_precio' => ['required', 'string', 'in:unitario,docena,ciento'],
            'detalles.*.descuento' => ['present', 'nullable', 'string', 'max:30'],
        ]);

        $carrito = $this->datosCarrito($datos);
        if ($carrito['cliente_id'] === null && $carrito['detalles'] === []) {
            $request->session()->forget('ventas.carrito');
        } else {
            $request->session()->put('ventas.carrito', $carrito);
        }

        return response()->json(['guardado' => true]);
    }

    /**
     * Buscar presentaciones activas de productos y servicios para una venta.
     */
    public function buscarProductos(BuscarProductosRequest $request): JsonResponse
    {
        $datos = $request->validated();
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
                'ubicacion',
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
            ->map(fn (Presentacion $presentacion) => $this->datosProducto($presentacion))
            ->values();

        return response()->json($presentaciones);
    }

    public function buscarClientes(BuscarClientesRequest $request): JsonResponse
    {
        $datos = $request->validated();
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
                ->orderBy('created_at')
                ->get(['id', 'nombre', 'tipo_captura'])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentaRequest $request, CrearVentaService $crearVentaService)
    {
        $datos = $request->validated();
        $tipoOperacion = $datos['tipo_operacion'] ?? 'VENTA';
        if ($tipoOperacion === 'VENTA') {
            $request->session()->put('ventas.carrito', $this->datosCarrito($datos));
        }

        $venta = $crearVentaService->crear($datos, (int) Auth::id());

        if ($tipoOperacion === 'VENTA') {
            $request->session()->forget('ventas.carrito');
        }

        return $tipoOperacion === 'VENTA'
            ? redirect()->back()->with('success', "La operación {$venta->numero_venta} se registró correctamente.")
            : redirect()->route('ventas.show', $venta)->with('success', "La operación {$venta->numero_venta} se registró correctamente.");
    }

    private function datosProducto(Presentacion $presentacion): array
    {
        return [
            'presentacion_id' => $presentacion->id,
            'nombre' => $presentacion->nombre,
            'sku' => $presentacion->codigoSKU,
            'ubicacion' => $presentacion->ubicacion,
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
        ];
    }

    private function datosCarrito(array $datos): array
    {
        return [
            'cliente_id' => isset($datos['cliente_id']) && $datos['cliente_id'] !== ''
                ? (int) $datos['cliente_id']
                : null,
            'detalles' => collect($datos['detalles'] ?? [])
                ->filter(fn (array $detalle) => ! empty($detalle['presentacion_id']))
                ->map(fn (array $detalle) => [
                    'presentacion_id' => (int) $detalle['presentacion_id'],
                    'cantidad' => $detalle['cantidad'] ?? '',
                    'tipo_precio' => $detalle['tipo_precio'] ?? 'unitario',
                    'descuento' => (string) ($detalle['descuento'] ?? ''),
                ])
                ->values()
                ->all(),
        ];
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
            'operacionesRelacionadas.detalles:id,venta_id,detalle_referencia_id,tipo_movimiento,cantidad,subtotal',
        ]);

        $totalProductos = round((float) $venta->detalles->sum(
            fn (VentaDetalle $detalle) => ((float) $detalle->subtotal + (float) $detalle->descuento)
                * ($detalle->tipo_movimiento === 'DEVOLUCION' ? -1 : 1)
        ), 2);
        $montoPagado = round((float) $venta->detallesPago->sum('monto'), 2);
        $saldoPendiente = round((float) $venta->total - $montoPagado, 2);
        $devolucionesRegistradas = $venta->operacionesRelacionadas
            ->where('estado_venta', 'REGISTRADA')
            ->flatMap(fn (Venta $operacion) => $operacion->detalles)
            ->where('tipo_movimiento', 'DEVOLUCION')
            ->groupBy('detalle_referencia_id');
        $detallesDisponiblesCambio = $venta->tipo_operacion === 'VENTA'
            ? $venta->detalles
                ->where('tipo_movimiento', 'VENTA')
                ->map(function (VentaDetalle $detalle) use ($devolucionesRegistradas) {
                    $devolucionesPrevias = $devolucionesRegistradas->get($detalle->id, collect());
                    $cantidadRestante = round(
                        (float) $detalle->cantidad - (float) $devolucionesPrevias->sum('cantidad'),
                        2
                    );

                    return [
                        'id' => $detalle->id,
                        'nombre' => $detalle->presentacion?->nombre ?? 'Presentación no disponible',
                        'sku' => $detalle->presentacion?->codigoSKU,
                        'tipo_item' => $detalle->tipo_item,
                        'cantidad_original' => (float) $detalle->cantidad,
                        'cantidad_disponible' => max(0, $cantidadRestante),
                        'factor' => (int) $detalle->factor,
                        'precio_aplicado' => (float) $detalle->precio_aplicado,
                        'subtotal_original' => (float) $detalle->subtotal,
                        'subtotal_disponible' => round(
                            (float) $detalle->subtotal - (float) $devolucionesPrevias->sum('subtotal'),
                            2
                        ),
                        'reingresa_inventario' => $detalle->tipo_item === 'PRODUCTO',
                    ];
                })
                ->filter(fn (array $detalle) => $detalle['cantidad_disponible'] > 0)
                ->values()
            : collect();

        $estadoPago = $venta->total < 0 && $venta->estado_pago === 'PAGADO'
            ? 'REEMBOLSADO'
            : $venta->estado_pago;
        $puedeCambiarOAnular = $this->estaDentroDelPlazoDeOperacion($venta);
        $puedeCambiar = $puedeCambiarOAnular && $saldoPendiente <= 0;
        $razonBloqueoCambio = ! $puedeCambiarOAnular
            ? 'El plazo de 5 días hábiles, excluyendo domingos, venció.'
            : ($saldoPendiente > 0
                ? 'No se puede cambiar o devolver una venta con saldo pendiente. Liquida el saldo primero.'
                : '');

        return view('ventas.show', compact(
            'venta',
            'totalProductos',
            'montoPagado',
            'saldoPendiente',
            'estadoPago',
            'detallesDisponiblesCambio',
            'puedeCambiarOAnular',
            'puedeCambiar',
            'razonBloqueoCambio'
        ));
    }

    public function registrarPago(RegistrarPagoVentaRequest $request, Venta $venta, RegistrarPagoVentaService $registrarPagoVentaService)
    {
        $registrarPagoVentaService->registrar($request->validated(), $venta, (int) Auth::id());

        return redirect()
            ->route('ventas.show', $venta)
            ->with('success', 'El movimiento de pago se registró correctamente.');
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

            if (! $this->estaDentroDelPlazoDeOperacion($venta)) {
                throw ValidationException::withMessages([
                    'venta' => 'El plazo para anular esta venta venció: son 5 días hábiles, excluyendo domingos.',
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
