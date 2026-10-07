<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
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
                        Cliente::where('nit', $value)->exists()
                    ) {
                        $fail('El NIT ya está registrado.');
                    }
                },
            ],

            'clasificacion' => [
                'required',
                'in:Mayorista,Minorista',
            ],

            'nombres' => [
                'required',
                'string',
                'max:100',
            ],

            'apellidos' => [
                'nullable',
                'string',
                'max:100',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:15',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:100',
            ],

            'notas' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        if (strtoupper($validated['nit']) === 'CF') {
            $validated['nit'] = 'CF';
        }

        Cliente::create($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nit' => [
                'required',
                'string',
                'max:15',
                function ($attribute, $value, $fail) use ($cliente) {

                    if (strtoupper($value) === 'CF') {
                        return;
                    }

                    $existe = Cliente::where('nit', $value)
                        ->where('id', '!=', $cliente->id)
                        ->exists();

                    if ($existe) {
                        $fail('El NIT ya está registrado.');
                    }
                },
            ],

            'clasificacion' => [
                'required',
                'in:Mayorista,Minorista',
            ],

            'nombres' => [
                'required',
                'string',
                'max:100',
            ],

            'apellidos' => [
                'nullable',
                'string',
                'max:100',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:15',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:100',
            ],

            'notas' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        if (strtoupper($validated['nit']) === 'CF') {
            $validated['nit'] = 'CF';
        }

        $cliente->update($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}
