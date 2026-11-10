<x-app-layout>
    @php
        $nombrePresentacion = trim(
            $presentacion->nombre . ($presentacion->inventario?->marca?->nombre ? ' - ' . $presentacion->inventario->marca->nombre : ''),
        );
        $presentacionBase = $presentacion->inventario?->presentaciones->first();
    @endphp

    <div class="space-y-8">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <section>
            <div class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                <div class="grid grid-cols-1 items-start gap-6 p-5 sm:p-6 md:grid-cols-[200px_minmax(0,1fr)]">
                    <div class="flex justify-center md:justify-start">
                        @if ($presentacion->imagen)
                            <img src="{{ asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo) }}"
                                alt="{{ $nombrePresentacion }}"
                                class="h-48 w-48 rounded-xl border border-slate-200 bg-slate-50 object-cover shadow-sm">
                        @else
                            <div
                                class="flex h-48 w-48 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-slate-400 shadow-sm">
                                <x-lucide-image class="h-16 w-16" />
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 space-y-5">
                        <div
                            class="flex flex-col justify-between gap-4 border-b border-slate-100 pb-4 lg:flex-row lg:items-start">
                            <div class="min-w-0">
                                <p class="mb-1 text-xs font-medium text-slate-500">Nombre y marca</p>
                                <h1 class="break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                                    {{ $nombrePresentacion }}
                                </h1>
                            </div>

                            <div class="grid shrink-0 grid-cols-2 gap-3 text-sm lg:min-w-[280px]">
                                <x-detail-field label="Creado en" class="bg-slate-50">
                                    <span class="tabular-nums">{{ $presentacion->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                </x-detail-field>
                                <x-detail-field label="Última edición" class="bg-slate-50">
                                    <span class="tabular-nums">{{ $presentacion->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                </x-detail-field>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                            <x-detail-field label="Estado">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                    {{ $presentacion->estado ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $presentacion->estado ? 'Activa' : 'Inactiva' }}
                                </span>
                            </x-detail-field>
                            <x-detail-field label="SKU">
                                {{ $presentacion->codigoSKU }}
                            </x-detail-field>
                            <x-detail-field label="Código de barras">
                                {{ $presentacion->codigoBarras ?? '—' }}
                            </x-detail-field>
                            <x-detail-field label="Artículo base">
                                {{ $presentacionBase?->nombre ?? '—' }}
                            </x-detail-field>
                            <x-detail-field label="Categoría">
                                {{ $presentacion->inventario?->categoria?->nombre ?? '—' }}
                            </x-detail-field>
                            <x-detail-field label="Vencimiento">
                                {{ $presentacion->fecha_vencimiento?->format('d/m/Y') ?? 'No registrada' }}
                            </x-detail-field>
                        </div>

                        <x-detail-field label="Detalles">
                            {{ $presentacion->detalle ?? 'Sin descripción registrada.' }}
                        </x-detail-field>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <x-section-title>Información de inventario y costos</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="whitespace-nowrap px-4 py-3">Existencias del artículo base</th>
                    <th class="whitespace-nowrap px-4 py-3">Factor</th>
                    <th class="whitespace-nowrap px-4 py-3">Presentaciones disponibles</th>
                    <th class="whitespace-nowrap px-4 py-3">Costo promedio por unidad base</th>
                    <th class="whitespace-nowrap px-4 py-3">Costo promedio de esta presentación</th>
                    <th class="whitespace-nowrap px-4 py-3">Ubicación</th>
                </x-slot:headers>
                <tr>
                    <td class="whitespace-nowrap px-4 py-4 font-semibold tabular-nums text-slate-800">
                        {{ number_format($presentacion->inventario?->cantidad ?? 0, 0) }} unidades
                    </td>
                    <td class="whitespace-nowrap px-4 py-4 text-slate-700">
                        {{ $presentacion->factor }} unidades
                    </td>
                    <td class="whitespace-nowrap px-4 py-4 tabular-nums text-slate-700">
                        {{ intdiv(
                            max(0, (int) ($presentacion->inventario?->cantidad ?? 0)),
                            max(1, (int) ($presentacion->factor ?? 1)),
                        ) }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-4 tabular-nums text-slate-700">
                        Q {{ number_format((float) ($presentacion->inventario?->costoPromedioUnitario ?? 0), 2) }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-4 font-semibold tabular-nums text-slate-800">
                        Q {{ number_format($costoPromedioPresentacion, 2) }}
                    </td>
                    <td class="px-4 py-4 text-slate-700">
                        {{ $presentacion->ubicacion ?? 'Sin ubicación registrada' }}
                    </td>
                </tr>
            </x-table>
        </section>

        <section>
            <x-section-title>Precios de venta</x-section-title>
            <x-table>
                <x-slot:headers>
                    <th class="px-4 py-3">Escala de precio</th>
                    <th class="px-4 py-3">Cantidad de referencia</th>
                    <th class="px-4 py-3">Precio de venta</th>
                </x-slot:headers>
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800">Unitario</td>
                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">1 unidad</td>
                    <td class="whitespace-nowrap px-4 py-3 font-semibold tabular-nums text-indigo-700">
                        Q {{ number_format((float) ($presentacion->precioUnitario ?? 0), 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800">Docena</td>
                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">12 unidades</td>
                    <td class="whitespace-nowrap px-4 py-3 font-semibold tabular-nums text-indigo-700">
                        @if ($presentacion->precioUnitarioDocena !== null)
                            Q {{ number_format((float) $presentacion->precioUnitarioDocena, 2) }}
                        @else
                            <span class="font-normal text-slate-400">No definido</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800">Ciento</td>
                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">100 unidades</td>
                    <td class="whitespace-nowrap px-4 py-3 font-semibold tabular-nums text-indigo-700">
                        @if ($presentacion->precioUnitarioCiento !== null)
                            Q {{ number_format((float) $presentacion->precioUnitarioCiento, 2) }}
                        @else
                            <span class="font-normal text-slate-400">No definido</span>
                        @endif
                    </td>
                </tr>
            </x-table>
        </section>

        @include('partials._historial-precios', ['historialPrecios' => $historialPrecios])

        <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-5 sm:flex-row">
            <x-secondary-link href="{{ route('presentaciones.index') }}">
                Volver
            </x-secondary-link>
            <x-secondary-link href="{{ route('presentaciones.edit', $presentacion->id) }}">
                Editar presentación
            </x-secondary-link>
        </div>
    </div>
</x-app-layout>
