@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'relative inline-flex items-center justify-center h-10 px-4 text-base font-medium
        text-app-nav-active-text bg-app-primary-500/15 rounded-lg
        focus:outline-none focus-visible:ring-2 focus-visible:ring-app-primary-500
        focus-visible:ring-offset-2 focus-visible:ring-offset-app-background
        transition duration-200 ease-in-out'
            : 'relative inline-flex items-center justify-center h-10 px-4 text-base font-medium
        text-app-text-secondary hover:text-app-text-primary hover:bg-app-surface-hover/70 rounded-lg
        focus:outline-none focus-visible:ring-2 focus-visible:ring-app-primary-500
        focus-visible:ring-offset-2 focus-visible:ring-offset-app-background
        transition duration-200 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="relative z-10">
        {{ $slot }}
    </span>
</a>