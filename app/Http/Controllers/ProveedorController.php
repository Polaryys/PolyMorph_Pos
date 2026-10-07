<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedores = Proveedor::paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nit' => [
                'required',
                'string',
                'max:15',
                function ($attribute, $value, $fail) {
                    if (
                        strtoupper($value) !== 'CF' &&
                        Proveedor::where('nit', $value)->exists()
                    ) {
                        $fail('El NIT ya está registrado.');
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:20'],
            'nombre_contacto' => ['nullable', 'string', 'max:150'],
            'telefono_contacto' => ['required', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255', 'unique:proveedores,correo'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:255'],
        ]);

        if (strtoupper($validated['nit']) === 'CF') {
            $validated['nit'] = 'CF';
        }

        Proveedor::create($validated);

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nit' => [
                'required',
                'string',
                'max:15',
                function ($attribute, $value, $fail) use ($proveedor) {
                    if (strtoupper($value) === 'CF') {
                        return;
                    }

                    $existe = Proveedor::where('nit', $value)
                        ->where('id', '!=', $proveedor->id)
                        ->exists();

                    if ($existe) {
                        $fail('El NIT ya está registrado.');
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:20'],
            'nombre_contacto' => ['nullable', 'string', 'max:150'],
            'telefono_contacto' => ['required', 'string', 'max:20'],
            'correo' => [
                'nullable',
                'email',
                'max:255',
                'unique:proveedores,correo,' . $proveedor->id,
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:255'],
        ]);

        if (strtoupper($validated['nit']) === 'CF') {
            $validated['nit'] = 'CF';
        }

        $proveedor->update($validated);

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
