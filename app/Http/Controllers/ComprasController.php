<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Presentacion;
use App\Models\Inventario;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        $compraPendiente = count(session('compras.borrador.detalles', [])) > 0;

        return view('compras.index', compact('compras', 'buscar', 'compraPendiente'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $borrador = session('compras.borrador', []);
        $detallesBorrador = collect($borrador['detalles'] ?? []);
        $presentaciones = Presentacion::query()
            ->with('inventario.marca')
            ->whereIn(
                'id',
                $detallesBorrador->pluck('presentacion_id')->filter()->unique()
            )
            ->where('base', true)
            ->where('factor', 1)
            ->where('estado', true)
            ->whereHas('inventario', function ($query) {
                $query->where('tipo', 'PRODUCTO')
                    ->where('estado', true);
            })
            ->get()
            ->keyBy('id');

        $detalles = $detallesBorrador
            ->map(function (array $detalle) use ($presentaciones) {
                $presentacion = $presentaciones->get($detalle['presentacion_id']);
                $inventario = $presentacion?->inventario;

                if (!$presentacion || !$inventario) {
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
                    'cantidad' => (int) $detalle['cantidad'],
                    'costo_unitario' => number_format((float) $detalle['costo_unitario'], 2, '.', ''),
                    'precio_unitario_nuevo' => number_format((float) $detalle['precio_unitario_nuevo'], 2, '.', ''),
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

        return view('compras.create', compact(
            'borrador',
            'detalles',
            'proveedores'
        ));
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
     * Guardar el borrador de compra en la sesión actual.
     */
    public function guardarBorrador(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'proveedor_id' => ['nullable', 'integer', 'exists:proveedores,id'],
            'numero_factura' => ['nullable', 'string', 'max:50'],
            'fecha_compra' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
            'detalles' => ['present', 'array', 'max:100'],
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

        $ids = collect($datos['detalles'])->pluck('presentacion_id');
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

        session()->put('compras.borrador', $datos);

        return response()->json(['guardado' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $borrador = session('compras.borrador', []);

        if (!empty($borrador['detalles'])) {
            $request->merge(['detalles' => $borrador['detalles']]);
        }

        $datos = $request->validate([
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'numero_factura' => ['nullable', 'string', 'max:50'],
            'fecha_compra' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'detalles' => ['required', 'array', 'min:1'],
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

        DB::transaction(function () use ($datos) {
            $compra = Compra::create([
                'numero_compra' => 'TMP-' . Str::random(15),
                'usuario_id' => Auth::id(),
                'proveedor_id' => $datos['proveedor_id'] ?? null,
                'numero_factura' => $datos['numero_factura'] ?? null,
                'fecha_compra' => $datos['fecha_compra'],
                'observaciones' => $datos['observaciones'] ?? null,
                'costo_total' => 0,
                'cantidad_total' => 0,
                'estado' => 'REGISTRADA',
            ]);

            $compra->update([
                'numero_compra' => 'COMP-' . str_pad(
                    (string) $compra->id,
                    10,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            $costoTotal = 0;
            $cantidadTotal = 0;

            foreach ($datos['detalles'] as $detalle) {
                // Solo se permite la presentación base.
                $presentacion = Presentacion::query()
                    ->whereKey($detalle['presentacion_id'])
                    ->where('base', true)
                    ->where('factor', 1)
                    ->where('estado', true)
                    ->whereHas('inventario', function ($query) {
                        $query->where('tipo', 'PRODUCTO')
                            ->where('estado', true);
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$presentacion) {
                    throw ValidationException::withMessages([
                        'detalles' => 'Todos los artículos deben utilizar su presentación base.',
                    ]);
                }

                // Bloquear el inventario para evitar cálculos concurrentes.
                $inventario = Inventario::query()
                    ->whereKey($presentacion->inventario_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($inventario->tipo !== 'PRODUCTO') {
                    throw ValidationException::withMessages([
                        'detalles' => 'No se pueden comprar servicios.',
                    ]);
                }

                $cantidadAnterior = (int) $inventario->cantidad;
                $costoAnterior = (float) $inventario->costoPromedioUnitario;

                $cantidad = (int) $detalle['cantidad'];
                $costoUnitario = round(
                    (float) $detalle['costo_unitario'],
                    2
                );
                $precioUnitarioNuevo = round(
                    (float) $detalle['precio_unitario_nuevo'],
                    2
                );
                $precioDocenaNuevo = $detalle['precio_docena_nuevo'] ?? null;
                $precioCientoNuevo = $detalle['precio_ciento_nuevo'] ?? null;

                $subtotal = round($cantidad * $costoUnitario, 2);

                // Calcular el nuevo costo promedio ponderado.
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

                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'presentacion_id' => $presentacion->id,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costoUnitario,
                    'costo_promedio_anterior' => $costoAnterior,
                    'costo_promedio_nuevo' => $costoNuevo,
                    'precio_unitario_anterior' => $presentacion->precioUnitario,
                    'precio_unitario_nuevo' => $precioUnitarioNuevo,
                    'subtotal' => $subtotal,
                ]);

                $presentacion->precioUnitario = $precioUnitarioNuevo;
                $presentacion->precioUnitarioDocena = $precioDocenaNuevo;
                $presentacion->precioUnitarioCiento = $precioCientoNuevo;
                $presentacion->save();

                // Actualizar las existencias y el costo promedio.
                $inventario->cantidad = $cantidadNueva;
                $inventario->costoPromedioUnitario = $costoNuevo;
                $inventario->save();

                $costoTotal += $subtotal;
                $cantidadTotal += $cantidad;
            }

            $compra->update([
                'costo_total' => round($costoTotal, 2),
                'cantidad_total' => $cantidadTotal,
            ]);
        });

        session()->forget('compras.borrador');

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra se registró correctamente.');
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
