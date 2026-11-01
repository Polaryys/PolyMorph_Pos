<a {{ $attributes->merge([
    'class' => 'inline-flex items-center justify-center rounded-md
        border border-gray-300 bg-white py-2 px-4
        text-sm font-medium text-gray-700 shadow-sm
        hover:bg-gray-50 transition'
]) }}>
    {{ $slot }}
</a>