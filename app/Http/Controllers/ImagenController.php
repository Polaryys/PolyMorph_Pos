<?php

namespace App\Http\Controllers;

use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\View\View;
use Intervention\Image\Format;

class ImagenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $imagenes = Imagen::query()
            ->orderByDesc('id')
            ->paginate(24);

        foreach ($imagenes as $imagen) {
            $ruta = 'imagenes/' . $imagen->nombre_archivo;

            if (Storage::disk('public')->exists($ruta)) {
                $imagen->peso_archivo = Storage::disk('public')->size($ruta);

                $datos = Image::decode(
                    Storage::disk('public')->path($ruta)
                );

                $imagen->ancho = $datos->width();
                $imagen->alto = $datos->height();
            }
        }

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
            'archivos' => ['required', 'array', 'min:1'],
            'archivos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:2048',
            ],
        ]);

        foreach ($validated['archivos'] as $archivo) {
            $nombre = Str::of(
                pathinfo(
                    $archivo->getClientOriginalName(),
                    PATHINFO_FILENAME
                )
            )
                ->squish()
                ->limit(50, '')
                ->toString();

            if ($nombre === '') {
                $nombre = 'Imagen';
            }

            $nombreArchivo = Str::uuid() . '.webp';

            $imagenWebp = Image::decode($archivo)
                ->scaleDown(width: 1024, height: 1024)
                ->encodeUsingFormat(Format::WEBP, quality: 80);

            Storage::disk('public')->put(
                'imagenes/' . $nombreArchivo,
                (string) $imagenWebp
            );

            try {
                Imagen::create([
                    'nombre' => $nombre,
                    'nombre_archivo' => $nombreArchivo,
                ]);
            } catch (\Throwable $e) {
                Storage::disk('public')->delete(
                    'imagenes/' . $nombreArchivo
                );

                throw $e;
            }
        }

        return redirect()
            ->route('imagenes.index')
            ->with('success', 'Imágenes cargadas correctamente.');
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
     *     public function edit(Imagen $imagen): View
     *{
     *   return view('imagenes.edit', compact('imagen'));
     *}
     */


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
        ]);

        $imagen->update([
            'nombre' => $validated['nombre'],
        ]);

        return redirect()
            ->route('imagenes.index')
            ->with('success', 'Nombre de la imagen actualizado correctamente.');
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
