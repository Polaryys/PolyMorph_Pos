@props(['headers', 'paginator' => null])

<div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden mt-3">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left border-collapse">

            <thead class="bg-slate-700 text-white text-xs uppercase tracking-wider font-semibold">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 text-sm text-slate-600
                [&>tr:nth-child(odd)]:bg-white
                [&>tr:nth-child(even)]:bg-slate-100/80">
                {{ $slot }}
            </tbody>

        </table>
    </div>

    @if ($paginator && $paginator->hasPages())
        <div class="px-6 py-3.5 border-t border-slate-200">
            {{ $paginator->links() }}
        </div>
    @endif
</div>