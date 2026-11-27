<x-app-layout>

    {{-- Filtros --}}
    <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-4 mb-3">
        <form action="{{ route('reportes.pagos') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">

            {{-- Fecha --}}
            <div class="w-full sm:max-w-xs">
                <label for="fecha" class="block text-sm font-medium text-app-text-secondary mb-1.5">
                    Fecha del reporte
                </label>

                <input type="date" id="fecha" name="fecha" value="{{ $fecha }}"
                    max="{{ now()->toDateString() }}"
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

        {{-- Ventas del día --}}
        <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-app-text-secondary">
                        Ventas del día
                    </p>

                    <p class="mt-2 text-xl font-semibold text-app-text-primary">
                        Q {{ number_format($ventasDia, 2) }}
                    </p>

                    <p class="mt-1 text-xs text-app-text-muted">
                        Cobros netos de la fecha seleccionada
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

        <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-app-text-secondary">
                        Utilidad bruta
                    </p>

                    <p class="mt-2 text-xl font-semibold text-app-text-primary">
                        Q {{ number_format($utilidadBruta, 2) }}
                    </p>

                    <p class="mt-1 text-xs text-app-text-muted">
                        Operaciones del día seleccionado
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


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Tabla de métodos de pago --}}
        <div class="bg-app-surface border border-app-border rounded-xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-app-border">
                <h3 class="text-sm font-semibold text-app-text-primary">
                    Resumen por método de pago
                </h3>
                <p class="mt-1 text-xs text-app-text-muted">
                    Cobros y reembolsos del día seleccionado
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-app-surface-alt text-app-text-secondary">
                        <tr>
                            <th class="px-5 py-3 font-medium">Método</th>
                            <th class="px-5 py-3 font-medium text-right">Cobrado</th>
                            <th class="px-5 py-3 font-medium text-right">Reembolsado</th>
                            <th class="px-5 py-3 font-medium text-right">Neto</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-app-border">
                        @forelse ($resumenMetodosPago as $metodo)
                            <tr class="hover:bg-app-surface-hover transition-colors">
                                <td class="px-5 py-3 font-medium text-app-text-primary">
                                    {{ $metodo->nombre }}
                                </td>
                                <td class="px-5 py-3 text-right text-app-text-primary tabular-nums">
                                    Q {{ number_format($metodo->cobrado, 2) }}
                                </td>
                                <td class="px-5 py-3 text-right text-rose-500 tabular-nums">
                                    Q {{ number_format($metodo->reembolsado, 2) }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-green-500 tabular-nums whitespace-nowrap">
                                    Q {{ number_format($metodo->neto, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-app-text-muted">
                                    No hay métodos de pago registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Gráfico de distribución --}}
        <div class="bg-app-surface border border-app-border rounded-xl shadow-sm p-5">

            <div class="mb-4">
                <h3 class="text-sm font-semibold text-app-text-primary">
                    Distribución de cobros
                </h3>
                <p class="mt-1 text-xs text-app-text-muted">
                    Participación por método de pago
                </p>
            </div>

            @if ($resumenMetodosPago->sum('cobrado') > 0)
                <div class="relative mx-auto w-full max-w-sm h-64">
                    <canvas id="graficoMetodosPago"
                        data-labels="{{ json_encode(
                            $resumenMetodosPago->filter(fn($metodo) => (float) $metodo->cobrado > 0)->pluck('nombre')->values(),
                        ) }}"
                        data-valores="{{ json_encode(
                            $resumenMetodosPago->filter(fn($metodo) => (float) $metodo->cobrado > 0)->pluck('cobrado')->values(),
                        ) }}">
                    </canvas>
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center text-center">
                    <x-lucide-chart-pie class="w-9 h-9 text-app-text-muted mb-3" />
                    <p class="text-sm font-medium text-app-text-secondary">
                        Sin cobros registrados
                    </p>
                    <p class="text-xs text-app-text-muted mt-1">
                        No hay información para representar.
                    </p>
                </div>
            @endif

        </div>

    </div>



</x-app-layout>
