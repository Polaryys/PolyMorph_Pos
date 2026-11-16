
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Móvil --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">

            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center rounded-lg border border-app-border
                           bg-app-surface-alt px-4 py-2 text-sm font-medium
                           text-app-text-muted cursor-not-allowed">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center rounded-lg border border-app-border
                           bg-app-surface px-4 py-2 text-sm font-medium text-app-text-secondary
                           transition hover:bg-app-primary-500/10 hover:text-app-primary-700
                           focus:outline-none focus:ring-2 focus:ring-app-primary-500
                           focus:ring-offset-2 focus:ring-offset-app-surface">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center rounded-lg border border-app-border
                           bg-app-surface px-4 py-2 text-sm font-medium text-app-text-secondary
                           transition hover:bg-app-primary-500/10 hover:text-app-primary-700
                           focus:outline-none focus:ring-2 focus:ring-app-primary-500
                           focus:ring-offset-2 focus:ring-offset-app-surface">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="inline-flex items-center rounded-lg border border-app-border
                           bg-app-surface-alt px-4 py-2 text-sm font-medium
                           text-app-text-muted cursor-not-allowed">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        {{-- Escritorio --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">

            {{-- Información de registros --}}
            <div>
                <p class="text-sm text-app-text-secondary">
                    {!! __('Showing') !!}

                    @if ($paginator->firstItem())
                        <span class="font-semibold text-app-text-primary">
                            {{ $paginator->firstItem() }}
                        </span>

                        {!! __('to') !!}

                        <span class="font-semibold text-app-text-primary">
                            {{ $paginator->lastItem() }}
                        </span>
                    @else
                        {{ $paginator->count() }}
                    @endif

                    {!! __('of') !!}

                    <span class="font-semibold text-app-text-primary">
                        {{ $paginator->total() }}
                    </span>

                    {!! __('results') !!}
                </p>
            </div>

            {{-- Botones de paginación --}}
            <div>
                <span class="inline-flex items-center gap-1">

                    {{-- Anterior --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true"
                            class="inline-flex items-center justify-center rounded-lg
                                   border border-app-border bg-app-surface-alt p-2
                                   text-app-text-muted cursor-not-allowed">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            aria-label="{{ __('pagination.previous') }}"
                            class="inline-flex items-center justify-center rounded-lg
                                   border border-app-border bg-app-surface p-2 text-app-text-secondary
                                   transition hover:border-app-primary-500/40
                                   hover:bg-app-primary-500/10 hover:text-app-primary-700
                                   focus:outline-none focus:ring-2 focus:ring-app-primary-500
                                   focus:ring-offset-2 focus:ring-offset-app-surface">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Números de página --}}
                    @foreach ($elements as $element)
                        {{-- Puntos suspensivos --}}
                        @if (is_string($element))
                            <span class="px-2 py-2 text-sm font-medium text-app-text-muted">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Enlaces --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex min-w-9 items-center justify-center
                                               rounded-lg border border-app-primary-600
                                               bg-app-primary-600 px-3 py-2 text-sm
                                               font-semibold text-white shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                        class="inline-flex min-w-9 items-center justify-center
                                               rounded-lg border border-app-border bg-app-surface
                                               px-3 py-2 text-sm font-medium text-app-text-secondary
                                               transition hover:border-app-primary-500/40
                                               hover:bg-app-primary-500/10 hover:text-app-primary-700
                                               focus:outline-none focus:ring-2 focus:ring-app-primary-500
                                               focus:ring-offset-2 focus:ring-offset-app-surface">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Siguiente --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            aria-label="{{ __('pagination.next') }}"
                            class="inline-flex items-center justify-center rounded-lg
                                   border border-app-border bg-app-surface p-2 text-app-text-secondary
                                   transition hover:border-app-primary-500/40
                                   hover:bg-app-primary-500/10 hover:text-app-primary-700
                                   focus:outline-none focus:ring-2 focus:ring-app-primary-500
                                   focus:ring-offset-2 focus:ring-offset-app-surface">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 01-1.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true"
                            class="inline-flex items-center justify-center rounded-lg
                                   border border-app-border bg-app-surface-alt p-2
                                   text-app-text-muted cursor-not-allowed">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                </span>
            </div>
        </div>

    </nav>
@endif
