@props(['headers', 'paginator' => null])

<div class="overflow-hidden rounded-xl border border-app-border bg-app-surface">

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse text-left text-sm">

            {{-- Encabezado --}}
            <thead class="bg-app-surface-alt text-xs font-semibold uppercase tracking-wider text-app-text-primary">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>

            {{-- Contenido --}}
            <tbody
                class="divide-y divide-app-border text-app-text-secondary
                [&>tr:nth-child(odd)]:bg-app-surface
                [&>tr:nth-child(even)]:bg-app-surface-alt/40
                [&>tr:hover]:bg-app-surface-hover
                [&>tr]:transition-colors [&>tr]:duration-150">

                {{ $slot }}

            </tbody>

        </table>
    </div>

    {{-- Paginación --}}
    @if ($paginator && $paginator->hasPages())
        <div class="flex flex-col gap-3 border-t border-app-border
                    bg-app-surface-alt/50 px-4 py-3 sm:px-6">
            {{ $paginator->links() }}
        </div>
    @endif

</div>