<h3 {{ $attributes->merge([
    'class' => 'flex items-center gap-2 text-lg font-medium leading-6
        text-app-text-primary pb-2 pl-4'
]) }}>
    {{ $slot }}
</h3>