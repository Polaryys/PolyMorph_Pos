@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Móvil --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">

            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center rounded-lg border border-slate-200
                             bg-slate-100 px-4 py-2 text-sm font-medium
                             text-slate-400 cursor-not-allowed">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center rounded-lg border border-slate-300
                          bg-white px-4 py-2 text-sm font-medium text-slate-700
                          transition hover:bg-indigo-50 hover:text-indigo-700
                          focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center rounded-lg border border-slate-300
                          bg-white px-4 py-2 text-sm font-medium text-slate-700
                          transition hover:bg-indigo-50 hover:text-indigo-700
                          focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="inline-flex items-center rounded-lg border border-slate-200
                             bg-slate-100 px-4 py-2 text-sm font-medium
                             text-slate-400 cursor-not-allowed">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        {{-- Escritorio --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">

            {{-- Información de registros --}}
            <div>
                <p class="text-sm text-slate-600">
                    {!! __('Showing') !!}

                    @if ($paginator->firstItem())
                        <span class="font-semibold text-slate-800">
                            {{ $paginator->firstItem() }}
                        </span>

                        {!! __('to') !!}

                        <span class="font-semibold text-slate-800">
                            {{ $paginator->lastItem() }}
                        </span>
                    @else
                        {{ $paginator->count() }}
                    @endif

                    {!! __('of') !!}

                    <span class="font-semibold text-slate-800">
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
                                     border border-slate-200 bg-slate-100 p-2
                                     text-slate-400 cursor-not-allowed">
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
                                  border border-slate-300 bg-white p-2 text-slate-600
                                  transition hover:border-indigo-300 hover:bg-indigo-50
                                  hover:text-indigo-700 focus:outline-none
                                  focus:ring-2 focus:ring-indigo-500">
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
                            <span class="px-2 py-2 text-sm font-medium text-slate-400">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Enlaces --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex min-w-9 items-center justify-center
                                                 rounded-lg border border-indigo-600
                                                 bg-indigo-600 px-3 py-2 text-sm
                                                 font-semibold text-white shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                        class="inline-flex min-w-9 items-center justify-center
                                              rounded-lg border border-slate-300 bg-white
                                              px-3 py-2 text-sm font-medium text-slate-600
                                              transition hover:border-indigo-300
                                              hover:bg-indigo-50 hover:text-indigo-700
                                              focus:outline-none focus:ring-2
                                              focus:ring-indigo-500">
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
                                  border border-slate-300 bg-white p-2 text-slate-600
                                  transition hover:border-indigo-300 hover:bg-indigo-50
                                  hover:text-indigo-700 focus:outline-none
                                  focus:ring-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 010 1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true"
                            class="inline-flex items-center justify-center rounded-lg
                                     border border-slate-200 bg-slate-100 p-2
                                     text-slate-400 cursor-not-allowed">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 010 1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                </span>
            </div>
        </div>

    </nav>
@endif
