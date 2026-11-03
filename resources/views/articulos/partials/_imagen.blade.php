
<div class="flex justify-center md:justify-start">
    <div class="relative h-56 w-56 shrink-0">
        <img
            id="imagen-preview"
            src="{{ $imagenSeleccionada
                ? asset('storage/imagenes/' . $imagenSeleccionada->nombre_archivo)
                : '' }}"
            alt="Imagen del artículo"
            class="{{ $imagenSeleccionada ? '' : 'hidden' }} h-56 w-56 rounded border-2 border-indigo-100
                   bg-gray-50 object-cover shadow-sm">

        <div id="imagen-placeholder"
            class="{{ $imagenSeleccionada ? 'hidden' : 'flex' }} h-56 w-56 items-center justify-center rounded border-2 border-indigo-100 bg-slate-100 text-slate-400 shadow-sm">
            <x-lucide-image class="h-16 w-16" />
        </div>

        <button
            type="button"
            onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
            title="Seleccionar imagen"
            aria-label="Seleccionar imagen"
            class="absolute bottom-2 right-2 rounded-full bg-indigo-600
                   p-2.5 text-white shadow-md transition
                   hover:bg-indigo-700">

            <x-lucide-camera class="h-4 w-4" />
        </button>

        @if ($mostrarBotonQuitarImagen ?? true)
            <button
                type="button"
                id="imagen-quitar"
                onclick="quitarImagenArticulo()"
                title="Quitar imagen"
                aria-label="Quitar imagen"
                class="{{ $imagenSeleccionada ? 'flex' : 'hidden' }} absolute left-2 top-2 items-center justify-center rounded-full bg-transparent p-1.5 text-red-600 transition hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <x-lucide-x class="h-5 w-5" />
            </button>
        @endif
    </div>

    <input
        type="hidden"
        id="imagen_id"
        name="imagen_id"
        value="{{ $imagenSeleccionada?->id }}">
</div>

<x-input-error :messages="$errors->get('imagen_id')" />
