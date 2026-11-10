
@php
    $success = session('success');
    $error = session('error');

    $type = $success ? 'success' : ($error ? 'error' : null);
    $message = $success ?? $error;

    $styles = [
        'success' => 'text-green-700 bg-green-100 border-green-200',
        'error' => 'text-red-700 bg-red-100 border-red-200',
    ];
@endphp

@if ($message)
    <div
        id="alert-message"
        role="alert"
        class="mb-4 p-4 text-sm rounded-lg border flex items-center justify-between gap-4 {{ $styles[$type] }}"
    >
        <span>{{ $message }}</span>

        <button
            type="button"
            onclick="this.parentElement.remove()"
            class="shrink-0 opacity-70 hover:opacity-100 transition-opacity"
            aria-label="Cerrar alerta"
        >
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>
@endif
