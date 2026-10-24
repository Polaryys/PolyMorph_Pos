<x-app-layout>

    <!-- Encabezado y acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('articulos.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Agregar Artículo
        </a>
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div id="success-message" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                document.getElementById('success-message')?.remove();
            }, 3000);
        </script>
    @endif

    <!-- TABLA DE ARTÍCULOS -->
    <x-table :paginator="$articulos">

        <x-slot:headers>
            <th class="text-center whitespace-nowrap">Imagen</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">SKU</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">BARCODE</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Nombre</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Marca</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Precio U.</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
        </x-slot:headers>

        @forelse ($articulos as $articulo)
            <tr class="hover:bg-indigo-50/50 transition-colors">
                <!-- IMAGEN -->
                <td class="px-2 py-2 text-center">
                    @if ($articulo->presentaciones->first()?->imagen)
                        <img src="{{ asset('storage/imagenes/' . $articulo->presentaciones->first()->imagen->nombre_archivo) }}"
                            alt="{{ $articulo->presentaciones->first()->nombre }}"
                            class="w-16 h-16 object-cover rounded border border-slate-200 mx-auto">
                    @else
                        <div
                            class="w-16 h-16 mx-auto flex items-center justify-center bg-slate-100 rounded text-slate-400">
                            <x-lucide-image class="w-5 h-5" />
                        </div>
                    @endif
                </td>

                <!-- SKU -->
                <td class="px-4 py-3 font-medium text-indigo-700 whitespace-nowrap">
                    {{ $articulo->presentaciones->first()?->codigoSKU }}
                </td>

                <!-- CÓDIGO DE BARRAS -->
                <td class="font-medium text-slate-500 whitespace-nowrap">
                    {{ $articulo->presentaciones->first()?->codigoBarras }}
                </td>

                <!-- NOMBRE -->
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                    {{ $articulo->presentaciones->first()?->nombre }}
                </td>

                <!-- MARCA -->
                <td class="px-4 py-3 text-center font-medium text-slate-700 whitespace-nowrap">
                    {{ $articulo->marca?->nombre }}
                </td>

                <!-- PRECIO DE VENTA -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                    Q {{ number_format((float) ($articulo->presentaciones->first()?->precioUnitario ?? 0), 2) }}
                </td>

                <!-- CANTIDAD -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                    {{ $articulo->cantidad }}
                </td>

                <!-- ACCIONES -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-3">

                        <!-- Ver -->
                        <a href="{{ route('articulos.show', $articulo) }}" wire:navigate title="Ver artículo"
                            aria-label="Ver artículo" class="text-slate-500 hover:text-indigo-600 transition">
                            <x-lucide-eye class="w-5 h-5" />
                        </a>

                        <!-- Editar -->
                        <a href="{{ route('articulos.edit', $articulo) }}" wire:navigate title="Editar artículo"
                            aria-label="Editar artículo" class="text-slate-500 hover:text-green-600 transition">
                            <x-lucide-pencil class="w-5 h-5" />
                        </a>

                        <!-- Eliminar -->
                        <form action="{{ route('articulos.destroy', $articulo) }}" method="POST"
                            onsubmit="return confirm('¿Deseas eliminar este artículo?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit" title="Eliminar artículo" aria-label="Eliminar artículo"
                                class="text-slate-500 hover:text-red-600 transition">
                                <x-lucide-trash-2 class="w-5 h-5" />
                            </button>
                        </form>

                    </div>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                    No se encontraron artículos.
                </td>
            </tr>
        @endforelse

    </x-table>

</x-app-layout>
