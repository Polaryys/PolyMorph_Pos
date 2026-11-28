@props(['disabled' => false, 'error' => false])

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block rounded-md border p-2 text-sm shadow-sm 
            bg-app-surface-alt border-app-border-strong text-app-text-primary
            focus:outline-none focus:ring-1
            focus:border-app-app-primary-500 focus:ring-app-primary-500
            focus-visible:border-app-primary-500 focus-visible:ring-app-app-primary-500
            disabled:cursor-not-allowed disabled:bg-app-surface-alt disabled:opacity-75'
    ])->class([
        'border-red-500 focus:border-red-500 focus:ring-red-500 focus-visible:border-red-500 focus-visible:ring-red-500' => $error,
        'border-app-border' => !$error,
    ]) }}
>
    {{ $slot }}
</select>