@props(['active'])

@php
$classes = ($active ?? false)
            ? 'relative inline-flex items-end px-4 pt-3 pb-3 text-lg font-semibold leading-5 bg-white text-indigo-600 focus:outline-none transition rounded-t-xl duration-150 ease-in-out'
            : 'relative inline-flex items-end px-2 pt-2.5 pb-3 text-base font-medium leading-5 text-gray-300 hover:text-gray-100 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>

    <span class="relative z-10">
        {{ $slot }}
    </span>
</a>