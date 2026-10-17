@props(['active'])

@php
$classes = ($active ?? false)
            ? 'relative inline-flex items-center px-2 pt-1 border-b-2 border-indigo-600 text-base font-semibold leading-5 text-indigo-600 focus:outline-none transition duration-150 ease-in-out'
            : 'relative inline-flex items-center px-2 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>


    <span class="relative z-10">
        {{ $slot }}
    </span>
</a>