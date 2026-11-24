
@props([
    'label',
])

<div class="min-w-0 rounded-xl border border-app-border
            bg-app-surface-alt/60 px-4 py-3.5">

    <dt class="mb-1.5 text-xs font-medium uppercase tracking-wide
               text-app-text-muted">
        {{ $label }}
    </dt>

    <dd class="min-w-0 text-sm font-semibold text-app-text-primary
               break-words">
        {{ $slot }}
    </dd>

</div>
