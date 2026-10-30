<x-app-layout>


    <div class="mb-5">
        <form action="{{ route('inventario.movimientos') }}" method="GET"
            class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input
                type="search"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar por SKU del producto"
                class="w-full rounded-sm border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:w-96"
            >

            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Buscar
                </button>

                @if ($buscar !== '')
                    <a href="{{ route('inventario.movimientos') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <x-table :paginator="$movimientos">
        <x-slot:headers>
            <th class="px-4 py-3 text-left whitespace-nowrap">ID</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Usuario</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Producto</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Tipo de movimiento</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad anterior</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad posterior</th>
            <th class="px-4 py-3 text-left">Motivo</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Fecha de creación</th>
        </x-slot:headers>

        @forelse ($movimientos as $movimiento)
            <tr class="transition-colors hover:bg-indigo-50/50">
                <td class="px-4 py-3 whitespace-nowrap font-semibold text-indigo-700">
                    {{ $movimiento->id }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ trim(($movimiento->usuario?->nombres ?? '')) ?: 'Usuario eliminado' }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ $movimiento->inventario?->presentaciones->first()?->nombre ?? '—' }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap">
                    {{ $movimiento->tipo_movimiento }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap tabular-nums">
                    {{ number_format($movimiento->cantidad_anterior) }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap tabular-nums">
                    {{ number_format($movimiento->cantidad) }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap tabular-nums">
                    {{ number_format($movimiento->cantidad_posterior) }}
                </td>

                <td class="px-4 py-3 min-w-56 text-slate-700">
                    {{ $movimiento->motivo ?: '—' }}
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap text-slate-700">
                    {{ $movimiento->created_at?->format('d/m/Y H:i:s') ?? '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                    No se encontraron movimientos de inventario.
                </td>
            </tr>
        @endforelse
    </x-table>
</x-app-layout>
