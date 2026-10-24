@props(['headers', 'paginator' => null])

<div class="bg-white border border-slate-300 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left border-collapse">
            <thead class="bg-slate-200/75 text-slate-600 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
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