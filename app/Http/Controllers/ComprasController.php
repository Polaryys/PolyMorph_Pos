<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Inventario;
use App\Models\Movimiento;
use App\Models\Presentacion;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ComprasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $compras = Compra::query()
            ->with('proveedor')
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('numero_compra', 'like', "%{$buscar}%")
                        ->orWhereHas('proveedor', function ($query) use ($buscar) {
                            $query->where('nombre', 'like', "%{$buscar}%");
                        });
                });
            })
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('compras.index', compact('compras', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->formularioCompra(null);
    }

    /**
     * Buscar productos con presentación base para agregarlos a una compra.
     */
    public function buscarProductos(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'buscar' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $buscar = trim($datos['buscar']);

        $presentaciones = Presentacion::query()
            ->select([
                'id',
                'inventario_id',
                'nombre',
                'codigoSKU',
                'precioUnitario',
                'precioUnitarioDocena',
                'precioUnitarioCiento',
            ])
            ->where('base', true)
            ->where('factor', 1)
            ->where('estado', true)
            ->whereHas('inventario', function ($query) {
                $query->where('tipo', 'PRODUCTO')
                    ->where('estado', true);
            })
            ->with([
                'inventario:id,marca_id,cantidad,costoPromedioUnitario,tipo,estado',
                'inventario.marca:id,nombre',
            ])
            ->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigoSKU', 'like', "%{$buscar}%")
                    ->orWhereHas('inventario.marca', function ($query) use ($buscar) {
                        $query->where('nombre', 'like', "%{$buscar}%");
                    });
            })
            ->orderBy('nombre')
            ->limit(10)
            ->get();

        return response()->json($presentaciones->map(function ($presentacion) {
            return [
                'presentacion_id' => $presentacion->id,
                'nombre' => $presentacion->nombre,
                'sku' => $presentacion->codigoSKU,
                'marca' => $presentacion->inventario?->marca?->nombre ?? 'Sin marca',
                'cantidad_actual' => (int) ($presentacion->inventario?->cantidad ?? 0),
                'costo_promedio_actual' => (float) ($presentacion->inventario?->costoPromedioUnitario ?? 0),
                'precio_unitario_actual' => (float) $presentacion->precioUnitario,
                'precio_docena_actual' => $presentacion->precioUnitarioDocena === null
                    ? null
                    : (float) $presentacion->precioUnitarioDocena,
                'precio_ciento_actual' => $presentacion->precioUnitarioCiento === null
                    ? null
                    : (float) $presentacion->precioUnitarioCiento,
            ];
        })->values());
    }

    /**
     * Store a purchase as pending without changing inventory or prices.
     */
    public function store(Request $request)
    {
        $datos = $this->validarDatosCompra($request);
        $this->validarPresentacionesCompra($datos['detalles']);

        $compra = DB::transaction(function () use ($datos) {
            $compra = Compra::create([
                'numero_compra' => 'TMP-'.Str::random(15),
                'usuario_id' => Auth::id(),
                'proveedor_id' => $datos['proveedor_id'] ?? null,
                'numero_factura' => $datos['numero_factura'] ?? null,
                'fecha_compra' => $datos['fecha_compra'],
                'observaciones' => $datos['observaciones'] ?? null,
                'costo_total' => 0,
                'cantidad_total' => 0,
                'estado' => 'PENDIENTE',
            ]);

            $compra->update([
                'numero_compra' => 'COMP-'.str_pad(
                    (string) $compra->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            $this->guardarDetallesPendientes($compra, $datos['detalles']);

            return $compra;
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra se guardó como pendiente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Compra $compra)
    {
        $compra->load([
            'proveedor',
            'usuario',
            'detalles.presentacion.inventario.marca',
        ]);

        return view('compras.show', compact('compra'));
    }

    public function edit(Compra $compra)
    {
        abort_unless($compra->estado === 'PENDIENTE', 404);

        return $this->formularioCompra($compra);
    }

    public function update(Request $request, Compra $compra)
    {
        $datos = $this->validarDatosCompra($request);

        DB::transaction(function () use ($datos, $compra) {
            $compra = Compra::query()
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($compra->estado !== 'PENDIENTE') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden editar compras pendientes.',
                ]);
            }

            $this->validarPresentacionesCompra($datos['detalles']);

            $compra->fill([
                'proveedor_id' => $datos['proveedor_id'] ?? null,
                'numero_factura' => $datos['numero_factura'] ?? null,
                'fecha_compra' => $datos['fecha_compra'],
                'observaciones' => $datos['observaciones'] ?? null,
            ])->save();

            $compra->detalles()->delete();
            $this->guardarDetallesPendientes($compra, $datos['detalles']);
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra pendiente se actualizó correctamente.');
    }

    public function destroy(Compra $compra)
    {
        DB::transaction(function () use ($compra) {
            $compra = Compra::query()
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($compra->estado !== 'PENDIENTE') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden eliminar compras pendientes.',
                ]);
            }

            $compra->delete();
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra pendiente se eliminó correctamente.');
    }

    public function registrarCompra(Compra $compra)
    {
        DB::transaction(function () use ($compra) {
            $compra = Compra::query()
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($compra->estado !== 'PENDIENTE') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden registrar compras pendientes.',
                ]);
            }

            $detalles = $compra->detalles()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'compra' => 'Agrega al menos un producto antes de registrar la compra.',
                ]);
            }

            $detalleInvalido = $detalles->contains(fn (CompraDetalle $detalle) => (int) $detalle->cantidad < 1
                || (float) $detalle->costo_unitario <= 0
                || $detalle->precio_unitario_nuevo === null
                || (float) $detalle->precio_unitario_nuevo < 0
                || ($detalle->precio_docena_nuevo !== null && (float) $detalle->precio_docena_nuevo < 0)
                || ($detalle->precio_ciento_nuevo !== null && (float) $detalle->precio_ciento_nuevo < 0)
            );

            if ($detalleInvalido) {
                throw ValidationException::withMessages([
                    'compra' => 'Los datos de uno o más productos son inválidos. Edita la compra antes de registrarla.',
                ]);
            }

            $presentaciones = Presentacion::query()
                ->whereIn('id', $detalles->pluck('presentacion_id')->unique())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $idsPresentaciones = $detalles->pluck('presentacion_id')->unique();
            $presentacionesValidas = $presentaciones->count() === $idsPresentaciones->count()
                && $presentaciones->every(fn (Presentacion $presentacion) => $presentacion->base
                    && (int) $presentacion->factor === 1
                    && $presentacion->estado
                );

            if (! $presentacionesValidas) {
                throw ValidationException::withMessages([
                    'compra' => 'Uno o más productos ya no están disponibles para registrar.',
                ]);
            }

            $inventarios = Inventario::query()
                ->whereIn(
                    'id',
                    $presentaciones->pluck('inventario_id')->unique()
                )
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $costoTotal = 0;
            $cantidadTotal = 0;

            foreach ($detalles as $detalle) {
                $presentacion = $presentaciones->get($detalle->presentacion_id);
                $inventario = $inventarios->get($presentacion->inventario_id);

                if (! $inventario || $inventario->tipo !== 'PRODUCTO' || ! $inventario->estado) {
                    throw ValidationException::withMessages([
                        'compra' => 'Uno o más productos ya no están disponibles para registrar.',
                    ]);
                }

                $cantidadAnterior = (int) $inventario->cantidad;
                $costoAnterior = (float) $inventario->costoPromedioUnitario;
                $cantidad = (int) $detalle->cantidad;
                $costoUnitario = (float) $detalle->costo_unitario;
                $cantidadNueva = $cantidadAnterior + $cantidad;
                $costoNuevo = $cantidadAnterior === 0
                    ? $costoUnitario
                    : round(
                        (
                            ($cantidadAnterior * $costoAnterior)
                            + ($cantidad * $costoUnitario)
                        ) / $cantidadNueva,
                        2
                    );
                $subtotal = round($cantidad * $costoUnitario, 2);

                $detalle->costo_promedio_anterior = $costoAnterior;
                $detalle->costo_promedio_nuevo = $costoNuevo;
                $detalle->subtotal = $subtotal;
                $detalle->save();

                $presentacion->precioUnitario = $detalle->precio_unitario_nuevo;
                $presentacion->precioUnitarioDocena = $detalle->precio_docena_nuevo;
                $presentacion->precioUnitarioCiento = $detalle->precio_ciento_nuevo;
                $presentacion->save();

                $inventario->cantidad = $cantidadNueva;
                $inventario->costoPromedioUnitario = $costoNuevo;
                DB::statement('SET @usuario_id = ?, @motivo = ?', [
                    Auth::id(),
                    'Compra '.$compra->numero_compra,
                ]);

                try {
                    $inventario->save();
                } finally {
                    DB::statement('SET @usuario_id = NULL, @motivo = NULL');
                }

                $costoTotal += $subtotal;
                $cantidadTotal += $cantidad;
            }

            $compra->costo_total = round($costoTotal, 2);
            $compra->cantidad_total = $cantidadTotal;
            $compra->estado = 'REGISTRADA';
            $compra->save();
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra se registró correctamente.');
    }

    /**
     * Reverse a registered purchase when its inventory movements are still current.
     */
    public function revertirCompra(Compra $compra)
    {
        DB::transaction(function () use ($compra) {
            $compra = Compra::query()
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($compra->estado !== 'REGISTRADA') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden revertir compras registradas.',
                ]);
            }

            $detalles = $compra->detalles()
                ->with('presentacion:id,inventario_id,nombre')
                ->orderBy('id')
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'compra' => 'No se puede revertir una compra sin detalles.',
                ]);
            }

            $detallesPorInventario = $detalles->groupBy(
                fn (CompraDetalle $detalle) => $detalle->presentacion->inventario_id
            );
            $inventarios = Inventario::query()
                ->whereIn('id', $detallesPorInventario->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $motivoCompra = 'Compra '.$compra->numero_compra;

            foreach ($detallesPorInventario as $inventarioId => $detallesInventario) {
                $inventario = $inventarios->get($inventarioId);

                if (! $inventario || $inventario->tipo !== 'PRODUCTO') {
                    throw ValidationException::withMessages([
                        'compra' => 'No se puede revertir: uno de los productos ya no está disponible.',
                    ]);
                }

                $cantidadCompra = (int) $detallesInventario->sum('cantidad');
                $nombreProducto = $detallesInventario->first()->presentacion->nombre;

                if ((int) $inventario->cantidad < $cantidadCompra) {
                    throw ValidationException::withMessages([
                        'compra' => "No se puede revertir {$nombreProducto}: las existencias actuales son insuficientes.",
                    ]);
                }

                $movimientosCompra = Movimiento::query()
                    ->where('inventario_id', $inventarioId)
                    ->where('motivo', $motivoCompra)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($movimientosCompra->count() !== $detallesInventario->count()) {
                    throw ValidationException::withMessages([
                        'compra' => "No se puede verificar el historial de movimientos de {$nombreProducto}.",
                    ]);
                }

                foreach ($detallesInventario->values() as $indice => $detalle) {
                    $movimiento = $movimientosCompra->get($indice);

                    if (
                        $movimiento->tipo_movimiento !== 'ENTRADA'
                        || (int) $movimiento->cantidad !== (int) $detalle->cantidad
                        || (int) $movimiento->cantidad_posterior
                            !== (int) $movimiento->cantidad_anterior + (int) $detalle->cantidad
                        || (
                            $indice > 0
                            && (int) $movimientosCompra[$indice - 1]->cantidad_posterior
                                !== (int) $movimiento->cantidad_anterior
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'compra' => "No se puede verificar el historial de movimientos de {$nombreProducto}.",
                        ]);
                    }
                }

                $ultimoMovimiento = $movimientosCompra->last();
                $detalleFinal = $detallesInventario->last();

                $hayMovimientosPosteriores = Movimiento::query()
                    ->where('inventario_id', $inventarioId)
                    ->where('id', '>', $ultimoMovimiento->id)
                    ->exists();

                if (
                    $hayMovimientosPosteriores
                    || (int) $inventario->cantidad !== (int) $ultimoMovimiento->cantidad_posterior
                    || round((float) $inventario->costoPromedioUnitario, 2)
                        !== round((float) $detalleFinal->costo_promedio_nuevo, 2)
                ) {
                    throw ValidationException::withMessages([
                        'compra' => "No se puede revertir {$nombreProducto}: tiene cambios de inventario posteriores.",
                    ]);
                }

                $inventario->cantidad = (int) $inventario->cantidad - $cantidadCompra;
                $inventario->costoPromedioUnitario =
                    $detallesInventario->first()->costo_promedio_anterior;

                DB::statement('SET @usuario_id = ?, @motivo = ?', [
                    Auth::id(),
                    'Reversión de compra '.$compra->numero_compra,
                ]);

                try {
                    $inventario->save();
                } finally {
                    DB::statement('SET @usuario_id = NULL, @motivo = NULL');
                }
            }

            $compra->estado = 'REVERSADA';
            $compra->save();
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra se revirtió correctamente.');
    }

    private function validarDatosCompra(Request $request): array
    {
        return $request->validate([
            'proveedor_id' => ['nullable', 'integer', 'exists:proveedores,id'],
            'numero_factura' => ['nullable', 'string', 'max:50'],
            'fecha_compra' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'detalles' => ['required', 'array', 'min:1', 'max:100'],
            'detalles.*.presentacion_id' => [
                'required',
                'integer',
                'distinct',
                'exists:presentaciones,id',
            ],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.costo_unitario' => [
                'required',
                'numeric',
                'gt:0',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'detalles.*.precio_unitario_nuevo' => [
                'required',
                'numeric',
                'min:0',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'detalles.*.precio_docena_nuevo' => [
                'nullable',
                'numeric',
                'min:0',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'detalles.*.precio_ciento_nuevo' => [
                'nullable',
                'numeric',
                'min:0',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
        ]);
    }

    private function validarPresentacionesCompra(array $detalles): void
    {
        $ids = collect($detalles)->pluck('presentacion_id');
        $productosValidos = Presentacion::query()
            ->whereIn('id', $ids)
            ->where('base', true)
            ->where('factor', 1)
            ->where('estado', true)
            ->whereHas('inventario', function ($query) {
                $query->where('tipo', 'PRODUCTO')
                    ->where('estado', true);
            })
            ->count();

        if ($productosValidos !== $ids->count()) {
            throw ValidationException::withMessages([
                'detalles' => 'Uno o más productos ya no están disponibles para compra.',
            ]);
        }
    }

    private function guardarDetallesPendientes(Compra $compra, array $detalles): void
    {
        $costoTotal = 0;
        $cantidadTotal = 0;

        foreach ($detalles as $detalle) {
            $cantidad = (int) $detalle['cantidad'];
            $costoUnitario = round((float) $detalle['costo_unitario'], 2);
            $subtotal = round($cantidad * $costoUnitario, 2);

            $compra->detalles()->create([
                'presentacion_id' => $detalle['presentacion_id'],
                'cantidad' => $cantidad,
                'costo_unitario' => $costoUnitario,
                'costo_promedio_anterior' => null,
                'costo_promedio_nuevo' => null,
                'precio_unitario_nuevo' => round((float) $detalle['precio_unitario_nuevo'], 2),
                'precio_docena_nuevo' => isset($detalle['precio_docena_nuevo'])
                    ? round((float) $detalle['precio_docena_nuevo'], 2)
                    : null,
                'precio_ciento_nuevo' => isset($detalle['precio_ciento_nuevo'])
                    ? round((float) $detalle['precio_ciento_nuevo'], 2)
                    : null,
                'subtotal' => $subtotal,
            ]);

            $costoTotal += $subtotal;
            $cantidadTotal += $cantidad;
        }

        $compra->costo_total = round($costoTotal, 2);
        $compra->cantidad_total = $cantidadTotal;
        $compra->save();
    }

    private function formularioCompra(?Compra $compra)
    {
        $compra?->loadMissing('detalles');
        $detallesEntrada = old('detalles');

        if ($detallesEntrada !== null && ! is_array($detallesEntrada)) {
            $detallesEntrada = [];
        }

        if ($detallesEntrada === null) {
            $detallesEntrada = $compra
                ? $compra->detalles->map(fn (CompraDetalle $detalle) => [
                    'presentacion_id' => $detalle->presentacion_id,
                    'cantidad' => $detalle->cantidad,
                    'costo_unitario' => $detalle->costo_unitario,
                    'precio_unitario_nuevo' => $detalle->precio_unitario_nuevo,
                    'precio_docena_nuevo' => $detalle->precio_docena_nuevo,
                    'precio_ciento_nuevo' => $detalle->precio_ciento_nuevo,
                ])->all()
                : [];
        } else {
            $detallesEntrada = collect($detallesEntrada)
                ->filter(fn ($detalle) => is_array($detalle) && isset($detalle['presentacion_id']))
                ->values()
                ->all();
        }

        $presentaciones = Presentacion::query()
            ->with('inventario.marca')
            ->whereIn(
                'id',
                collect($detallesEntrada)->pluck('presentacion_id')->filter()->unique()
            )
            ->get()
            ->keyBy('id');

        $detalles = collect($detallesEntrada)
            ->map(function (array $detalle) use ($presentaciones) {
                $presentacion = $presentaciones->get($detalle['presentacion_id']);
                $inventario = $presentacion?->inventario;

                if (! $presentacion || ! $inventario) {
                    return null;
                }

                return [
                    'presentacion_id' => $presentacion->id,
                    'nombre' => $presentacion->nombre,
                    'sku' => $presentacion->codigoSKU,
                    'marca' => $inventario->marca?->nombre ?? 'Sin marca',
                    'cantidad_actual' => (int) $inventario->cantidad,
                    'costo_promedio_actual' => number_format((float) $inventario->costoPromedioUnitario, 2, '.', ''),
                    'precio_unitario_actual' => number_format((float) $presentacion->precioUnitario, 2, '.', ''),
                    'precio_docena_actual' => $presentacion->precioUnitarioDocena === null
                        ? null
                        : number_format((float) $presentacion->precioUnitarioDocena, 2, '.', ''),
                    'precio_ciento_actual' => $presentacion->precioUnitarioCiento === null
                        ? null
                        : number_format((float) $presentacion->precioUnitarioCiento, 2, '.', ''),
                    'cantidad' => (int) ($detalle['cantidad'] ?? 0),
                    'costo_unitario' => number_format((float) ($detalle['costo_unitario'] ?? 0), 2, '.', ''),
                    'precio_unitario_nuevo' => number_format((float) ($detalle['precio_unitario_nuevo'] ?? 0), 2, '.', ''),
                    'precio_docena_nuevo' => isset($detalle['precio_docena_nuevo'])
                        ? number_format((float) $detalle['precio_docena_nuevo'], 2, '.', '')
                        : '',
                    'precio_ciento_nuevo' => isset($detalle['precio_ciento_nuevo'])
                        ? number_format((float) $detalle['precio_ciento_nuevo'], 2, '.', '')
                        : '',
                ];
            })
            ->filter()
            ->values();

        $proveedores = Proveedor::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('compras.create', compact('compra', 'detalles', 'proveedores'));
    }
}
