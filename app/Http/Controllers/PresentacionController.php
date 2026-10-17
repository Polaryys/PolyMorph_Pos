<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Inventario;
use App\Models\Presentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PresentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articulos = Inventario::with([
            'categoria',
            'marca',
            'presentaciones',
        ])
            ->latest()
            ->paginate(10);

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
            // Datos generales del inventario
            'tipo' => ['required', Rule::in(['PRODUCTO', 'SERVICIO'])],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'marca_id' => ['nullable', 'exists:marcas,id'],
            'umbral' => ['required', 'integer', 'min:0'],

            // Presentación base
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
            'imagen_id' => ['nullable', 'exists:imagenes,id'],
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
                'base' => true,
                'imagen_id' => $datos['imagen_id'] ?? null,
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
    public function show(Presentacion $presentaciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presentacion $presentaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presentacion $presentaciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presentacion $presentaciones)
    {
        //
    }
}
