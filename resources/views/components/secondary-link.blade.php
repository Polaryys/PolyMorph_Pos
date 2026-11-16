<a {{ $attributes->merge([
    'class' => 'inline-flex items-center justify-center rounded-md
        border border-app-border bg-app-surface py-2 px-4
        text-sm font-medium text-app-text-secondary shadow-sm
        hover:bg-app-surface-hover hover:text-app-text-primary
        transition'
]) }}>
    {{ $slot }}
</a>