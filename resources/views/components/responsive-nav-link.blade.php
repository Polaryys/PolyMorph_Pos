@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-app-primary-400 text-start text-base font-medium
            text-app-primary-700 bg-app-primary-100 focus:outline-none focus:text-app-primary-800 focus:bg-app-primary-200
            focus:border-app-primary-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium
            text-app-text-secondary hover:text-app-text-primary hover:bg-app-surface-hover hover:border-app-border-hover focus:outline-none
            focus:text-app-text-primary focus:bg-app-surface-hover focus:border-app-border-hover transition duration-150
            ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
