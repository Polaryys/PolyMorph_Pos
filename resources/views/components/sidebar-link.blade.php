@props(['active' => false])

@php
    $classes = $active
        ? 'group flex items-center gap-3 px-3 py-2.5 rounded-lg
           border-l-4 border-primary
           bg-primary text-white
           text-base font-medium shadow-sm
           transition-colors duration-150'
        : 'group flex items-center gap-3 px-3 py-2.5 rounded-lg
           border-l-4 border-transparent
           text-app-text/80 text-base font-medium
           hover:bg-primary-light hover:text-primary
           hover:border-primary
           transition-colors duration-150';
@endphp



<a {{ $attributes->merge(['class' => $classes]) }} @if ($active) aria-current="page" @endif>

    @if (isset($icon))
        <div class="shrink-0 {{ $active ? 'text-white' : 'text-app-text group-hover:text-primary' }}">
            {{ $icon }}
        </div>
    @endif

    <span class="min-w-0">
        {{ $slot }}
    </span>

</a>
