@props(['disabled' => false, 'error' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full rounded-md border p-2 text-sm shadow-sm
            focus:ring-indigo-500
            disabled:cursor-not-allowed disabled:bg-slate-100 disabled:opacity-75'
    ])->class([
        'border-red-500 focus:border-red-500 focus:ring-red-500' => $error,
        'border-slate-300 focus:border-indigo-500' => !$error,
    ]) }}
>