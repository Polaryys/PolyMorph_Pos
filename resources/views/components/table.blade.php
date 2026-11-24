@props(['headers', 'paginator' => null])

<div class="overflow-hidden rounded-xl border border-app-border bg-app-surface shadow-sm">

    <div class="overflow-x-auto scrollbar-app">
        <table class="min-w-full border-collapse text-left text-sm">

            {{-- Encabezado --}}
            <thead class="bg-app-table-header text-xs font-semibold uppercase tracking-wider text-app-table-header-text">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>

            {{-- Contenido --}}
            <tbody
                class="divide-y divide-app-border text-app-text-secondary
                    [&_tr:nth-child(odd)]:bg-app-surface
                    [&_tr:nth-child(even)]:bg-app-surface-alt/30
                    [&_tr:hover]:bg-app-surface-hover
                    [&_tr]:transition-colors [&_tr]:duration-150">

                {{ $slot }}

            </tbody>

        </table>
    </div>

    {{-- Paginación --}}
    @if ($paginator && $paginator->hasPages())
        <div
            class="flex flex-col gap-3 border-t border-app-border
                    bg-app-surface-alt/50 px-4 py-3 sm:px-6">
            {{ $paginator->links() }}
        </div>
    @endif

</div>
