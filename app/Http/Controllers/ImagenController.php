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


    public function selector(Request $request)
    {
        $buscar = trim($request->query('buscar', ''));

        $imagenes = Imagen::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('nombre', 'like', '%' . $buscar . '%');
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'data' => $imagenes->getCollection()->map(fn($imagen) => [
                'id' => $imagen->id,
                'nombre' => $imagen->nombre,
                'url' => asset('storage/imagenes/' . $imagen->nombre_archivo),
            ]),
            'current_page' => $imagenes->currentPage(),
            'last_page' => $imagenes->lastPage(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = trim($request->query('buscar', ''));

        $imagenes = Imagen::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('nombre', 'like', '%' . $buscar . '%');
            })
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

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
                'regex:/^[\pL\s\pN]+$/u',
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
