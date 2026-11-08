<x-app-layout>
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-slate-800">Ventas</h1>
        <x-primary-link href="{{ route('ventas.create') }}" wire:navigate>
            + Nueva venta
        </x-primary-link>
    </div>

    <x-table :paginator="$ventas">
        <x-slot:headers>
            <th class="px-4 py-3 text-center whitespace-nowrap">Fecha y hora</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">No. de registro</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Tipo de operación</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Vendedor</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Cliente</th>
            <th class="px-4 py-3 text-right whitespace-nowrap">Total</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Estado</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Estado de pago</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Acción</th>
        </x-slot:headers>

        @forelse ($ventas as $venta)
            <tr>
                <td class="px-4 py-3 text-center whitespace-nowrap text-slate-700">
                    {{ $venta->created_at?->format('d/m/Y H:i') ?? '—' }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap font-semibold text-indigo-700">
                    {{ $venta->numero_venta }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ match ($venta->tipo_operacion) {
                        'VENTA' => 'Venta',
                        'DEVOLUCION' => 'Devolución',
                        'CAMBIO' => 'Cambio',
                        default => $venta->tipo_operacion,
                    } }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ $venta->vendedor?->username ?? '—' }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-900">
                    {{ $venta->cliente ? trim($venta->cliente->nombres.' '.$venta->cliente->apellidos) : '—' }}
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap font-medium text-slate-900">
                    Q {{ number_format((float) $venta->total, 2) }}
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-green-100 text-green-700' => $venta->estado_venta === 'REGISTRADA',
                        'bg-red-100 text-red-700' => $venta->estado_venta === 'ANULADA',
                    ])>
                        {{ ucfirst(strtolower($venta->estado_venta)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    @php($estadoPago = $venta->total < 0 && $venta->estado_pago === 'PAGADO'
                        ? 'REEMBOLSADO'
                        : $venta->estado_pago)
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-green-100 text-green-700' => $estadoPago === 'PAGADO',
                        'bg-violet-100 text-violet-700' => $estadoPago === 'REEMBOLSADO',
                        'bg-amber-100 text-amber-700' => $estadoPago === 'PARCIAL',
                        'bg-slate-100 text-slate-700' => $estadoPago === 'PENDIENTE',
                        'bg-slate-100 text-slate-500' => $estadoPago === null,
                    ])>
                        {{ $estadoPago ? ucfirst(strtolower($estadoPago)) : 'No aplica' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <a href="{{ route('ventas.show', $venta) }}" title="Ver venta" aria-label="Ver venta"
                        class="inline-flex text-slate-500 transition hover:text-indigo-600">
                        <x-lucide-eye class="h-5 w-5" />
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                    No hay ventas registradas.
                </td>
            </tr>
        @endforelse
    </x-table>
</x-app-layout>