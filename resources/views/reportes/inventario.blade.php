<x-app-layout>

    {{-- Indicadores generales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">

        {{-- Total de productos --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Productos registrados
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-800">
                        {{ number_format($resumen->productos) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Productos activos
                    </p>
                </div>

                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-100 text-indigo-600">
                    <x-lucide-package class="w-6 h-6" />
                </div>
            </div>
        </div>

        {{-- Valor del inventario --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Valor del inventario
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-800">
                        Q {{ number_format($resumen->valorInventario, 2) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Calculado al costo promedio
                    </p>
                </div>

                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-100 text-indigo-600">
                    <x-lucide-wallet class="w-6 h-6" />
                </div>
            </div>
        </div>

        {{-- Productos agotados --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Productos agotados
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-red-600">
                        {{ number_format($agotados->total()) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Sin existencias disponibles
                    </p>
                </div>

                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-red-100 text-red-600">
                    <x-lucide-package-x class="w-6 h-6" />
                </div>
            </div>
        </div>

    </div>


    {{-- Tablas de inventario --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 items-start">

        {{-- PRODUCTOS AGOTADOS --}}
        <div class="min-w-0">

            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-800">
                        Productos agotados
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Productos sin existencias
                    </p>
                </div>

                <span
                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                    {{ $agotados->total() }}
                </span>
            </div>

            <x-table-secondary :paginator="$agotados">

                <x-slot:headers>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-3 py-3 text-center whitespace-nowrap">Stock actual</th>
                    <th class="px-3 py-3 text-center">Mínimo</th>
                </x-slot:headers>

                @forelse ($agotados as $producto)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">
                                {{ $producto->presentacionBase->nombre }}
                                @if ($producto->marca)
                                    <span class="text-slate-500 font-normal">
                                        · {{ $producto->marca->nombre }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-slate-400 mt-1 font-mono">
                                {{ $producto->presentacionBase->codigoSKU }}
                            </div>
                        </td>

                        <td class="px-3 py-3 text-center">
                            <span class="font-semibold text-red-600">
                                {{ $producto->cantidad }}
                            </span>
                        </td>

                        <td class="px-3 py-3 text-center text-slate-600">
                            {{ $producto->umbral }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <x-lucide-package-check class="w-7 h-7 text-slate-300" />
                                <span>No hay productos agotados.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </x-table-secondary>

        </div>


        {{-- PRODUCTOS CON STOCK BAJO --}}
        <div class="min-w-0">

            <div class="flex flex-col gap-3 mb-3">

                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-800">
                            Productos con bajo stock
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Productos por debajo del umbral
                        </p>
                    </div>

                    <span
                        class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                        {{ $productos->total() }}
                    </span>
                </div>

                {{-- Filtros --}}
                <div class="flex flex-wrap items-center gap-1.5">

                    <a href="{{ route('reportes.inventario') }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                        {{ !request('estado') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Todos
                    </a>

                    <a href="{{ route('reportes.inventario', ['estado' => 'critico']) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                        {{ request('estado') === 'critico' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                        Crítico
                    </a>

                    <a href="{{ route('reportes.inventario', ['estado' => 'muy_bajo']) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                        {{ request('estado') === 'muy_bajo' ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">
                        Muy bajo
                    </a>

                    <a href="{{ route('reportes.inventario', ['estado' => 'bajo']) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                        {{ request('estado') === 'bajo' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Bajo
                    </a>

                </div>

            </div>

            <x-table-secondary :paginator="$productos">

                <x-slot:headers>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-3 py-3 text-center whitespace-nowrap">Stock actual</th>
                    <th class="px-3 py-3 text-center">Mínimo</th>
                    <th class="px-3 py-3 text-center">Estado</th>
                </x-slot:headers>

                @forelse ($productos as $producto)
                    @php
                        $porcentajeStock = ($producto->cantidad / $producto->umbral) * 100;

                        if ($porcentajeStock <= 25) {
                            $estadoTexto = 'Crítico';
                            $estadoColor = 'bg-red-100 text-red-700';
                        } elseif ($porcentajeStock <= 50) {
                            $estadoTexto = 'Muy bajo';
                            $estadoColor = 'bg-orange-100 text-orange-700';
                        } else {
                            $estadoTexto = 'Bajo';
                            $estadoColor = 'bg-amber-100 text-amber-700';
                        }
                    @endphp

                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">
                                {{ $producto->presentacionBase->nombre }}
                                @if ($producto->marca)
                                    <span class="text-slate-500 font-normal">
                                        · {{ $producto->marca->nombre }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-slate-400 mt-1 font-mono">
                                {{ $producto->presentacionBase->codigoSKU }}
                            </div>
                        </td>

                        <td class="px-3 py-3 text-center font-semibold text-slate-700">
                            {{ $producto->cantidad }}
                        </td>

                        <td class="px-3 py-3 text-center text-slate-600">
                            {{ $producto->umbral }}
                        </td>

                        <td class="px-3 py-3 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $estadoColor }}">
                                {{ $estadoTexto }}
                            </span>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <x-lucide-package-check class="w-7 h-7 text-slate-300" />
                                <span>No hay productos con stock bajo.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </x-table-secondary>

        </div>

    </div>

</x-app-layout>
