@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-app-text-secondary']) }}>
    {{ $value ?? $slot }}
</label>