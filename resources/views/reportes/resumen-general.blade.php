<x-app-layout>

    {{-- Filtros --}}
    <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-4 mb-3">
        <form action="{{ route('reportes.resumen-general') }}" method="GET"
            class="flex flex-col sm:flex-row sm:items-end gap-3">

            {{-- Semana --}}
            <div class="w-full sm:max-w-xs">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="semana" class="block text-sm font-medium text-app-text-secondary">
                        Semana del reporte
                    </label>

                    <span class="text-sm text-app-text-muted">
                        {{ $inicio->format('d/m/Y') }} - {{ $fin->format('d/m/Y') }}
                    </span>
                </div>

                <input type="week" id="semana" name="semana" value="{{ $semana }}"
                    class="w-full p-2 rounded-lg border border-app-border bg-app-surface-alt
                    text-app-text-primary text-sm shadow-sm
                    focus:border-app-primary-500 focus:ring-app-primary-500
                    transition [color-scheme:light] dark:[color-scheme:dark]">
            </div>

            {{-- Consultar --}}
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-4 py-2
                rounded-lg bg-app-primary-600 hover:bg-app-primary-700
                text-white text-sm font-medium transition shadow-sm">

                <x-lucide-search class="w-4 h-4" />
                Consultar
            </button>

        </form>

    </div>

    {{-- Indicadores --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">

        {{-- Ventas netas --}}
        <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-app-text-secondary">
                        Ventas netas
                    </p>

                    <p class="mt-2 text-xl font-semibold text-app-text-primary">
                        Q {{ number_format($ventasSemana, 2) }}
                    </p>

                    <p class="mt-1 text-xs text-app-text-muted">
                        Cobros netos de la semana seleccionada
                    </p>
                </div>

                <div
                    class="flex items-center justify-center w-12 h-12 rounded-lg
                    bg-app-primary-100/70 dark:bg-app-primary-900/40
                    text-app-primary-600 dark:text-app-primary-300">

                    <x-lucide-wallet class="w-6 h-6" />
                </div>

            </div>
        </div>

        {{-- Utilidad bruta --}}
        <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-app-text-secondary">
                        Utilidad bruta
                    </p>

                    <p class="mt-2 text-xl font-semibold text-app-text-primary">
                        Q {{ number_format($utilidadSemana, 2) }}
                    </p>

                    <p class="mt-1 text-xs text-app-text-muted">
                        Operaciones de la semana seleccionada
                    </p>
                </div>

                <div
                    class="flex items-center justify-center w-12 h-12 rounded-lg
                    bg-app-primary-100/70 dark:bg-app-primary-900/40
                    text-app-primary-600 dark:text-app-primary-300">

                    <x-lucide-chart-no-axes-combined class="w-6 h-6" />
                </div>

            </div>
        </div>

    </div>

    {{-- Gráfico semanal --}}
    <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5">

        <div class="mb-5">
            <h3 class="text-sm font-semibold text-app-text-primary">
                Actividad semanal
            </h3>

            <p class="mt-1 text-xs text-app-text-muted">
                Comparación diaria de ventas netas y utilidad bruta
            </p>
        </div>

        <div class="relative h-80 w-full">
            <canvas id="graficoResumenSemanal" data-labels="{{ json_encode($resumenDiario->pluck('dia')->values()) }}"
                data-ventas="{{ json_encode($resumenDiario->pluck('ventas')->values()) }}"
                data-utilidad="{{ json_encode($resumenDiario->pluck('utilidad')->values()) }}">
            </canvas>
        </div>

    </div>

    {{-- Productos más vendidos --}}
    <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5 mt-3">

        {{-- Encabezado --}}
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-app-text-primary">
                Productos más vendidos
            </h3>

            <p class="mt-1 text-xs text-app-text-muted">
                Artículos con mayor cantidad neta vendida durante la semana
            </p>
        </div>

        @if ($productosMasVendidos->isNotEmpty())

            <div class="divide-y divide-app-border">

                @foreach ($productosMasVendidos as $indice => $producto)
                    <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">

                        {{-- Posición y producto --}}
                        <div class="flex items-center gap-3 min-w-0">

                            <div
                                class="flex items-center justify-center shrink-0
                            w-8 h-8 rounded-lg
                            bg-app-primary-100/70 dark:bg-app-primary-900/40
                            text-app-primary-600 dark:text-app-primary-300
                            text-sm font-semibold">

                                {{ $indice + 1 }}

                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-app-text-primary truncate">
                                    {{ $producto->nombre_completo }}
                                </p>

                                <p class="mt-0.5 text-xs text-app-text-muted">
                                    Q {{ number_format($producto->total_vendido, 2) }} vendidos
                                </p>
                            </div>

                        </div>

                        {{-- Cantidad --}}
                        <div class="text-right shrink-0">
                            <p class="text-sm font-semibold text-app-text-primary tabular-nums">
                                {{ number_format($producto->cantidad_neta, 0) }}
                            </p>

                            <p class="text-xs text-app-text-muted">
                                Unidades
                            </p>
                        </div>

                    </div>
                @endforeach

            </div>
        @else
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <x-lucide-package-open class="w-9 h-9 text-app-text-muted mb-2" />

                <p class="text-sm font-medium text-app-text-secondary">
                    Sin productos vendidos
                </p>

                <p class="mt-1 text-xs text-app-text-muted">
                    No se encontraron ventas de productos durante esta semana.
                </p>
            </div>

        @endif

    </div>

</x-app-layout>
