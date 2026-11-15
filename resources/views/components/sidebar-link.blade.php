
@props(['active' => false])

@php
    $classes = $active
        ? 'group flex items-center gap-3 px-3 py-2
           rounded-lg text-[15px] font-medium
           bg-primary text-text-on-primary shadow-sm'
        : 'group flex items-center gap-3 px-3 py-2
           rounded-lg text-[15px] font-medium
           text-text-secondary
           transition-colors duration-200
           hover:bg-surface-alt hover:text-text-primary';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
        <span class="shrink-0">
            {{ $icon }}
        </span>
    @endisset

    <span class="truncate">
        {{ $slot }}
    </span>
</a>
