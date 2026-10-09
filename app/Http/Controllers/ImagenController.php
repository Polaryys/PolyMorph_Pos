<?php

namespace App\Http\Controllers;

use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\View\View;

class ImagenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $imagenes = Imagen::orderBy('id')
            ->paginate(10);
        return view('imagenes.index', compact('imagenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('imagenes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'archivo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:2048',
            ],
        ]);

        $archivo = $request->file('archivo');

        $imagenWebp = Image::read($archivo)
            ->toWebp(80);

        $nombreArchivo = str()->uuid() . '.webp';

        Storage::disk('public')->put(
            'imagenes/' . $nombreArchivo,
            (string) $imagenWebp
        );

        try {
            Imagen::create([
                'nombre' => $validated['nombre'],
                'nombre_archivo' => $nombreArchivo,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete('imagenes/' . $nombreArchivo);
            throw $e;
        }

        return redirect()
            ->route('imagenes.index')
            ->with('success', 'Imagen registrada correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Imagen $imagen): View
    {
        return view('imagenes.edit', compact('imagen'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Imagen $imagen): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\pL\s]+$/u',
            ],
            'archivo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $nombreAnterior = $imagen->nombre_archivo;
        $nombreNuevo = $nombreAnterior;

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');

            $imagenWebp = Image::read($archivo)->toWebp(80);

            $nombreNuevo = Str::uuid() . '.webp';

            Storage::disk('public')->put(
                'imagenes/' . $nombreNuevo,
                (string) $imagenWebp
            );
        }

        try {
            $imagen->update([
                'nombre' => $validated['nombre'],
                'nombre_archivo' => $nombreNuevo,
            ]);
        } catch (\Throwable $e) {

            if ($nombreNuevo !== $nombreAnterior) {
                Storage::disk('public')->delete(
                    'imagenes/' . $nombreNuevo
                );
            }

            throw $e;
        }

        if ($nombreNuevo !== $nombreAnterior) {
            Storage::disk('public')->delete(
                'imagenes/' . $nombreAnterior
            );
        }

        return redirect()
            ->route('imagenes.index')
            ->with('success', 'Imagen actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Imagen $imagen): RedirectResponse
    {
        $nombreArchivo = $imagen->nombre_archivo;

        $imagen->delete();

        Storage::disk('public')->delete(
            'imagenes/' . $nombreArchivo
        );

        return redirect()
            ->route('imagenes.index')
            ->with('success', 'Imagen eliminada correctamente.');
    }
}
