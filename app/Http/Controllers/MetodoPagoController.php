<?php

namespace App\Http\Controllers;


use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $metodosPago = MetodoPago::orderBy('nombre')
            ->paginate(10);

        return view('metodos-pago.index', compact('metodosPago'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('metodos-pago.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                'unique:metodos_pago,nombre',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        MetodoPago::create($validated);

        return redirect()
            ->route('metodos-pago.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetodoPago $metodoPago)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetodoPago $metodoPago): View
    {
        return view('metodos-pago.edit', compact('metodoPago'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetodoPago $metodoPago): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('metodos_pago', 'nombre')
                    ->ignore($metodoPago->id),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $metodoPago->update($validated);

        return redirect()
            ->route('metodos-pago.index')
            ->with('success', 'Método de pago actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetodoPago $metodoPago)
    {
        $metodoPago->delete();
        return redirect()
            ->route('metodos-pago.index')
            ->with('success', 'Metodo eliminado exitosamente.');
    }
}
