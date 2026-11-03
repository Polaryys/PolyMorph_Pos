<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex justify-center rounded-md border border-transparent
            bg-green-700/80 py-2 px-4 text-sm font-medium text-white
            shadow-sm hover:bg-green-700 active:bg-indigo-800
            focus:outline-none focus:ring-2 focus:ring-indigo-500
            focus:ring-offset-2 transition duration-150',
    ]) }}>
    {{ $slot }}
</button>
