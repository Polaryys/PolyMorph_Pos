<x-app-layout>

    <!-- Encabezado y acciones -->
    <div class="mb-5">

        <x-search-bar :action="route('presentaciones.index')" :buscar="$buscar"
            placeholder="Buscar presentaciones por nombre, código o artículo">

            <x-primary-link href="{{ route('presentaciones.create') }}" wire:navigate>
                + Agregar presentación
            </x-primary-link>

        </x-search-bar>

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

    <!-- TABLA DE PRESENTACIONES -->
    <x-table :paginator="$presentaciones">

        <x-slot:headers>
            <th class="text-center whitespace-nowrap">Imagen</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">SKU</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">BARCODE</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Presentación</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Factor</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Precio U.</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Disponibilidad</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
        </x-slot:headers>

        @forelse ($presentaciones as $presentacion)
            <tr class="hover:bg-indigo-50/50 transition-colors">
                <!-- IMAGEN -->
                <td class="px-2 py-2 text-center">
                    @if ($presentacion->imagen)
                        <img src="{{ asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo) }}"
                            alt="{{ $presentacion->nombre }}"
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
                    {{ $presentacion->codigoSKU }}
                </td>

                <!-- CÓDIGO DE BARRAS -->
                <td class="text-slate-500 text-sm whitespace-nowrap">
                    {{ $presentacion->codigoBarras }}
                </td>

                <!-- NOMBRE DE LA PRESENTACIÓN -->
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">
                    {{ $presentacion->nombre }}
                </td>

                <!-- FACTOR -->
                <td class="px-4 py-3 text-center font-medium text-slate-700 whitespace-nowrap">
                    {{ $presentacion->factor }}
                </td>

                <!-- PRECIO DE VENTA -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                    Q {{ number_format((float) ($presentacion->precioUnitario ?? 0), 2) }}
                </td>

                <!-- DISPONIBILIDAD EN PRESENTACIONES -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                    {{ intdiv(
                        max(0, (int) ($presentacion->inventario?->cantidad ?? 0)),
                        max(1, (int) ($presentacion->factor ?? 1)),
                    ) }}
                </td>

                <!-- ACCIONES -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-3">

                        <!-- Ver -->
                        <x-action-icon element="a" href="{{ route('presentaciones.show', $presentacion) }}"
                            wire:navigate title="Ver presentación">
                            <x-lucide-eye class="w-5 h-5" />
                        </x-action-icon>

                        <!-- Editar -->
                        <x-action-icon element="a" href="{{ route('presentaciones.edit', $presentacion) }}"
                            wire:navigate title="Editar presentación" color="green">
                            <x-lucide-pencil class="w-5 h-5" />
                        </x-action-icon>

                        <!-- Desactivar -->
                        <form action="{{ route('presentaciones.destroy', $presentacion) }}" method="POST"
                            onsubmit="return confirm('¿Deseas desactivar esta presentación?')">

                            @csrf
                            @method('DELETE')

                            <x-action-icon type="submit" title="Desactivar presentación" color="red">
                                <x-lucide-trash-2 class="w-5 h-5" />
                            </x-action-icon>
                        </form>

                    </div>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                    No se encontraron presentaciones.
                </td>
            </tr>
        @endforelse

    </x-table>

</x-app-layout>
