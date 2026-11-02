<x-app-layout>

    <div class="mb-5">
        <x-search-bar :action="route('inventario.movimientos')" :buscar="$buscar" placeholder="Buscar por SKU del producto" />
    </div>

    <x-table :paginator="$movimientos">
        <x-slot:headers>
            <th class="px-4 py-3 text-left whitespace-nowrap">ID</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Usuario</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Producto</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Tipo de movimiento</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Antes</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Diferencia</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Despues</th>
            <th class="px-4 py-3 text-left">Motivo</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Fecha de creación</th>
        </x-slot:headers>

        @forelse ($movimientos as $movimiento)
            <tr class="transition-colors hover:bg-indigo-50/50">
                <td class="px-4 py-3 whitespace-nowrap font-semibold text-indigo-700">
                    {{ $movimiento->id }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ trim($movimiento->usuario?->nombres ?? '') ?: 'Usuario eliminado' }}
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

                <td @class([
                    'px-4 py-3 text-center whitespace-nowrap tabular-nums font-semibold',
                    'text-blue-600' =>
                        $movimiento->cantidad_posterior > $movimiento->cantidad_anterior,
                    'text-red-600' =>
                        $movimiento->cantidad_posterior < $movimiento->cantidad_anterior,
                    'text-slate-600' =>
                        $movimiento->cantidad_posterior == $movimiento->cantidad_anterior,
                ])>
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
