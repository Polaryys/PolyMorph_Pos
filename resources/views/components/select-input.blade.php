@props(['disabled' => false, 'error' => false])

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full rounded-md border p-2 text-sm shadow-sm
            bg-app-surface text-app-text-primary
            focus:outline-none focus:ring-1
            focus:border-app-border-focus focus:ring-app-border-focus
            focus-visible:border-app-border-focus focus-visible:ring-app-border-focus
            disabled:cursor-not-allowed disabled:bg-app-surface-alt disabled:opacity-75'
    ])->class([
        'border-red-500 focus:border-red-500 focus:ring-red-500 focus-visible:border-red-500 focus-visible:ring-red-500' => $error,
        'border-app-border' => !$error,
    ]) }}
>
    {{ $slot }}
</select>