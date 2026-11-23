<x-app-layout>
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
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
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    {{ $venta->created_at?->format('d/m/Y H:i') ?? '—' }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap font-semibold">
                    {{ $venta->numero_venta }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    {{ match ($venta->tipo_operacion) {
                        'VENTA' => 'Venta',
                        'DEVOLUCION' => 'Devolución',
                        'CAMBIO' => 'Cambio',
                        default => $venta->tipo_operacion,
                    } }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    {{ $venta->vendedor?->username ?? '—' }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    {{ $venta->cliente ? trim($venta->cliente->nombres . ' ' . $venta->cliente->apellidos) : '—' }}
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap font-medium">
                    Q {{ number_format((float) $venta->total, 2) }}
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-green-100/70 text-green-700 dark:bg-green-900/30 dark:text-green-400' =>
                            $venta->estado_venta === 'REGISTRADA',
                        'bg-red-100/70 text-red-700 dark:bg-red-900/30 dark:text-red-400' =>
                            $venta->estado_venta === 'ANULADA',
                    ])>
                        {{ ucfirst(strtolower($venta->estado_venta)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    @php($estadoPago = $venta->total < 0 && $venta->estado_pago === 'PAGADO' ? 'REEMBOLSADO' : $venta->estado_pago)
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-green-100/70 text-green-700 dark:bg-green-900/30 dark:text-green-400' =>
                            $estadoPago === 'PAGADO',
                        'bg-violet-100/70 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400' =>
                            $estadoPago === 'REEMBOLSADO',
                        'bg-amber-100/70 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' =>
                            $estadoPago === 'PARCIAL',
                        'bg-app-surface-alt text-app-text-secondary' => $estadoPago === 'PENDIENTE',
                        'bg-app-surface-alt/60 text-app-text-muted' => $estadoPago === null,
                    ])>
                        {{ $estadoPago ? ucfirst(strtolower($estadoPago)) : 'No aplica' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <a href="{{ route('ventas.show', $venta) }}" title="Ver venta" aria-label="Ver venta"
                        class="inline-flex text-app-text-muted transition hover:text-app-primary-600 dark:hover:text-app-primary-400">
                        <x-lucide-eye class="h-5 w-5" />
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-6 py-8 text-center">
                    No hay ventas registradas.
                </td>
            </tr>
        @endforelse
    </x-table>
</x-app-layout>
