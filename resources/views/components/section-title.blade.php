<h3 {{ $attributes->merge([
    'class' => 'flex items-center gap-2 text-lg font-semibold leading-6 text-indigo-700'
]) }}>
    {{ $slot }}
</h3>