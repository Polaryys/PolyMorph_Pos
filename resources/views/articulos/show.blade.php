<x-app-layout>
    @php
        $presentacion = $articulo->presentaciones->first();
    @endphp

    <div class="space-y-6">
        {{-- Información general --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                    Detalles del {{ $articulo->tipo === 'PRODUCTO' ? 'Producto' : 'Servicio' }}
                </h3>
            </div>

            <div class="grid grid-cols-1 items-start gap-6 p-5 md:grid-cols-[220px_minmax(0,1fr)]">

                {{-- Imagen --}}
                <div class="flex justify-center md:justify-start">
                    <div class="relative h-56 w-56 shrink-0">

                        <img src="{{ $presentacion?->imagen
                            ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                            : asset('images/default-avatar.png') }}"
                            alt="Imagen del artículo"
                            class="h-56 w-56 rounded border-2 border-indigo-100
                                   bg-gray-50 object-cover shadow-sm">

                    </div>
                </div>

                {{-- Datos generales --}}
                <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- Tipo --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tipo</p>

                        <div class="mt-1">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $articulo->tipo === 'PRODUCTO' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $articulo->tipo }}
                            </span>
                        </div>
                    </div>

                    {{-- Estado del inventario --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estado del artículo</p>

                        <div class="mt-1">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $articulo->estado ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $articulo->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    {{-- Fecha de creación --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de creación</p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $articulo->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    {{-- Última edición --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Última edición</p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $articulo->updated_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    {{-- Nombre --}}
                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Nombre</p>

                        <p class="mt-1 break-words text-base font-semibold text-gray-800">
                            {{ $presentacion?->nombre ?? '—' }}
                        </p>
                    </div>

                    {{-- SKU --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Código SKU</p>

                        <p class="mt-1 break-all text-sm text-gray-800">
                            {{ $presentacion?->codigoSKU ?? '—' }}
                        </p>
                    </div>

                    {{-- Código de barras --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Código de barras</p>

                        <p class="mt-1 break-all text-sm text-gray-800">
                            {{ $presentacion?->codigoBarras ?? '—' }}
                        </p>
                    </div>

                    {{-- Categoría --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Categoría</p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $articulo->categoria?->nombre ?? 'Sin categoría' }}
                        </p>
                    </div>

                    {{-- Marca --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Marca</p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $articulo->marca?->nombre ?? 'Sin marca' }}
                        </p>
                    </div>
                    {{-- Vencimiento --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de vencimiento</p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $presentacion?->fecha_vencimiento
                                ? \Carbon\Carbon::parse($presentacion->fecha_vencimiento)->format('d/m/Y')
                                : 'No registrada' }}
                        </p>
                    </div>

                    {{-- Descripción --}}
                    <div class="">
                        <p class="text-sm font-medium text-gray-500">
                            Descripción o detalle
                        </p>

                        <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-gray-700">
                            {{ $presentacion?->detalle ?? 'Sin descripción registrada.' }}
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Información de inventario y precios del producto --}}
        @if ($articulo->tipo === 'PRODUCTO')
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Información de inventario
                    </h3>
                </div>

                <div class="p-5">

                    {{-- Cantidades y costos --}}
                    <div class="mb-6 overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Existencias actuales</th>
                                    <th class="px-4 py-3">Umbral de inventario</th>
                                    <th class="px-4 py-3">Costo promedio unitario</th>
                                    <th class="px-4 py-3">Ubicación</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-800">
                                        {{ number_format($articulo->cantidad ?? 0, 0) }} unidades
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-800">
                                        {{ number_format($articulo->umbral ?? 0, 0) }} unidades
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-800">
                                        Q {{ number_format($articulo->costoPromedioUnitario ?? 0, 2) }}
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-800">
                                        {{ $presentacion?->ubicacion ?? 'Sin ubicación registrada' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Precios de venta --}}
                    <h4 class="mb-3 text-sm font-semibold text-gray-700">
                        Precios de venta
                    </h4>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Escala de precio</th>
                                    <th class="px-4 py-3">Cantidad de referencia</th>
                                    <th class="px-4 py-3">Precio de venta</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                        Unitario
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        1 unidad
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-indigo-600">
                                        Q {{ number_format($presentacion?->precioUnitario ?? 0, 2) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                        Docena
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        12 unidades
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-indigo-600">
                                        @if ($presentacion?->precioUnitarioDocena !== null)
                                            Q {{ number_format($presentacion->precioUnitarioDocena, 2) }}
                                        @else
                                            <span class="font-normal text-gray-400">No definido</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                        Ciento
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        100 unidades
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-indigo-600">
                                        @if ($presentacion?->precioUnitarioCiento !== null)
                                            Q {{ number_format($presentacion->precioUnitarioCiento, 2) }}
                                        @else
                                            <span class="font-normal text-gray-400">No definido</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Información del servicio --}}
        @if ($articulo->tipo === 'SERVICIO')
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Información del servicio
                    </h3>
                </div>

                <div class="p-5">

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Concepto</th>
                                    <th class="px-4 py-3">Precio de venta</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800">
                                        Precio unitario del servicio
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-base font-semibold text-indigo-600">
                                        Q {{ number_format($presentacion?->precioUnitario ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @endif

        {{-- Acciones --}}
        <div class="flex justify-end gap-3 pb-4">

            <a href="{{ route('articulos.index') }}"
                class="rounded-md border border-gray-300 bg-white px-4 py-2
                       text-sm font-medium text-gray-700 shadow-sm
                       transition hover:bg-gray-50">
                Volver
            </a>

            <a href="{{ route('articulos.edit', $articulo->id) }}"
                class="inline-flex justify-center rounded-md border border-transparent
                       bg-indigo-600 px-4 py-2 text-sm font-medium text-white
                       shadow-sm transition hover:bg-indigo-700">
                Editar artículo
            </a>

        </div>

    </div>
</x-app-layout>
