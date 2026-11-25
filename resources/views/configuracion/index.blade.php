<x-app-layout>
    <div x-data="{
        theme: 'light',
        accent: 'indigo',
    
        init() {
            this.theme = localStorage.getItem('app-theme') || 'light';
            this.accent = localStorage.getItem('app-accent') || 'indigo';
    
            this.apply();
        },
    
        apply() {
            document.documentElement.dataset.theme = this.theme;
            document.documentElement.dataset.accent = this.accent;
    
            localStorage.setItem('app-theme', this.theme);
            localStorage.setItem('app-accent', this.accent);
        }
    }" class="space-y-8">
        {{-- Apariencia --}}
        <section class="space-y-3">
            <div>
                <h3 class="text-sm font-semibold text-app-text-primary">
                    Apariencia
                </h3>
                <p class="mt-1 text-sm text-app-text-secondary">
                    Selecciona el tema visual del sistema.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                {{-- Tema claro --}}
                <button type="button" @click="theme = 'light'; apply()" :aria-pressed="theme === 'light'"
                    @class([
                        'relative rounded-xl border p-3 text-left transition-all duration-200',
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' => false,
                        'border-app-border bg-app-surface hover:border-app-primary-400' => true,
                    ])
                    :class="theme === 'light'
                        ?
                        'border-app-primary-500! bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border hover:border-app-primary-400'">
                    <div class="mb-3 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                        <div class="flex h-7 items-center gap-1.5 border-b border-slate-200 bg-white px-2">
                            <span class="size-2 rounded-full bg-slate-300"></span>
                            <span class="size-2 rounded-full bg-slate-300"></span>
                            <span class="size-2 rounded-full bg-slate-300"></span>
                        </div>
                        <div class="flex h-20">
                            <div class="w-1/4 bg-slate-200 p-2">
                                <div class="mb-2 h-2 w-full rounded bg-slate-400"></div>
                                <div class="mb-1 h-1.5 w-3/4 rounded bg-slate-300"></div>
                                <div class="h-1.5 w-full rounded bg-slate-300"></div>
                            </div>
                            <div class="flex-1 space-y-2 bg-white p-2">
                                <div class="h-2 w-1/2 rounded bg-slate-300"></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="h-9 rounded border border-slate-200 bg-slate-50"></div>
                                    <div class="h-9 rounded border border-slate-200 bg-slate-50"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-app-text-primary">Claro</p>
                            <p class="text-xs text-app-text-muted">Superficies claras</p>
                        </div>

                        <span class="flex size-5 items-center justify-center rounded-full border transition-colors"
                            :class="theme === 'light'
                                ?
                                'border-app-primary-600 bg-app-primary-600 text-white' :
                                'border-app-border text-transparent'">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5">
                                <path fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.2 7.26a1 1 0 0 1-1.42.003l-3.8-3.8a1 1 0 0 1 1.414-1.414l3.09 3.09 6.494-6.548a1 1 0 0 1 1.416-.005Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </button>

                {{-- Tema oscuro --}}
                <button type="button" @click="theme = 'dark'; apply()" :aria-pressed="theme === 'dark'"
                    class="relative rounded-xl border p-3 text-left transition-all duration-200"
                    :class="theme === 'dark'
                        ?
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border bg-app-surface hover:border-app-primary-400'">
                    <div class="mb-3 overflow-hidden rounded-lg border border-zinc-700 bg-zinc-900">
                        <div class="flex h-7 items-center gap-1.5 border-b border-zinc-700 bg-zinc-800 px-2">
                            <span class="size-2 rounded-full bg-zinc-600"></span>
                            <span class="size-2 rounded-full bg-zinc-600"></span>
                            <span class="size-2 rounded-full bg-zinc-600"></span>
                        </div>
                        <div class="flex h-20">
                            <div class="w-1/4 bg-zinc-800 p-2">
                                <div class="mb-2 h-2 w-full rounded bg-zinc-600"></div>
                                <div class="mb-1 h-1.5 w-3/4 rounded bg-zinc-700"></div>
                                <div class="h-1.5 w-full rounded bg-zinc-700"></div>
                            </div>
                            <div class="flex-1 space-y-2 bg-zinc-900 p-2">
                                <div class="h-2 w-1/2 rounded bg-zinc-600"></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="h-9 rounded border border-zinc-700 bg-zinc-800"></div>
                                    <div class="h-9 rounded border border-zinc-700 bg-zinc-800"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-app-text-primary">Oscuro</p>
                            <p class="text-xs text-app-text-muted">Superficies oscuras</p>
                        </div>

                        <span class="flex size-5 items-center justify-center rounded-full border transition-colors"
                            :class="theme === 'dark'
                                ?
                                'border-app-primary-600 bg-app-primary-600 text-white' :
                                'border-app-border text-transparent'">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5">
                                <path fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.2 7.26a1 1 0 0 1-1.42.003l-3.8-3.8a1 1 0 0 1 1.414-1.414l3.09 3.09 6.494-6.548a1 1 0 0 1 1.416-.005Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </button>
            </div>
        </section>

        {{-- Color principal --}}
        <section class="space-y-3">
            <div>
                <h3 class="text-sm font-semibold text-app-text-primary">
                    Color principal
                </h3>
                <p class="mt-1 text-sm text-app-text-secondary">
                    Personaliza el color de acento utilizado en botones, enlaces y elementos destacados.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                {{-- Indigo --}}
                <button type="button" @click="accent = 'indigo'; apply()" :aria-pressed="accent === 'indigo'"
                    class="flex items-center justify-between gap-3 rounded-xl border p-4 text-left transition-all duration-200"
                    :class="accent === 'indigo'
                        ?
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border bg-app-surface hover:border-app-primary-400'">
                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-lg bg-indigo-600">
                            <span class="size-4 rounded-full border-2 border-white/80"></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-app-text-primary">Indigo</p>
                            <p class="text-xs text-app-text-muted">Elegante y equilibrado</p>
                        </div>
                    </div>

                    <span class="flex size-5 items-center justify-center rounded-full border transition-colors"
                        :class="accent === 'indigo'
                            ?
                            'border-app-primary-600 bg-app-primary-600 text-white' :
                            'border-app-border text-transparent'">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5">
                            <path fill-rule="evenodd"
                                d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.2 7.26a1 1 0 0 1-1.42.003l-3.8-3.8a1 1 0 0 1 1.414-1.414l3.09 3.09 6.494-6.548a1 1 0 0 1 1.416-.005Z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                {{-- Blue --}}
                <button type="button" @click="accent = 'blue'; apply()" :aria-pressed="accent === 'blue'"
                    class="flex items-center justify-between gap-3 rounded-xl border p-4 text-left transition-all duration-200"
                    :class="accent === 'blue'
                        ?
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border bg-app-surface hover:border-app-primary-400'">
                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-lg bg-blue-600">
                            <span class="size-4 rounded-full border-2 border-white/80"></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-app-text-primary">Azul</p>
                            <p class="text-xs text-app-text-muted">Moderno y profesional</p>
                        </div>
                    </div>

                    <span class="flex size-5 items-center justify-center rounded-full border transition-colors"
                        :class="accent === 'blue'
                            ?
                            'border-app-primary-600 bg-app-primary-600 text-white' :
                            'border-app-border text-transparent'">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5">
                            <path fill-rule="evenodd"
                                d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.2 7.26a1 1 0 0 1-1.42.003l-3.8-3.8a1 1 0 0 1 1.414-1.414l3.09 3.09 6.494-6.548a1 1 0 0 1 1.416-.005Z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>
            </div>
        </section>
    </div>
</x-app-layout>
