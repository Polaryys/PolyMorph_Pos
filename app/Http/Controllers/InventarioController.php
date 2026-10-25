<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Inventario;
use App\Models\Presentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            ->latest()
            ->paginate(15);

        return view('articulos.index', compact('articulos'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

        return view('articulos.create', compact(
            'categorias',
            'marcas'
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

        return view('articulos.show', [
            'articulo' => $inventario,
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

        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

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
