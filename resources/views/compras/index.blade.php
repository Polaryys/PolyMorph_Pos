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
    @if (session('success'))
        <div id="success-message" class="mb-4 rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                document.getElementById('success-message')?.remove();
            }, 3000);
        </script>
    @endif

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
            <th class="px-4 py-3 text-left whitespace-nowrap">No. de compra</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Fecha</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Proveedor</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad</th>
            <th class="px-4 py-3 text-right whitespace-nowrap">Costo total</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Estado</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
        </x-slot:headers>

        @forelse ($compras as $compra)
            <tr class="transition-colors hover:bg-indigo-50/50">
                <td class="px-4 py-3 whitespace-nowrap font-semibold text-indigo-700">
                    <a href="{{ route('compras.show', $compra) }}" class="transition hover:text-indigo-900">
                    {{ $compra->numero_compra }}
                    </a>
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap text-slate-700">
                    {{ $compra->fecha_compra?->format('d/m/Y') }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ $compra->proveedor?->nombre ?? 'Sin proveedor' }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap font-medium text-slate-700">
                    {{ number_format((int) $compra->cantidad_total) }}
                </td>

                <td class="px-4 py-3 text-right whitespace-nowrap font-medium text-slate-900">
                    Q {{ number_format((float) $compra->costo_total, 2) }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap">
                    @if ($compra->estado === 'PENDIENTE')
                        <span
                            class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            Pendiente
                        </span>
                    @elseif ($compra->estado === 'REGISTRADA')
                        <span
                            class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                            Registrada
                        </span>
                    @else
                        <span
                            class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                            Reversada
                        </span>
                    @endif
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('compras.show', $compra) }}" title="Ver compra" aria-label="Ver compra"
                            class="text-slate-500 transition hover:text-indigo-600">
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
