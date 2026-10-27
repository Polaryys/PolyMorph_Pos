<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Presentacion;
use App\Models\Imagen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PresentacionController extends Controller
{
    /**
     * Listar presentaciones adicionales.
     */

    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $presentaciones = Presentacion::with([
            'inventario.presentaciones' => function ($query) {
                $query->where('base', true)
                    ->select('id', 'inventario_id', 'nombre');
            },
            'imagen',
        ])
            ->where('base', false)
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {

                    // Buscar en los datos de la presentación adicional.
                    $query->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigoSKU', 'like', "%{$buscar}%")
                        ->orWhere('codigoBarras', 'like', "%{$buscar}%")

                        // Buscar por el nombre de la presentación base.
                        ->orWhereHas('inventario.presentaciones', function ($subquery) use ($buscar) {
                        $subquery->where('base', true)
                            ->where('nombre', 'like', "%{$buscar}%");
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('presentaciones.index', compact(
            'presentaciones',
            'buscar'
        ));
    }


    /**
     * Mostrar formulario de creación.
     */

    public function create(Request $request)
    {
        $productoSeleccionado = null;
        $inventarioId = $request->old('inventario_id');

        if ($inventarioId) {
            $presentacionBase = Presentacion::query()
                ->where('inventario_id', $inventarioId)
                ->where('base', true)
                ->where('estado', true)
                ->whereHas('inventario', function ($query) {
                    $query->where('tipo', 'PRODUCTO')
                        ->where('estado', true);
                })
                ->with([
                    'imagen:id,nombre_archivo',
                    'inventario:id,cantidad',
                ])
                ->first();

            if ($presentacionBase) {
                $productoSeleccionado = [
                    'inventario_id' => $presentacionBase->inventario_id,
                    'nombre' => $presentacionBase->nombre,
                    'sku' => $presentacionBase->codigoSKU,
                    'codigoBarras' => $presentacionBase->codigoBarras,
                    'cantidad' => $presentacionBase->inventario?->cantidad ?? 0,
                    'imagen' => $presentacionBase->imagen
                        ? asset(
                            'storage/imagenes/' .
                            $presentacionBase->imagen->nombre_archivo
                        )
                        : null,
                ];
            }
        }

        return view('presentaciones.create', compact('productoSeleccionado'));
    }

    /**
     * Buscar presentaciones base de productos activos.
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
                'codigoBarras',
                'imagen_id',
            ])
            ->where('base', true)
            ->where('estado', true)
            ->whereHas('inventario', function ($query) {
                $query->where('tipo', 'PRODUCTO')
                    ->where('estado', true);
            })
            ->with([
                'imagen:id,nombre_archivo',
                'inventario:id,cantidad',
            ])
            ->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigoSKU', 'like', "%{$buscar}%")
                    ->orWhere('codigoBarras', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->limit(10)
            ->get();

        return response()->json(
            $presentaciones->map(function ($presentacion) {
                return [
                    'inventario_id' => $presentacion->inventario_id,
                    'nombre' => $presentacion->nombre,
                    'sku' => $presentacion->codigoSKU,
                    'codigoBarras' => $presentacion->codigoBarras,

                    // Existencias del inventario
                    'cantidad' => $presentacion->inventario?->cantidad ?? 0,

                    'imagen' => $presentacion->imagen
                        ? asset(
                            'storage/imagenes/' .
                            $presentacion->imagen->nombre_archivo
                        )
                        : null,
                ];
            })->values()
        );
    }

    /**
     * Guardar presentación adicional.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'inventario_id' => [
                'required',
                'integer',
                Rule::exists('inventario', 'id')->where(
                    fn($query) => $query->where('tipo', 'PRODUCTO')
                ),
            ],

            'codigoSKU' => [
                'required',
                'string',
                'max:50',
                'unique:presentaciones,codigoSKU',
            ],

            'codigoBarras' => [
                'nullable',
                'string',
                'max:50',
                'unique:presentaciones,codigoBarras',
            ],

            'nombre' => ['required', 'string', 'max:100'],
            'detalle' => ['nullable', 'string'],
            'factor' => ['required', 'integer', 'min:1'],
            'ubicacion' => ['nullable', 'string', 'max:100'],

            'precioUnitario' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'imagen_id' => [
                'nullable',
                'integer',
                'exists:imagenes,id',
            ],

            'fecha_vencimiento' => [
                'nullable',
                'date',
            ],
        ]);

        $tieneBase = Presentacion::where(
            'inventario_id',
            $datos['inventario_id']
        )
            ->where('base', true)
            ->exists();

        if (!$tieneBase) {
            return back()
                ->withInput()
                ->withErrors([
                    'inventario_id' =>
                        'El producto debe tener una presentación base antes de agregar otra.',
                ]);
        }

        DB::transaction(function () use ($datos) {
            Presentacion::create([
                'inventario_id' => $datos['inventario_id'],
                'codigoSKU' => $datos['codigoSKU'],
                'codigoBarras' => $datos['codigoBarras'] ?? null,
                'nombre' => $datos['nombre'],
                'detalle' => $datos['detalle'] ?? null,
                'factor' => $datos['factor'],
                'ubicacion' => $datos['ubicacion'] ?? null,

                // Una presentación adicional nunca es base.
                'base' => false,

                'precioUnitario' => $datos['precioUnitario'],
                'precioUnitarioDocena' => null,
                'precioUnitarioCiento' => null,

                'imagen_id' => $datos['imagen_id'] ?? null,
                'fecha_vencimiento' =>
                    $datos['fecha_vencimiento'] ?? null,

                // Se activa automáticamente.
                'estado' => true,
            ]);
        });

        return redirect()
            ->route('presentaciones.index')
            ->with('success', 'Presentación adicional creada correctamente.');
    }

    /**
     * Mostrar presentación.
     */
    public function show(Presentacion $presentacion)
    {
        abort_if($presentacion->base, 404);

        $presentacion->load([
            'inventario.categoria',
            'inventario.marca',
            'inventario.presentaciones' => function ($query) {
                $query->where('base', true);
            },
            'imagen',
        ]);

        $costoPromedioPresentacion =
            (float) ($presentacion->inventario?->costoPromedioUnitario ?? 0)
            * (int) ($presentacion->factor ?? 0);

        return view('presentaciones.show', compact(
            'presentacion',
            'costoPromedioPresentacion'
        ));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Presentacion $presentacion)
    {
        abort_if($presentacion->base, 404);

        $presentacion->load(['inventario', 'imagen']);
        $imagenes = Imagen::orderBy('id', 'desc')->get();

        return view('presentaciones.edit', compact(
            'presentacion',
            'imagenes'
        ));
    }

    /**
     * Actualizar presentación adicional.
     */
    public function update(Request $request, Presentacion $presentacion)
    {
        abort_if($presentacion->base, 404);

        $datos = $request->validate([
            'codigoSKU' => [
                'required',
                'string',
                'max:50',
                Rule::unique('presentaciones', 'codigoSKU')
                    ->ignore($presentacion->id),
            ],

            'codigoBarras' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('presentaciones', 'codigoBarras')
                    ->ignore($presentacion->id),
            ],

            'nombre' => ['required', 'string', 'max:100'],
            'detalle' => ['nullable', 'string'],
            'factor' => ['required', 'integer', 'min:1'],
            'ubicacion' => ['nullable', 'string', 'max:100'],

            'precioUnitario' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'precioUnitarioDocena' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'precioUnitarioCiento' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'imagen_id' => [
                'nullable',
                'integer',
                'exists:imagenes,id',
            ],

            'fecha_vencimiento' => [
                'nullable',
                'date',
            ],

            'estado' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($datos, $presentacion) {
            $presentacion->update([
                'codigoSKU' => $datos['codigoSKU'],
                'codigoBarras' => $datos['codigoBarras'] ?? null,
                'nombre' => $datos['nombre'],
                'detalle' => $datos['detalle'] ?? null,
                'factor' => $datos['factor'],
                'ubicacion' => $datos['ubicacion'] ?? null,
                'precioUnitario' => $datos['precioUnitario'],
                'precioUnitarioDocena' => $datos['precioUnitarioDocena'] ?? null,
                'precioUnitarioCiento' => $datos['precioUnitarioCiento'] ?? null,
                'imagen_id' => $datos['imagen_id'] ?? null,
                'fecha_vencimiento' =>
                    $datos['fecha_vencimiento'] ?? null,
                'estado' => $datos['estado'],
                'base' => false,
            ]);
        });

        return redirect()
            ->route('presentaciones.index')
            ->with('success', 'Presentación actualizada correctamente.');
    }

    /**
     * Desactivar presentación adicional.
     */
    public function destroy(Presentacion $presentacion)
    {
        abort_if($presentacion->base, 404);

        $presentacion->update(['estado' => false]);

        return redirect()
            ->route('presentaciones.index')
            ->with('success', 'Presentación desactivada correctamente.');
    }
}
