<h3 {{ $attributes->merge([
    'class' => 'flex items-center gap-2 text-xl font-medium leading-6 text-indigo-700 pl-4'
]) }}>
    {{ $slot }}
</h3>