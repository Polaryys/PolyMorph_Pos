<x-app-layout>
    <!-- Encabezado y buscador -->
    <div class="mb-5">

        <x-search-bar :action="route('compras.index')" :buscar="$buscar" placeholder="Buscar por número, proveedor o estado">

            <x-primary-link href="{{ route('compras.create') }}" wire:navigate class="sm:ml-auto">
                + Nueva compra
            </x-primary-link>

        </x-search-bar>

    </div>

    <!-- MENSAJES -->
    @include('components.alert-message')

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- TABLA DE COMPRAS -->
    <x-table :paginator="$compras">
        <x-slot:headers>
            <th class="px-4 py-3 w-24 text-left whitespace-nowrap">No. de compra</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Fecha</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Proveedor</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad</th>
            <th class="px-4 py-3 text-right whitespace-nowrap">Costo total</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Estado</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
        </x-slot:headers>

        @forelse ($compras as $compra)
            <tr>
                <td class="px-4 py-3 whitespace-nowrap font-semibold ">
                    <a href="{{ route('compras.show', $compra) }}" class="transition hover:text-app-primary-600">
                        {{ $compra->numero_compra }}
                    </a>
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap">
                    {{ $compra->fecha_compra?->format('d/m/Y') }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                    {{ $compra->proveedor?->nombre ?? 'Sin proveedor' }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap font-medium">
                    {{ number_format((int) $compra->cantidad_total) }}
                </td>

                <td class="px-4 py-3 text-right whitespace-nowrap font-medium">
                    Q {{ number_format((float) $compra->costo_total, 2) }}
                </td>


                <td class="px-4 py-3 text-center whitespace-nowrap">
                    @if ($compra->estado === 'PENDIENTE')
                        <span
                            class="inline-flex rounded-full bg-amber-500/10 px-2.5 py-1 font-normal text-amber-600">
                            Pendiente
                        </span>
                    @elseif ($compra->estado === 'REGISTRADA')
                        <span
                            class="inline-flex rounded-full bg-emerald-500/10 px-2.5 py-1 font-normal text-emerald-600">
                            Registrada
                        </span>
                    @else
                        <span
                            class="inline-flex rounded-full bg-red-500/10 px-2.5 py-1 font-normal text-red-600">
                            Reversada
                        </span>
                    @endif
                </td>


                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('compras.show', $compra) }}" title="Ver compra" aria-label="Ver compra"
                            class="text-app-text-muted transition hover:text-app-primary-600">
                            <x-lucide-eye class="h-5 w-5" />
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                    No se encontraron compras.
                </td>
            </tr>
        @endforelse
    </x-table>

</x-app-layout>
