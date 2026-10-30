@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'relative inline-flex items-center justify-center h-10 px-4 text-base font-medium
        bg-indigo-100 text-indigo-500 rounded-lg focus:outline-none focus-visible:ring-2 
        focus-visible:ring-indigo-600 focus-visible:ring-offset-2 transition duration-150 ease-in-out'
        
        : 'relative inline-flex items-center justify-center h-10 px-4 text-base font-medium 
        text-gray-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg focus:outline-none 
        focus-visible:ring-2 focus-visible:ring-indigo-600 focus-visible:ring-offset-2 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="relative z-10">
        {{ $slot }}
    </span>
</a>