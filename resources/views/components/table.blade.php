@props(['headers', 'paginator' => null])

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse text-left text-sm">

            {{-- Encabezado --}}
            <thead class="bg-indigo-100 text-xs font-semibold uppercase tracking-wider text-indigo-600">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>

            {{-- Contenido --}}
            <tbody
                class="divide-y divide-slate-200 text-slate-600
                [&>tr:nth-child(odd)]:bg-white
                [&>tr:nth-child(even)]:bg-slate-50
                [&>tr:hover]:bg-indigo-50/70
                [&>tr]:transition-colors [&>tr]:duration-150">

                {{ $slot }}

            </tbody>

        </table>
    </div>

    {{-- Paginación --}}
    @if ($paginator && $paginator->hasPages())
        <div class="flex flex-col gap-3 border-t border-slate-200
                    bg-slate-50/50 px-4 py-3 sm:px-6">
            {{ $paginator->links() }}
        </div>
    @endif

</div>
