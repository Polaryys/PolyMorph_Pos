<x-app-layout>
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-100 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Información general --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                    Detalles de la presentación
                </h3>
            </div>

            <div class="grid grid-cols-1 items-start gap-6 p-5 md:grid-cols-[220px_minmax(0,1fr)]">
                {{-- Imagen --}}
                <div class="flex justify-center md:justify-start">
                    <div class="relative h-56 w-56 shrink-0">
                        <img src="{{ $presentacion->imagen
                            ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                            : asset('images/default-avatar.png') }}"
                            alt="Imagen de {{ $presentacion->nombre }}"
                            class="h-56 w-56 rounded border-2 border-indigo-100 bg-gray-50 object-cover shadow-sm">
                    </div>
                </div>

                {{-- Datos generales --}}
                <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estado de la presentación</p>
                        <div class="mt-1">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $presentacion->estado ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $presentacion->estado ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de creación</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $presentacion->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Última edición</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $presentacion->updated_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Nombre</p>
                        <p class="mt-1 break-words text-base font-semibold text-gray-800">
                            {{ $presentacion->nombre }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Código SKU</p>
                        <p class="mt-1 break-all text-sm text-gray-800">
                            {{ $presentacion->codigoSKU }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Código de barras</p>
                        <p class="mt-1 break-all text-sm text-gray-800">
                            {{ $presentacion->codigoBarras ?? '—' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Artículo base</p>
                        <p class="mt-1 break-words text-sm text-gray-800">
                            {{ $presentacion->inventario?->presentaciones->first()?->nombre ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Categoría</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $presentacion->inventario?->categoria?->nombre ?? 'Sin categoría' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Marca</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $presentacion->inventario?->marca?->nombre ?? 'Sin marca' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de vencimiento</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $presentacion->fecha_vencimiento?->format('d/m/Y') ?? 'No registrada' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Descripción o detalle</p>
                        <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-gray-700">
                            {{ $presentacion->detalle ?? 'Sin descripción registrada.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventario y costo de la presentación --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                    Información de inventario y costos
                </h3>
            </div>

            <div class="p-5">
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Existencias del artículo base</th>
                                <th class="px-4 py-3">Factor</th>
                                <th class="px-4 py-3">Presentaciones disponibles</th>
                                <th class="px-4 py-3">Costo promedio por unidad base</th>
                                <th class="px-4 py-3">Costo promedio de esta presentación</th>
                                <th class="px-4 py-3">Ubicación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-800">
                                    {{ number_format($presentacion->inventario?->cantidad ?? 0, 0) }} unidades
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-800">
                                    {{ $presentacion->factor }} unidades
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-800">
                                    {{ intdiv(
                                        max(0, (int) ($presentacion->inventario?->cantidad ?? 0)),
                                        max(1, (int) ($presentacion->factor ?? 1)),
                                    ) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-800">
                                    Q
                                    {{ number_format((float) ($presentacion->inventario?->costoPromedioUnitario ?? 0), 2) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-800">
                                    Q {{ number_format($costoPromedioPresentacion, 2) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-800">
                                    {{ $presentacion->ubicacion ?? 'Sin ubicación registrada' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Precio de venta --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                    Precio de venta
                </h3>
            </div>

            <div class="p-5">
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Presentación</th>
                                <th class="px-4 py-3">Cantidad de referencia</th>
                                <th class="px-4 py-3">Precio de venta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $presentacion->nombre }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $presentacion->factor }} unidades
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-indigo-600">
                                    Q {{ number_format((float) ($presentacion->precioUnitario ?? 0), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex justify-end gap-3 pb-4">
            <a href="{{ route('presentaciones.index') }}"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                Volver
            </a>
            <a href="{{ route('presentaciones.edit', $presentacion->id) }}"
                class="inline-flex justify-center rounded-md border border-transparent
                       bg-indigo-600 px-4 py-2 text-sm font-medium text-white
                       shadow-sm transition hover:bg-indigo-700">
                Editar presentación
            </a>
        </div>
    </div>

</x-app-layout>
