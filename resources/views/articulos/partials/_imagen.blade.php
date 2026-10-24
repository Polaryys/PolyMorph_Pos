
<div class="flex justify-center md:justify-start">
    <div class="relative h-56 w-56 shrink-0">
        <img
            id="imagen-preview"
            src="{{ asset('images/default-avatar.png') }}"
            alt="Imagen del artículo"
            class="h-56 w-56 rounded border-2 border-indigo-100
                   bg-gray-50 object-cover shadow-sm">

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
    </div>

    <input
        type="hidden"
        id="imagen_id"
        name="imagen_id"
        value="{{ old('imagen_id') }}">
</div>

@error('imagen_id')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
@enderror
