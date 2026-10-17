@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center gap-3 px-3 py-2 rounded-lg text-base font-semibold bg-indigo-50 text-indigo-700 transition-colors'
    : 'flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100/80 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <div class="{{ $active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}">
            {{ $icon }}
        </div>
    @endif
    <span>{{ $slot }}</span>
</a>