@props(['action', 'buscar' => '', 'placeholder' => 'Buscar...'])

<div class="flex flex-col gap-2 sm:flex-row sm:items-center">

    <form action="{{ $action }}" method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <x-lucide-search class="w-5 h-5 shrink-0 text-slate-400 ml-2" />
        <x-text-input type="search" name="buscar" value="{{ $buscar }}" placeholder="{{ $placeholder }}"
            class="sm:w-96" />

        <div class="flex gap-2">

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 
                text-white text-sm font-medium rounded-md shadow-sm transition">
                Buscar
            </button>

            @if ($buscar !== '')
                <a href="{{ $action }}" wire:navigate
                    class="px-4 py-2 border border-slate-300 bg-white text-slate-700
                    text-sm font-medium rounded-md shadow-sm transition hover:bg-slate-50">
                    Limpiar
                </a>
            @endif

        </div>
    </form>

    {{ $slot }}

</div>
