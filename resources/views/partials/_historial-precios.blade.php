<section>
    <x-section-title>Historial de precios</x-section-title>
    <x-table :paginator="$historialPrecios">
        <x-slot:headers>
            <th class="whitespace-nowrap px-4 py-3">Fecha</th>
            <th class="whitespace-nowrap px-4 py-3">Unitario anterior</th>
            <th class="whitespace-nowrap px-4 py-3">Unitario nuevo</th>
            <th class="whitespace-nowrap px-4 py-3">Docena anterior</th>
            <th class="whitespace-nowrap px-4 py-3">Docena nueva</th>
            <th class="whitespace-nowrap px-4 py-3">Ciento anterior</th>
            <th class="whitespace-nowrap px-4 py-3">Ciento nuevo</th>
        </x-slot:headers>

        @forelse ($historialPrecios as $historialPrecio)
            <tr>
                <td class="whitespace-nowrap px-4 py-3 tabular-nums text-slate-700">
                    {{ $historialPrecio->created_at?->format('d/m/Y H:i') ?? '—' }}
                </td>
                @foreach ([
                    $historialPrecio->precio_unitario_anterior,
                    $historialPrecio->precio_unitario_nuevo,
                    $historialPrecio->precio_docena_anterior,
                    $historialPrecio->precio_docena_nuevo,
                    $historialPrecio->precio_ciento_anterior,
                    $historialPrecio->precio_ciento_nuevo,
                ] as $precio)
                    <td class="whitespace-nowrap px-4 py-3 tabular-nums text-slate-700">
                        @if ($precio !== null)
                            Q {{ number_format((float) $precio, 2) }}
                        @else
                            <span class="text-slate-400">No definido</span>
                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">
                    No hay cambios de precio registrados.
                </td>
            </tr>
        @endforelse
    </x-table>
</section>
