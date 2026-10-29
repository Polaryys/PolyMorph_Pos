<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Detalle de compra</p>
                <h1 class="mt-1 text-2xl font-semibold text-slate-900">
                    {{ $compra->numero_compra }}
                </h1>
            </div>
            <a href="{{ route('compras.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Volver a compras
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @if ($compra->estado === 'PENDIENTE')
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Esta compra aún no afecta inventario ni precios. El costo promedio se calculará al registrarla.
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                    Información general
                </h2>
            </div>

            <dl class="grid grid-cols-1 gap-x-6 gap-y-5 p-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-slate-500">Número de compra</dt>
                    <dd class="mt-1 font-semibold text-indigo-700">{{ $compra->numero_compra }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Fecha de compra</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ $compra->fecha_compra?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Estado</dt>
                    <dd class="mt-1">
                        @if ($compra->estado === 'PENDIENTE')
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                Pendiente
                            </span>
                        @elseif ($compra->estado === 'REGISTRADA')
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                Registrada
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                {{ ucfirst(strtolower($compra->estado)) }}
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Proveedor</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ $compra->proveedor?->nombre ?? 'Sin proveedor' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Número de factura</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ $compra->numero_factura ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Creada por</dt>
                    <dd class="mt-1 text-sm text-slate-800">
                        {{ $compra->usuario
                            ? trim($compra->usuario->nombres . ' ' . $compra->usuario->apellidos)
                            : '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Cantidad total</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">
                        {{ number_format((int) $compra->cantidad_total) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Costo total</dt>
                    <dd class="mt-1 text-lg font-semibold text-slate-900">
                        Q {{ number_format((float) $compra->costo_total, 2) }}
                    </dd>
                </div>
                <div class="sm:col-span-2 lg:col-span-4">
                    <dt class="text-sm font-medium text-slate-500">Observaciones</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm text-slate-800">
                        {{ $compra->observaciones ?: 'Sin observaciones' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Fecha de registro</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ $compra->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ $compra->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
            @if ($compra->estado === 'PENDIENTE')
                <div class="flex flex-wrap gap-3 border-t border-slate-200 px-5 py-4">
                    <a href="{{ route('compras.edit', $compra) }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Editar pendiente
                    </a>
                    <form action="{{ route('compras.registrar', $compra) }}" method="POST"
                        onsubmit="return confirm('¿Registrar esta compra? Se actualizarán inventario y precios.');">
                        @csrf
                        <button type="submit"
                            class="rounded-xl bg-green-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-800">
                            Registrar compra
                        </button>
                    </form>
                    <form action="{{ route('compras.destroy', $compra) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar esta compra pendiente?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50">
                            Eliminar pendiente
                        </button>
                    </form>
                </div>
            @elseif ($compra->estado === 'REGISTRADA')
                <div class="flex justify-end border-t border-slate-200 px-5 py-4">
                    <form action="{{ route('compras.revertir', $compra) }}" method="POST"
                        onsubmit="return confirm('¿Revertir esta compra? Esta acción no se puede deshacer.');">
                        @csrf
                        <button type="submit"
                            class="rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50">
                            Revertir compra
                        </button>
                    </form>
                </div>
            @endif
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                    Productos de la compra
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Cantidad</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Costo unitario</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Costo promedio anterior</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Costo promedio nuevo</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Precio unitario nuevo</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Precio docena</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Precio ciento</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($compra->detalles as $detalle)
                            <tr class="hover:bg-indigo-50/50">
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
                                <td class="px-4 py-3 text-center">{{ number_format((int) $detalle->cantidad) }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    Q {{ number_format((float) $detalle->costo_unitario, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{ $detalle->costo_promedio_anterior !== null
                                        ? 'Q ' . number_format((float) $detalle->costo_promedio_anterior, 2)
                                        : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{ $detalle->costo_promedio_nuevo !== null
                                        ? 'Q ' . number_format((float) $detalle->costo_promedio_nuevo, 2)
                                        : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{ $detalle->precio_unitario_nuevo !== null
                                        ? 'Q ' . number_format((float) $detalle->precio_unitario_nuevo, 2)
                                        : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{ $detalle->precio_docena_nuevo !== null
                                        ? 'Q ' . number_format((float) $detalle->precio_docena_nuevo, 2)
                                        : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{ $detalle->precio_ciento_nuevo !== null
                                        ? 'Q ' . number_format((float) $detalle->precio_ciento_nuevo, 2)
                                        : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold whitespace-nowrap text-slate-900">
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
                    </tbody>
                    @if ($compra->detalles->isNotEmpty())
                        <tfoot class="bg-slate-50">
                            <tr>
                                <th colspan="8" class="px-4 py-3 text-right text-sm font-semibold text-slate-700">
                                    Total de la compra
                                </th>
                                <th class="px-4 py-3 text-right text-sm font-bold whitespace-nowrap text-slate-900">
                                    Q {{ number_format((float) $compra->costo_total, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </section>
    </div>
</x-app-layout>