<x-app-layout>
    <div class="space-y-8">
        <section class="space-y-3">
            <div class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:p-6">
                    <div class="min-w-0">
                        <p class="mb-1 text-xs font-medium text-slate-500">Numero de compra</p>
                        <h1 class="break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            {{ $compra->numero_compra }}
                        </h1>
                    </div>

                    <div class="grid shrink-0 grid-cols-2 gap-3 text-sm lg:min-w-[280px]">
                        <x-detail-field label="Creada en" class="bg-slate-50">
                            <span class="tabular-nums">{{ $compra->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        </x-detail-field>
                        <x-detail-field label="Última edición" class="bg-slate-50">
                            <span class="tabular-nums">{{ $compra->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        </x-detail-field>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mx-5 mt-5 rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700 sm:mx-6">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mx-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 sm:mx-6"
                        role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if ($compra->estado === 'PENDIENTE')
                    <div class="mx-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 sm:mx-6">
                        Esta compra aún no afecta inventario ni precios. El costo promedio se calculará al registrarla.
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-2 p-5 sm:grid-cols-2 xl:grid-cols-3 sm:p-6">
                    <x-detail-field label="Estado">
                        @if ($compra->estado === 'PENDIENTE')
                            <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                Pendiente
                            </span>
                        @elseif ($compra->estado === 'REGISTRADA')
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">
                                Registrada
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                {{ ucfirst(strtolower($compra->estado)) }}
                            </span>
                        @endif
                    </x-detail-field>
                    <x-detail-field label="Fecha de compra">
                        {{ $compra->fecha_compra?->format('d/m/Y') ?? '—' }}
                    </x-detail-field>
                    <x-detail-field label="Proveedor">
                        {{ $compra->proveedor?->nombre ?? 'Sin proveedor' }}
                    </x-detail-field>
                    <x-detail-field label="Número de factura">
                        {{ $compra->numero_factura ?: '—' }}
                    </x-detail-field>
                    <x-detail-field label="Cantidad total">
                        {{ number_format((int) $compra->cantidad_total) }}
                    </x-detail-field>
                    <x-detail-field label="Costo total">
                        <span class="text-base font-semibold text-slate-900">
                            Q {{ number_format((float) $compra->costo_total, 2) }}
                        </span>
                    </x-detail-field>
                    <x-detail-field label="Creada por">
                        {{ $compra->usuario?->nombres ?? '—' }}
                    </x-detail-field>
                    <x-detail-field label="Observaciones" class="xl:col-span-2">
                        {{ $compra->observaciones ?: 'Sin observaciones' }}
                    </x-detail-field>
                </div>

            </div>
        </section>

        <section>
            <x-section-title>Productos de la compra</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="whitespace-nowrap px-4 py-3">Producto</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center">Cantidad</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Costo unitario</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Costo promedio anterior</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Costo promedio nuevo</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio unitario nuevo</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio docena</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Precio ciento</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Subtotal</th>
                </x-slot:headers>

                @forelse ($compra->detalles as $detalle)
                    <tr class="transition-colors hover:bg-indigo-50/50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-900">
                                {{ $detalle->presentacion?->nombre ?? 'Producto no disponible' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                SKU: {{ $detalle->presentacion?->codigoSKU ?? '—' }}
                                <span class="mx-1">·</span>
                                Marca: {{ $detalle->presentacion?->inventario?->marca?->nombre ?? 'Sin marca' }}
                            </p>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ number_format((int) $detalle->cantidad) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            Q {{ number_format((float) $detalle->costo_unitario, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->costo_promedio_anterior !== null
                                ? 'Q ' . number_format((float) $detalle->costo_promedio_anterior, 2)
                                : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->costo_promedio_nuevo !== null
                                ? 'Q ' . number_format((float) $detalle->costo_promedio_nuevo, 2)
                                : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->precio_unitario_nuevo !== null
                                ? 'Q ' . number_format((float) $detalle->precio_unitario_nuevo, 2)
                                : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->precio_docena_nuevo !== null
                                ? 'Q ' . number_format((float) $detalle->precio_docena_nuevo, 2)
                                : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $detalle->precio_ciento_nuevo !== null
                                ? 'Q ' . number_format((float) $detalle->precio_ciento_nuevo, 2)
                                : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">
                            Q {{ number_format((float) $detalle->subtotal, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                            Esta compra no tiene productos registrados.
                        </td>
                    </tr>
                @endforelse

                @if ($compra->detalles->isNotEmpty())
                    <tr class="bg-slate-50">
                        <th colspan="8" class="px-4 py-3 text-right text-sm font-semibold text-slate-700">
                            Total de la compra
                        </th>
                        <th class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-slate-900">
                            Q {{ number_format((float) $compra->costo_total, 2) }}
                        </th>
                    </tr>
                @endif
            </x-table>
            
                @if ($compra->estado === 'PENDIENTE')
                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:px-6">
                        <x-secondary-link href="{{ route('compras.index') }}">Volver</x-secondary-link>
                        <x-secondary-link href="{{ route('compras.edit', $compra) }}">
                            Editar pendiente
                        </x-secondary-link>
                        <form action="{{ route('compras.destroy', $compra) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar esta compra pendiente?');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">Eliminar pendiente</x-danger-button>
                        </form>
                        <form action="{{ route('compras.registrar', $compra) }}" method="POST"
                            onsubmit="return confirm('¿Registrar esta compra? Se actualizarán inventario y precios.');">
                            @csrf
                            <x-primary-button>Registrar compra</x-primary-button>
                        </form>
                    </div>
                @elseif ($compra->estado === 'REGISTRADA')
                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:px-6">
                        <x-secondary-link href="{{ route('compras.index') }}">Volver</x-secondary-link>
                        <form action="{{ route('compras.revertir', $compra) }}" method="POST"
                            onsubmit="return confirm('¿Revertir esta compra? Esta acción no se puede deshacer.');">
                            @csrf
                            <x-danger-button type="submit">Revertir compra</x-danger-button>
                        </form>
                    </div>
                @else
                    <div class="flex justify-end border-t border-slate-200 px-5 py-4 sm:px-6">
                        <x-secondary-link href="{{ route('compras.index') }}">Volver</x-secondary-link>
                    </div>
                @endif
        </section>
    </div>
</x-app-layout>
