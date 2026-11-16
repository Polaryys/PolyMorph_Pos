@props(['action', 'buscar' => '', 'placeholder' => 'Buscar...'])

<div class="flex flex-col gap-2 sm:flex-row sm:items-center">

    <form action="{{ $action }}" method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <x-lucide-search class="w-5 h-5 shrink-0 text-app-text-muted ml-2" />
        <x-text-input type="search" name="buscar" value="{{ $buscar }}" placeholder="{{ $placeholder }}"
            class="sm:w-96" />

        <div class="flex gap-2">

            <button type="submit"
                class="px-4 py-2 bg-app-primary-600 hover:bg-app-primary-700
                text-white text-sm font-medium rounded-md shadow-sm transition">
                Buscar
            </button>

            @if ($buscar !== '')
                <a href="{{ $action }}" wire:navigate
                    class="px-4 py-2 border border-app-border bg-app-surface text-app-text-secondary
                    text-sm font-medium rounded-md shadow-sm transition
                    hover:bg-app-surface-hover hover:text-app-text-primary">
                    Limpiar
                </a>
            @endif

        </div>
    </form>

    {{ $slot }}

</div>
