<div class="flex items-center justify-between">
    <a
        {{ $attributes->merge([
            'class' =>
                'px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium 
                rounded-md shadow-sm transition',
        ]) }}>
        {{ $slot }}
    </a>
</div>
