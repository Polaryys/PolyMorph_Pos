<x-app-layout>
    <div class="mb-4 flex items-center ">
        <a href="{{ route('imagenes.index') }}" wire:navigate
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
            Cancelar
        </a>
        <div>
            <p class="ml-2 mt-1 text-sm text-gray-500">
                Selecciona una o varias imágenes para agregarlas a tus medios.
            </p>
        </div>

        
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-lg border border-red-200">
            <p class="font-medium">No se pudieron cargar las imágenes:</p>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <form action="{{ route('imagenes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="archivos" class="block text-sm font-medium text-gray-700 mb-2">
                Seleccionar imágenes
            </label>

            <input type="file" id="archivos" name="archivos[]" accept="image/jpeg,image/png,image/webp,image/gif"
                multiple required
                class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg
                       file:mr-4 file:py-2.5 file:px-4 file:border-0
                       file:bg-indigo-50 file:text-indigo-700 file:font-medium
                       hover:file:bg-indigo-100">

            <p class="mt-2 text-xs text-gray-500">
                Formatos permitidos: JPG, JPEG, PNG y WEBP.
                Tamaño máximo: 2 MB por imagen.
            </p>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-medium rounded-md shadow-sm transition">
                    Cargar imágenes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
