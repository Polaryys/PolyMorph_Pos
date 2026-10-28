<x-app-layout>
    @php
        $compraPendiente = count(session('compras.borrador.detalles', [])) > 0;
    @endphp

    <!-- Encabezado y buscador -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form action="{{ route('compras.index') }}" method="GET"
            class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input
                type="search"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar por número, proveedor o estado"
                class="w-full rounded-sm border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:w-96"
            >

            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Buscar
                </button>

                @if ($buscar !== '')
                    <a href="{{ route('compras.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
        <a href="{{ route('compras.create') }}"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            {{ $compraPendiente ? 'Continuar ingreso' : '+ Nuevo ingreso' }}
        </a>
        
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div id="success-message"
            class="mb-4 rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                document.getElementById('success-message')?.remove();
            }, 3000);
        </script>
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
                    {{ $compra->numero_compra }}
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
                    @if ($compra->estado === 'REGISTRADA')
                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                            Registrada
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                            Reversada
                        </span>
                    @endif
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('compras.show', $compra) }}" title="Ver compra"
                            aria-label="Ver compra" class="text-slate-500 transition hover:text-indigo-600">
                            <x-lucide-eye class="h-5 w-5" />
                        </a>

                        <button type="button" disabled title="La edición estará disponible próximamente"
                            aria-label="Editar compra"
                            class="cursor-not-allowed text-slate-300">
                            <x-lucide-pencil class="h-5 w-5" />
                        </button>

                        <button type="button" disabled title="La reversión estará disponible próximamente"
                            aria-label="Revertir compra"
                            class="cursor-not-allowed text-slate-300">
                            <span class="text-xs font-medium">Revertir</span>
                        </button>
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
