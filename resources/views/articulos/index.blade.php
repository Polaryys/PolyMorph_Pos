<x-app-layout>

    <!-- Encabezado y Acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('articulos.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Agregar Artículo
        </a>
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div id="success-message"
            class="mb-4 p-4 text-sm text-green-700 bg-green-100
                       rounded-lg border border-green-200">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                document.getElementById('success-message')?.remove();
            }, 3000);
        </script>
    @endif

    <!-- TABLA -->
    <div class="bg-white border rounded border-gray-300 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="min-w-[1100px] w-full table-auto text-left border-collapse">
                <thead
                    class="border-b border-gray-300 text-sm font-semibold
                               text-black  tracking-wider bg-gray-200">
                    <tr>
                        <th class="px-4 py-2.5 text-left whitespace-nowrap">SKU</th>
                        <th class="py-2.5 text-left whitespace-nowrap">Código de Barras</th>
                        <th class="px-2 py-2.5 text-left whitespace-nowrap">
                            Nombre
                        </th>
                        <th class=" py-2.5 text-center whitespace-nowrap">
                            Marca
                        </th>
                        <th class=" py-2.5 text-center whitespace-nowrap">
                            Precio Venta
                        </th>
                        <th class=" py-2.5 text-center whitespace-nowrap">
                            Cantidad
                        </th>
                        <th class="px-4 py-2.5 text-center whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse ($articulos as $articulo)
                        <tr class="hover:bg-gray-50 transition-colors">

                            <!-- SKU -->
                            <td class="px-4 py-3 font-medium text-blue-700 whitespace-nowrap">
                                {{ $articulo->presentaciones->first()?->codigoSKU }}
                            </td>

                            <!-- BARCODE -->
                            <td class=" py-3 font-medium text-gray-500 whitespace-nowrap">
                                {{ $articulo->presentaciones->first()?->codigoBarras }}
                            </td>

                            <!-- NAME -->
                            <td class="px-2 py-3 font-medium text-gray-900 whitespace-nowrap">
                                {{ $articulo->presentaciones->first()?->nombre }}
                            </td>

                            <!-- MARCA -->
                            <td class=" py-3 text-center font-medium text-gray-900 whitespace-nowrap">
                                {{ $articulo->marca?->nombre }}
                            </td>

                            <!-- PRECIO VENTA -->
                            <td class=" py-3 text-center font-medium text-gray-900 whitespace-nowrap">
                                Q
                                {{ number_format((float) ($articulo->presentaciones->first()?->precioUnitario ?? 0), 2) }}
                            </td>

                            <!-- CANTIDAD -->
                            <td class=" py-3 text-center font-medium text-gray-900 whitespace-nowrap ">
                                {{ $articulo->cantidad }}
                            </td>

                            <!-- ACCIONES -->

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3 ">


                                    <!-- Ver -->
                                    <a href="{{ route('articulos.show', $articulo) }}" wire:navigate
                                        title="Ver artículo" aria-label="Ver artículo"
                                        class="text-gray-500 hover:text-blue-600 transition">
                                        <x-lucide-eye class="w-5 h-5" />
                                    </a>

                                    <!-- Editar -->
                                    <a href="{{ route('articulos.edit', $articulo) }}" wire:navigate
                                        title="Editar artículo" aria-label="Editar artículo"
                                        class="text-gray-500 hover:text-green-600 transition">
                                        <x-lucide-pencil class="w-5 h-5" />
                                    </a>

                                    <!-- Eliminar -->
                                    <form action="{{ route('articulos.destroy', $articulo) }}" method="POST"
                                        onsubmit="return confirm('¿Deseas eliminar este artículo?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" title="Eliminar artículo" aria-label="Eliminar artículo"
                                            class="text-gray-500 hover:text-red-600 transition">
                                            <x-lucide-trash-2 class="w-5 h-5" />
                                        </button>
                                    </form>


                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                No se encontraron artículos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- PAGINACIÓN -->
                @if ($articulos->hasPages())
                    <div class="px-5 py-2 border-t border-gray-200">
                        {{ $articulos->links() }}
                    </div>
                @endif


        </div>
    </div>

</x-app-layout>
