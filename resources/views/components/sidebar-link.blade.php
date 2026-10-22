@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-semibold bg-indigo-600 text-white transition-colors shadow-sm'
    : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <div class="{{ $active ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">
            {{ $icon }}
        </div>
    @endif
    <span>{{ $slot }}</span>
</a>