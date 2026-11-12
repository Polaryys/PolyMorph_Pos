<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Inventario;
use App\Models\Imagen;
use App\Models\Movimiento;
use App\Models\Presentacion;
use App\Services\ImportarInventarioInicialCsv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class InventarioController extends Controller
{
    public function mostrarAjusteInventario()
    {
        return view('articulos.ajuste-inventario');
    }

    /**
     * Buscar productos activos por su presentación base.
     */
    public function buscarProductosParaAjuste(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'buscar' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $buscar = trim($datos['buscar']);

        if ($buscar === '') {
            return response()->json([]);
        }

        $productos = Presentacion::query()
            ->buscarProductosActivos($buscar)
            ->select([
                'id',
                'inventario_id',
                'nombre',
                'codigoSKU',
                'imagen_id',
            ])
            ->with([
                'inventario:id,marca_id,cantidad',
                'inventario.marca:id,nombre',
                'imagen:id,nombre_archivo',
            ])
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(fn($presentacion) => [
                'inventario_id' => $presentacion->inventario_id,
                'nombre' => $presentacion->nombre,
                'sku' => $presentacion->codigoSKU,
                'marcaNombre' => $presentacion->inventario?->marca?->nombre,
                'imagen' => $presentacion->imagen
                    ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                    : null,
                'cantidad' => (int) ($presentacion->inventario?->cantidad ?? 0),
            ])
            ->values();

        return response()->json($productos);
    }

    /**
     * Actualizar la existencia al conteo físico indicado y guardar el movimiento.
     */
    public function ajustarInventario(Request $request)
    {
        $datos = $request->validate([
            'inventario_id' => ['required', 'integer', 'exists:inventario,id'],
            'cantidad' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($datos) {
            $inventario = Inventario::query()
                ->whereKey($datos['inventario_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $tienePresentacionBaseActiva = $inventario->presentaciones()
                ->where('base', true)
                ->where('estado', true)
                ->lockForUpdate()
                ->exists();

            if (
                $inventario->tipo !== 'PRODUCTO'
                || !$inventario->estado
                || !$tienePresentacionBaseActiva
            ) {
                throw ValidationException::withMessages([
                    'inventario_id' => 'Selecciona un producto activo con presentación base.',
                ]);
            }

            if ((int) $inventario->cantidad === (int) $datos['cantidad']) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad indicada coincide con las existencias actuales.',
                ]);
            }

            $inventario->cantidad = (int) $datos['cantidad'];
            DB::statement('SET @usuario_id = ?, @motivo = ?', [
                Auth::id(),
                'Ajuste de inventario: ' . $datos['motivo'],
            ]);

            try {
                $inventario->save();
            } finally {
                DB::statement('SET @usuario_id = NULL, @motivo = NULL');
            }
        });

        return redirect()
            ->route('articulos.index')
            ->with('success', 'El inventario se ajustó correctamente.');
    }

    public function movimientos(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $movimientos = Movimiento::query()
            ->with([
                'usuario:id,nombres,apellidos',
                'inventario.presentaciones' => function ($query) {
                    $query->select('id', 'inventario_id', 'nombre', 'codigoSKU')
                        ->where('base', true);
                },
            ])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->whereHas('inventario.presentaciones', function ($subquery) use ($buscar) {
                    $subquery->where('base', true)
                        ->where('codigoSKU', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('articulos.movimientos', compact('movimientos', 'buscar'));
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $articulos = Inventario::with([
            'marca',
            'presentaciones' => function ($query) {
                $query->select(
                    'id',
                    'inventario_id',
                    'codigoSKU',
                    'codigoBarras',
                    'nombre',
                    'precioUnitario',
                    'imagen_id'
                )->where('base', true);
            },
        ])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->whereHas('presentaciones', function ($subquery) use ($buscar) {
                        $subquery->where('base', true)
                            ->where(function ($subquery) use ($buscar) {
                                $subquery->where('nombre', 'like', "%{$buscar}%")
                                    ->orWhere('codigoSKU', 'like', "%{$buscar}%")
                                    ->orWhere('codigoBarras', 'like', "%{$buscar}%");
                            });
                    })

                        ->orWhereHas('marca', function ($subquery) use ($buscar) {
                            $subquery->where('nombre', 'like', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('articulos.index', compact('articulos', 'buscar'));
    }

    public function exportarCsv()
    {
        return response()->streamDownload(function () {
            $archivo = fopen('php://output', 'w');
            if ($archivo === false) {
                throw new \RuntimeException('No se pudo iniciar la exportación del inventario.');
            }

            try {
                fwrite($archivo, "\xEF\xBB\xBF");
                fputcsv($archivo, [
                    'SKU',
                    'Tipo',
                    'Nombre',
                    'Marca',
                    'ID interno',
                    'Categoría',
                    'Presentación base',
                    'Unidad base',
                    'Ubicación',
                    'Existencias actuales',
                    'Costo unitario',
                    'Precio unitario',
                    'Precio unitario por docena',
                    'Precio unitario por ciento',
                    'Stock mínimo',
                    'Estado artículo',
                    'Estado presentación base',
                ], ',', '"', '');

                Presentacion::query()
                    ->select([
                        'id',
                        'inventario_id',
                        'codigoSKU',
                        'nombre',
                        'ubicacion',
                        'precioUnitario',
                        'precioUnitarioDocena',
                        'precioUnitarioCiento',
                        'estado',
                    ])
                    ->where('base', true)
                    ->with([
                        'inventario:id,tipo,costoPromedioUnitario,cantidad,umbral,marca_id,categoria_id,estado',
                        'inventario.categoria:id,nombre',
                        'inventario.marca:id,nombre',
                    ])
                    ->orderBy('id')
                    ->chunkById(500, function ($presentaciones) use ($archivo) {
                        foreach ($presentaciones as $presentacion) {
                            $inventario = $presentacion->inventario;
                            $precios = [
                                $presentacion->precioUnitario,
                                $presentacion->precioUnitarioDocena,
                                $presentacion->precioUnitarioCiento,
                            ];
                            $precios = array_map(
                                fn ($precio) => $precio === null
                                    ? ''
                                    : number_format((float) $precio, 2, '.', ''),
                                $precios
                            );

                            fputcsv($archivo, [
                                $presentacion->codigoSKU,
                                $inventario->tipo === 'PRODUCTO' ? 'Producto' : 'Servicio',
                                $presentacion->nombre,
                                $inventario->marca?->nombre ?? '',
                                $inventario->id,
                                $inventario->categoria?->nombre ?? '',
                                $presentacion->nombre,
                                'unidad',
                                $presentacion->ubicacion ?? '',
                                (int) ($inventario->cantidad ?? 0),
                                number_format((float) ($inventario->costoPromedioUnitario ?? 0), 2, '.', ''),
                                ...$precios,
                                (int) ($inventario->umbral ?? 0),
                                $inventario->estado ? 'Activo' : 'Inactivo',
                                $presentacion->estado ? 'Activo' : 'Inactivo',
                            ], ',', '"', '');
                        }
                    });
            } finally {
                fclose($archivo);
            }
        }, 'inventario_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importarCsv(Request $request, ImportarInventarioInicialCsv $importador)
    {
        // Valida el archivo recibido y delega al servicio la validación del contenido y la carga atómica.
        $datos = $request->validate([
            'archivo' => ['required', 'file', 'max:10240'],
        ]);

        $cantidad = $importador->importar($datos['archivo'], Auth::id());

        // Confirma al usuario cuántos artículos base se crearon tras completar toda la importación.
        return redirect()
            ->route('articulos.index')
            ->with('success', "Se importaron {$cantidad} artículos base correctamente.");
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        $imagenSeleccionada = old('imagen_id')
            ? Imagen::find(old('imagen_id'))
            : null;

        return view('articulos.create', compact(
            'categorias',
            'marcas',
            'imagenSeleccionada'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $datos = $request->validate([
            'tipo' => [
                'required',
                Rule::in(['PRODUCTO', 'SERVICIO']),
            ],
            'categoria_id' => [
                'nullable',
                'exists:categorias,id',
            ],
            'marca_id' => [
                'nullable',
                'exists:marcas,id',
            ],
            'umbral' => [
                'required',
                'integer',
                'min:0',
            ],

            // Datos de la presentación base
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
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'detalle' => [
                'nullable',
                'string',
            ],
            'ubicacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'precioUnitario' => [
                Rule::requiredIf($request->input('tipo') === 'SERVICIO'),
                'nullable',
                'numeric',
                'min:0',
            ],
            'precioUnitarioDocena' => ['nullable', 'numeric', 'min:0'],
            'precioUnitarioCiento' => ['nullable', 'numeric', 'min:0'],
            'fecha_vencimiento' => [
                'nullable',
                'date',
            ],
            'imagen_id' => [
                'nullable',
                'exists:imagenes,id',
            ],
        ]);

        DB::transaction(function () use ($datos) {
            $inventario = Inventario::create([
                'tipo' => $datos['tipo'],
                'categoria_id' => $datos['categoria_id'] ?? null,
                'marca_id' => $datos['marca_id'] ?? null,
                'umbral' => $datos['umbral'],
                'cantidad' => 0,
                'costoPromedioUnitario' => 0,
                'estado' => true,
            ]);

            $inventario->presentaciones()->create([
                'codigoSKU' => $datos['codigoSKU'],
                'codigoBarras' => $datos['codigoBarras'] ?? null,
                'nombre' => $datos['nombre'],
                'detalle' => $datos['detalle'] ?? null,
                'factor' => 1,
                'ubicacion' => $datos['ubicacion'] ?? null,
                'base' => true,
                'precioUnitario' => $datos['precioUnitario'] ?? 0,
                'imagen_id' => $datos['imagen_id'] ?? null,
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'estado' => true,
            ]);
        });

        return redirect()
            ->route('articulos.index')
            ->with('success', 'Artículo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventario $inventario)
    {
        $inventario->load([
            'categoria',
            'marca',
            'presentaciones' => function ($query) {
                $query->where('base', true)
                    ->with('imagen');
            },
        ]);
        $presentacion = $inventario->presentaciones->first();
        $historialPrecios = $presentacion
            ? $presentacion->historialPrecios()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(10)
            : null;

        return view('articulos.show', [
            'articulo' => $inventario,
            'historialPrecios' => $historialPrecios,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventario $inventario)
    {
        $inventario->load([
            'categoria',
            'marca',
            'presentaciones.imagen',
        ]);

        $presentacion = $inventario->presentaciones->firstWhere('base', true)
            ?? $inventario->presentaciones->first();
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        $imagenId = old('imagen_id', $presentacion?->imagen_id);
        $imagenSeleccionada = (string) $imagenId === (string) $presentacion?->imagen_id
            ? $presentacion?->imagen
            : ($imagenId ? Imagen::find($imagenId) : null);
        $presentacion?->setRelation('imagen', $imagenSeleccionada);

        return view('articulos.edit', [
            'articulo' => $inventario,
            'categorias' => $categorias,
            'marcas' => $marcas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventario $inventario)
    {
        // Obtener la presentación base del artículo
        $presentacion = $inventario->presentaciones()
            ->where('base', true)
            ->firstOrFail();

        $datos = $request->validate([
            // El tipo no se modifica desde la edición
            'categoria_id' => [
                'nullable',
                'exists:categorias,id',
            ],
            'marca_id' => [
                'nullable',
                'exists:marcas,id',
            ],
            'umbral' => [
                'required',
                'integer',
                'min:0',
            ],
            'estado' => [
                'required',
                'boolean',
            ],

            // Datos de la presentación base
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
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'detalle' => [
                'nullable',
                'string',
            ],
            'ubicacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'precioUnitario' => [
                Rule::requiredIf($inventario->tipo === 'SERVICIO'),
                'nullable',
                'numeric',
                'min:0',
            ],
            'precioUnitarioDocena' => [
                Rule::requiredIf($inventario->tipo === 'PRODUCTO'),
                'nullable',
                'numeric',
                'min:0',
            ],
            'precioUnitarioCiento' => [
                Rule::requiredIf($inventario->tipo === 'PRODUCTO'),
                'nullable',
                'numeric',
                'min:0',
            ],
            'fecha_vencimiento' => [
                'nullable',
                'date',
            ],
            'imagen_id' => [
                'nullable',
                'exists:imagenes,id',
            ],
        ]);

        DB::transaction(function () use ($datos, $inventario, $presentacion) {

            // Update info general
            $inventario->update([
                'categoria_id' => $datos['categoria_id'] ?? null,
                'marca_id' => $datos['marca_id'] ?? null,
                'umbral' => $datos['umbral'],
                'estado' => $datos['estado'],
            ]);

            // Sincronizar el estado de todas las presentaciones
            $inventario->presentaciones()->update([
                'estado' => $datos['estado'],
            ]);

            // Info base de producto/servicio
            $datosPresentacion = [
                'codigoSKU' => $datos['codigoSKU'],
                'codigoBarras' => $datos['codigoBarras'] ?? null,
                'nombre' => $datos['nombre'],
                'detalle' => $datos['detalle'] ?? null,
                'ubicacion' => $datos['ubicacion'] ?? null,
                'imagen_id' => $datos['imagen_id'] ?? null,
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            ];

            if ($inventario->tipo === 'SERVICIO') {
                // Los servicios solo precio unitario
                $datosPresentacion['precioUnitario'] = $datos['precioUnitario'];
                // No se utilizan precios escalonados en servicios
                $datosPresentacion['precioUnitarioDocena'] = null;
                $datosPresentacion['precioUnitarioCiento'] = null;
            } else {
                // Los productos permiten editar los precios de venta
                $datosPresentacion['precioUnitario'] = $datos['precioUnitario']
                    ?? $presentacion->precioUnitario;

                $datosPresentacion['precioUnitarioDocena'] =
                    $datos['precioUnitarioDocena'];

                $datosPresentacion['precioUnitarioCiento'] =
                    $datos['precioUnitarioCiento'];
            }

            $presentacion->update($datosPresentacion);
        });

        return redirect()
            ->route('articulos.show', $inventario)
            ->with('success', 'Artículo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventario $inventario)
    {
        // Verificar si alguna presentación fue utilizada en ventas.
        $tieneVentas = $inventario->presentaciones()
            ->whereHas('ventaDetalles')
            ->exists();

        if ($tieneVentas) {
            return redirect()
                ->route('articulos.index')
                ->with(
                    'error',
                    'No se puede eliminar el artículo porque alguna de sus presentaciones ha sido utilizada en ventas.'
                );
        }

        DB::transaction(function () use ($inventario) {
            // Eliminar las presentaciones asociadas.
            $inventario->presentaciones()->delete();

            // Eliminar el artículo.
            $inventario->delete();
        });

        return redirect()
            ->route('articulos.index')
            ->with('success', 'Artículo eliminado correctamente.');
    }
}
