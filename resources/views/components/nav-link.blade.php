@props(['active'])

@php
$classes = ($active ?? false)
            ? 'relative inline-flex items-center px-2 pt-1 border-b-2 border-indigo-600 text-sm font-semibold leading-5 text-indigo-600 focus:outline-none transition duration-150 ease-in-out'
            : 'relative inline-flex items-center px-2 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Resplandor ultra delicado hacia arriba --}}
    @if ($active ?? false)
        <span class="absolute bottom-0 inset-x-0 h-4 bg-gradient-to-t from-indigo-500/10 to-transparent pointer-events-none"></span>
    @endif

    <span class="relative z-10">
        {{ $slot }}
    </span>
</a>