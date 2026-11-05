@props(['active' => false])

@php
    $classes = $active
        ? 'group flex items-center gap-3 px-3 py-2.5 rounded-lg
           border-l-4 border-indigo-600
           bg-indigo-600 text-white
           text-base font-medium shadow-sm
           transition-colors duration-150'
        : 'group flex items-center gap-3 px-3 py-2.5 rounded-lg
           border-l-4 border-transparent
           text-slate-600 text-base font-medium
           hover:bg-indigo-50 hover:text-indigo-600
           hover:border-indigo-200
           transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active) aria-current="page" @endif>

    @if (isset($icon))
        <div
            class="shrink-0 {{ $active ? 'text-white' : 'text-slate-500 group-hover:text-indigo-600' }}">
            {{ $icon }}
        </div>
    @endif

    <span class="min-w-0">
        {{ $slot }}
    </span>

</a>
