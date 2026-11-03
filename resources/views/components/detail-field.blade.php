@props(['label'])

<div {{ $attributes->merge(['class' => 'min-w-0 rounded border border-slate-200 bg-white px-3 py-2.5']) }}>
    <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
    <div class="mt-1 break-words text-sm font-medium text-slate-800">
        {{ $slot }}
    </div>
</div>
