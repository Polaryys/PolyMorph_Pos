<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Marca;
use App\Models\Categoria;
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
        // Validar parámetros de búsqueda y filtros.
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', 'integer', 'exists:categorias,id'],
            'marca' => ['nullable', 'integer', 'exists:marcas,id'],
        ]);

        $buscar = trim($filtros['buscar'] ?? '');
        $categoriaId = $filtros['categoria'] ?? null;
        $marcaId = $filtros['marca'] ?? null;

        $presentaciones = Presentacion::with([
            'inventario.presentaciones' => function ($query) {
                $query->where('base', true)
                    ->select('id', 'inventario_id', 'nombre');
            },
            'imagen',
        ])
            ->where('base', false)

            // Búsqueda por datos de la presentación o presentación base.
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {

                    $query->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigoSKU', 'like', "%{$buscar}%")
                        ->orWhere('codigoBarras', 'like', "%{$buscar}%")

                        ->orWhereHas('inventario.presentaciones', function ($subquery) use ($buscar) {
                            $subquery->where('base', true)
                                ->where('nombre', 'like', "%{$buscar}%");
                        });
                });
            })

            // Filtrar por categoría del producto asociado.
            ->when($categoriaId, function ($query) use ($categoriaId) {
                $query->whereHas('inventario', function ($subquery) use ($categoriaId) {
                    $subquery->where('categoria_id', $categoriaId);
                });
            })

            // Filtrar por marca del producto asociado.
            ->when($marcaId, function ($query) use ($marcaId) {
                $query->whereHas('inventario', function ($subquery) use ($marcaId) {
                    $subquery->where('marca_id', $marcaId);
                });
            })

            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Opciones disponibles para los filtros.
        $categorias = Categoria::orderBy('nombre')
            ->get(['id', 'nombre']);

        $marcas = Marca::orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('presentaciones.index', compact(
            'presentaciones',
            'buscar',
            'categoriaId',
            'marcaId',
            'categorias',
            'marcas'
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
                    'inventario:id,cantidad,costoPromedioUnitario',
                ])
                ->first();

            if ($presentacionBase) {
                $productoSeleccionado = [
                    'inventario_id' => $presentacionBase->inventario_id,
                    'nombre' => $presentacionBase->nombre,
                    'sku' => $presentacionBase->codigoSKU,
                    'codigoBarras' => $presentacionBase->codigoBarras,
                    'cantidad' => $presentacionBase->inventario?->cantidad ?? 0,
                    'precioUnitario' => (float) $presentacionBase->precioUnitario,
                    'costoPromedioUnitario' => (float) ($presentacionBase->inventario?->costoPromedioUnitario ?? 0),
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
            ->buscarProductosActivos($buscar)
            ->select([
                'id',
                'inventario_id',
                'nombre',
                'precioUnitario',
                'codigoSKU',
                'codigoBarras',
                'imagen_id',
            ])
            ->with([
                'imagen:id,nombre_archivo',
                'inventario:id,marca_id,cantidad,costoPromedioUnitario',
                'inventario.marca:id,nombre',
            ])
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(fn($presentacion) => [
                'inventario_id' => $presentacion->inventario_id,
                'nombre' => $presentacion->nombre,
                'sku' => $presentacion->codigoSKU,
                'codigoBarras' => $presentacion->codigoBarras,
                'marcaNombre' => $presentacion->inventario?->marca?->nombre,
                'cantidad' => $presentacion->inventario?->cantidad ?? 0,
                'precioUnitario' => (float) $presentacion->precioUnitario,
                'costoPromedioUnitario' => (float) ($presentacion->inventario?->costoPromedioUnitario ?? 0),
                'imagen' => $presentacion->imagen
                    ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                    : null,
            ])
            ->values();

        return response()->json($presentaciones);
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
        $historialPrecios = $presentacion->historialPrecios()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('presentaciones.show', compact(
            'presentacion',
            'costoPromedioPresentacion',
            'historialPrecios'
        ));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Presentacion $presentacion)
    {
        abort_if($presentacion->base, 404);

        $presentacion->load(['inventario.marca', 'imagen']);
        $imagenes = Imagen::orderBy('id', 'desc')->get();
        $imagenId = old('imagen_id', $presentacion->imagen_id);
        $presentacion->setRelation(
            'imagen',
            $imagenId ? Imagen::find($imagenId) : null
        );

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

        if ($presentacion->ventaDetalles()->exists()) {
            $presentacion->update(['estado' => false]);

            return redirect()
                ->route('presentaciones.index')
                ->with('success', 'La presentación tiene operaciones registradas y fue desactivada correctamente.');
        }

        $presentacion->delete();

        return redirect()
            ->route('presentaciones.index')
            ->with('success', 'Presentación eliminada correctamente.');
    }
}
