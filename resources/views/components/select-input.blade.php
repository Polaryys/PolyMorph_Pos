
@props(['disabled' => false, 'error' => false])

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full rounded-md border p-2 text-sm shadow-sm
            focus:outline-none focus:ring-1
            focus:border-indigo-600 focus:ring-indigo-600
            focus-visible:border-indigo-500 focus-visible:ring-indigo-500
            disabled:cursor-not-allowed disabled:bg-slate-100 disabled:opacity-75
            '
    ])->class([
        'border-red-500 focus:border-red-500 focus:ring-red-500 focus-visible:border-red-500 focus-visible:ring-red-500' => $error,
        'border-slate-300' => !$error,
    ]) }}
>
    {{ $slot }}
</select>
