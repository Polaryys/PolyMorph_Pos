@props(['headers', 'paginator' => null])

<div class="overflow-hidden rounded-xl border border-app-border bg-app-surface">

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse text-left text-sm">

            {{-- Encabezado --}}
            <thead class="bg-app-bg text-xs font-semibold uppercase tracking-wider text-app-text">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>

            {{-- Contenido --}}
            <tbody
                class="divide-y divide-app-border text-app-text
                [&>tr:nth-child(odd)]:bg-app-surface
                [&>tr:nth-child(even)]:bg-app-bg/50
                [&>tr:hover]:bg-primary-light/40
                [&>tr]:transition-colors [&>tr]:duration-150">

                {{ $slot }}

            </tbody>

        </table>
    </div>

    {{-- Paginación --}}
    @if ($paginator && $paginator->hasPages())
        <div class="flex flex-col gap-3 border-t border-app-border
                    bg-app-bg px-4 py-3 sm:px-6">
            {{ $paginator->links() }}
        </div>
    @endif

</div>
