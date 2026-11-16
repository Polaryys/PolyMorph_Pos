@props(['label'])

<div {{ $attributes->merge(['class' => 'min-w-0 rounded-lg border border-app-border bg-app-surface px-3 py-2.5']) }}>
    <p class="text-xs font-medium text-app-text-muted">{{ $label }}</p>
    <div class="mt-1 wrap-break-word text-sm font-medium text-app-text-primary">
        {{ $slot }}
    </div>
</div>