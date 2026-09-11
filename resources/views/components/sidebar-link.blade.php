<a {{ $attributes->merge([
    'class' => 'flex items-center gap-3 px-3 py-2 rounded text-sm leading-4 font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100'
]) }}>
    {{ $icon }}
    <span>{{ $slot }}</span>
</a>