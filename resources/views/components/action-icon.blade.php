@props([
    'title',
    'element' => 'button',
    'type' => 'button',
    'color' => 'indigo',
])

@php
    $colors = [
        'indigo' => 'text-slate-500 hover:text-indigo-600',
        'green' => 'text-slate-500 hover:text-green-600',
        'red' => 'text-slate-500 hover:text-red-600',
    ];
    $colorClass = $colors[$color] ?? $colors['indigo'];
@endphp

@if ($element === 'a')
    <a title="{{ $title }}" aria-label="{{ $title }}"
        {{ $attributes->merge(['class' => 'transition ' . $colorClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" title="{{ $title }}" aria-label="{{ $title }}"
        {{ $attributes->merge(['class' => 'transition ' . $colorClass]) }}>
        {{ $slot }}
    </button>
@endif
