@php
    $success = session('success');
    $error = session('error');

    $type = $success ? 'success' : ($error ? 'error' : null);
    $message = $success ?? $error;

    $styles = [
        'success' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
        'error' => 'text-red-600 dark:text-red-400 bg-red-500/10 border-red-500/20',
    ];
@endphp

@if ($message)
    <div
        id="alert-message"
        role="alert"
        class="mb-4 flex items-center justify-between gap-4 rounded-lg border p-4 text-sm {{ $styles[$type] }}"
    >
        <span>{{ $message }}</span>

        <button
            type="button"
            onclick="this.parentElement.remove()"
            class="shrink-0 opacity-70 transition-opacity hover:opacity-100"
            aria-label="Cerrar alerta"
        >
            <x-lucide-x class="h-5 w-5" />
        </button>
    </div>
@endif