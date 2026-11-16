
@props(['active' => false])

@php
    $classes = $active
        ? 'group flex items-center gap-3 px-3 py-2
           rounded-lg text-[15px] font-medium
           bg-app-primary-600 text-white shadow-sm'
        : 'group flex items-center gap-3 px-3 py-2
            rounded-lg text-[15px] font-medium
            text-app-text-secondary
            transition-colors duration-200
            hover:bg-app-primary-500/10 hover:text-app-primary-500/90';
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
