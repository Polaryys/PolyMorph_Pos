<div class="flex items-center justify-between">
    <a
        {{ $attributes->merge([
            'class' =>
                'px-4 py-2 bg-app-primary-600 hover:bg-app-primary-700 text-white text-sm font-medium
                rounded-md shadow-sm transition',
        ]) }}>
        {{ $slot }}
    </a>
</div>