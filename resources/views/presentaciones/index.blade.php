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
    @include('components.alert-message')

    <!-- TABLA DE PRESENTACIONES -->
    <x-table :paginator="$presentaciones">

        <x-slot:headers>
            <th class="text-center w-16 whitespace-nowrap">Imagen</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">SKU</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Presentación</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Marca</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Factor</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Precio U.</th>
            <th class="px-4 w-16 py-3 text-center whitespace-nowrap">Disponibilidad</th>
            <th class="px-4 w-16 py-3 text-center whitespace-nowrap">Estado</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
        </x-slot:headers>

        @forelse ($presentaciones as $presentacion)
            <tr class="hover:bg-indigo-50/50 transition-colors">
                <!-- IMAGEN -->
                <td class="px-2 py-2 text-center">
                    @if ($presentacion->imagen)
                        <img src="{{ asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo) }}"
                            alt="{{ $presentacion->nombre }}"
                            class="w-16 h-16 object-cover rounded border borderapp-border mx-auto">
                    @else
                        <div
                            class="w-16 h-16 mx-auto flex items-center justify-center bg-app-background rounded text-app-text-muted">
                            <x-lucide-image class="w-5 h-5" />
                        </div>
                    @endif
                </td>

                <!-- SKU -->
                <td class="px-4 py-3 font-medium whitespace-nowrap">
                    {{ $presentacion->codigoSKU }}
                </td>

                <!-- NOMBRE DE LA PRESENTACIÓN -->
                <td class="relative px-4 py-3">
                    <div class="flex min-h-10 items-center">
                        <span class="absolute inset-y-0 left-4 flex items-center font-normal">
                            {{ $presentacion->nombre }}
                        </span>
                    </div>

                    <span class="block text-[10px] text-app-text-muted">
                        {{ $presentacion->codigoBarras }}
                    </span>
                </td>
                <!-- CÓDIGO DE BARRAS -->
                <td class="text-sm whitespace-nowrap">
                    {{ $presentacion->inventario?->marca?->nombre ?? 'Sin marca' }}
                </td>

                <!-- FACTOR -->
                <td class="px-4 py-3 text-sm text-center font-medium whitespace-nowrap">
                    {{ $presentacion->factor }}
                </td>

                <!-- PRECIO DE VENTA -->
                <td class="px-4 py-3 text-sm text-center font-medium whitespace-nowrap">
                    Q {{ number_format((float) ($presentacion->precioUnitario ?? 0), 2) }}
                </td>

                <!-- DISPONIBILIDAD EN PRESENTACIONES -->
                <td class="px-4 py-3 text-sm text-center font-medium whitespace-nowrap">
                    {{ intdiv(
                        max(0, (int) ($presentacion->inventario?->cantidad ?? 0)),
                        max(1, (int) ($presentacion->factor ?? 1)),
                    ) }}
                </td>

                <!-- ESTADO DE LA PRESENTACIÓN -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                        <span @class([
                        'inline-flex items-center rounded-full text-xs px-2.5 py-1 font-semibold whitespace-nowrap',
                        'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' =>
                            $presentacion->estado,
                        'bg-red-500/10 text-red-600 dark:text-red-400' => !$presentacion->estado,
                    ])>
                        {{ $presentacion->estado ? 'Activo' : 'Inactivo' }}
                    </span>
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
                <td colspan="9" class="px-6 py-8 text-center">
                    No se encontraron presentaciones.
                </td>
            </tr>
        @endforelse

    </x-table>

</x-app-layout>
