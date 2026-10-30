@props(['active' => false])

@php
    $classes = $active
        ? 'group flex items-center gap-3 px-3 py-2.5 rounded-lg
           text-base font-semibold bg-indigo-600 text-white
           shadow-base transition-colors duration-150'
        : 'group flex items-center gap-3 px-3 py-2.5 rounded-lg
           text-base font-medium text-slate-300
           hover:text-white hover:bg-slate-800
           transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <div class="{{ $active ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">
            {{ $icon }}
        </div>
    @endif

    <span class="min-w-0">
        {{ $slot }}
    </span>
</a>
