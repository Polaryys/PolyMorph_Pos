<div class="fixed top-0 left-0 right-0 md:left-56 z-20 bg-app-surface border-b border-app-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex w-full">

                {{-- Hamburguesa --}}
                <div class="flex items-center md:hidden shrink-0">
                    <button type="button" @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-app-text-muted hover:text-app-text-primary hover:bg-app-surface-hover
                               focus:outline-none transition duration-150 ease-in-out"
                        :aria-expanded="open.toString()" aria-label="Abrir o cerrar menú">

                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />

                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-3 ms-2 sm:ms-4 whitespace-nowrap overflow-x-auto no-scrollbar">
                    @foreach ($navMenus as $item)
                        @if ($item['disabled'] ?? false)
                            <span
                                class="inline-flex px-1 pt-1 pb-3 text-sm font-medium text-app-text-muted cursor-not-allowed select-none">
                                {{ __($item['label']) }}
                            </span>
                        @else
                            @php
                                $activePattern = $item['active'] ?? $item['route'];
                            @endphp

                            <x-nav-link :href="route($item['route'])" :active="request()->routeIs($activePattern)">
                                <span class="inline-flex items-center gap-2">
                                    {{ __($item['label']) }}
                                </span>
                            </x-nav-link>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>