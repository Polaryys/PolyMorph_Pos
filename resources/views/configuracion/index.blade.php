<x-app-layout>
    <div x-data="{
        theme: 'light',
        accent: 'indigo',
        font: 'figtree',
    
        init() {
            const temas = ['light', 'dim', 'dark'];
            const acentos = ['indigo', 'blue', 'teal'];
            const fuentes = ['figtree', 'inter', 'roboto', 'nunito'];
    
            const temaGuardado = localStorage.getItem('app-theme');
            const acentoGuardado = localStorage.getItem('app-accent');
            const fuenteGuardada = localStorage.getItem('app-font');
    
            this.theme = temas.includes(temaGuardado) ? temaGuardado : 'light';
            this.accent = acentos.includes(acentoGuardado) ? acentoGuardado : 'indigo';
            this.font = fuentes.includes(fuenteGuardada) ? fuenteGuardada : 'figtree';
            this.apply();
        },
    
        apply() {
            document.documentElement.dataset.theme = this.theme;
            document.documentElement.dataset.accent = this.accent;
            document.documentElement.dataset.font = this.font;
    
            localStorage.setItem('app-theme', this.theme);
            localStorage.setItem('app-accent', this.accent);
            localStorage.setItem('app-font', this.font);
        }
    }" class="space-y-8">
        {{-- Apariencia --}}
        <section class="space-y-3">
            <div>
                <h3 class="text-sm font-semibold text-app-text-primary">
                    Temas
                </h3>
                <p class="mt-1 text-sm text-app-text-secondary">
                    Selecciona el tema visual del sistema.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                {{-- Tema claro --}}
                <button type="button" @click="theme = 'light'; apply()" :aria-pressed="theme === 'light'"
                    class="relative rounded-xl border p-3 text-left transition-all duration-200"
                    :class="theme === 'light'
                        ?
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border bg-app-surface hover:border-app-primary-400'">

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
                            <x-lucide-check class="size-3.5" />
                        </span>
                    </div>
                </button>

                {{-- Tema Crepúsculo --}}
                <button type="button" @click="theme = 'dim'; apply()" :aria-pressed="theme === 'dim'"
                    class="relative rounded-xl border p-3 text-left transition-all duration-200"
                    :class="theme === 'dim'
                        ?
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border bg-app-surface hover:border-app-primary-400'">

                    <div class="mb-3 overflow-hidden rounded-lg border border-slate-300 bg-slate-200">
                        <div class="flex h-7 items-center gap-1.5 border-b border-slate-300 bg-slate-100 px-2">
                            <span class="size-2 rounded-full bg-slate-400"></span>
                            <span class="size-2 rounded-full bg-slate-400"></span>
                            <span class="size-2 rounded-full bg-slate-400"></span>
                        </div>

                        <div class="flex h-20">
                            <div class="w-1/4 bg-slate-300 p-2">
                                <div class="mb-2 h-2 w-full rounded bg-slate-500"></div>
                                <div class="mb-1 h-1.5 w-3/4 rounded bg-slate-400"></div>
                                <div class="h-1.5 w-full rounded bg-slate-400"></div>
                            </div>

                            <div class="flex-1 space-y-2 bg-slate-100 p-2">
                                <div class="h-2 w-1/2 rounded bg-slate-400"></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="h-9 rounded border border-slate-300 bg-white"></div>
                                    <div class="h-9 rounded border border-slate-300 bg-white"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-app-text-primary">Crepúsculo</p>
                            <p class="text-xs text-app-text-muted">Equilibrio visual</p>
                        </div>

                        <span class="flex size-5 items-center justify-center rounded-full border transition-colors"
                            :class="theme === 'dim'
                                ?
                                'border-app-primary-600 bg-app-primary-600 text-white' :
                                'border-app-border text-transparent'">
                            <x-lucide-check class="size-3.5" />
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
                            <x-lucide-check class="size-3.5" />
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

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

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
                        <x-lucide-check class="size-3.5" />
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
                        <x-lucide-check class="size-3.5" />
                    </span>
                </button>

                {{-- Teal --}}
                <button type="button" @click="accent = 'teal'; apply()" :aria-pressed="accent === 'teal'"
                    class="flex items-center justify-between gap-3 rounded-xl border p-4 text-left transition-all duration-200"
                    :class="accent === 'teal'
                        ?
                        'border-app-primary-500 bg-app-primary-500/5 ring-1 ring-app-primary-500' :
                        'border-app-border bg-app-surface hover:border-app-primary-400'">

                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-lg bg-teal-600">
                            <span class="size-4 rounded-full border-2 border-white/80"></span>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-app-text-primary">Teal</p>
                            <p class="text-xs text-app-text-muted">Fresco y contemporáneo</p>
                        </div>
                    </div>

                    <span class="flex size-5 items-center justify-center rounded-full border transition-colors"
                        :class="accent === 'teal'
                            ?
                            'border-app-primary-600 bg-app-primary-600 text-white' :
                            'border-app-border text-transparent'">
                        <x-lucide-check class="size-3.5" />
                    </span>
                </button>

            </div>
        </section>

        {{-- selector de fuentes --}}
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-app-text-primary">
                    Tipografía
                </h3>
                <p class="mt-1 text-sm text-app-text-secondary">
                    Selecciona la fuente utilizada en la interfaz del sistema.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

                {{-- Figtree --}}
                <button type="button" @click="font = 'figtree'; apply()"
                    class="relative flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition
                hover:bg-app-surface-hover"
                    :class="font === 'figtree'
                        ?
                        'border-app-primary-500 bg-app-primary-100/40 dark:bg-app-primary-900/20' :
                        'border-app-border bg-app-surface'">

                    <span class="text-3xl font-medium" style="font-family: 'Figtree', sans-serif;">
                        AaBbCc
                    </span>

                    <span class="text-sm font-medium text-app-text-primary">
                        Figtree
                    </span>

                    <template x-if="font === 'figtree'">
                        <x-lucide-check-circle
                            class="absolute right-2 top-2 size-4 text-app-primary-600 dark:text-app-primary-400" />
                    </template>
                </button>

                {{-- Inter --}}
                <button type="button" @click="font = 'inter'; apply()"
                    class="relative flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition
                hover:bg-app-surface-hover"
                    :class="font === 'inter'
                        ?
                        'border-app-primary-500 bg-app-primary-100/40 dark:bg-app-primary-900/20' :
                        'border-app-border bg-app-surface'">

                    <span class="text-3xl font-medium" style="font-family: 'Inter', sans-serif;">
                        AaBbCc
                    </span>

                    <span class="text-sm font-medium text-app-text-primary">
                        Inter
                    </span>

                    <template x-if="font === 'inter'">
                        <x-lucide-check-circle
                            class="absolute right-2 top-2 size-4 text-app-primary-600 dark:text-app-primary-400" />
                    </template>
                </button>

                {{-- Roboto --}}
                <button type="button" @click="font = 'roboto'; apply()"
                    class="relative flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition
                hover:bg-app-surface-hover"
                    :class="font === 'roboto'
                        ?
                        'border-app-primary-500 bg-app-primary-100/40 dark:bg-app-primary-900/20' :
                        'border-app-border bg-app-surface'">

                    <span class="text-3xl font-medium" style="font-family: 'Roboto', sans-serif;">
                        AaBbCc
                    </span>

                    <span class="text-sm font-medium text-app-text-primary">
                        Roboto
                    </span>

                    <template x-if="font === 'roboto'">
                        <x-lucide-check-circle
                            class="absolute right-2 top-2 size-4 text-app-primary-600 dark:text-app-primary-400" />
                    </template>
                </button>

                {{-- Nunito Sans --}}
                <button type="button" @click="font = 'nunito'; apply()"
                    class="relative flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition
                hover:bg-app-surface-hover"
                    :class="font === 'nunito'
                        ?
                        'border-app-primary-500 bg-app-primary-100/40 dark:bg-app-primary-900/20' :
                        'border-app-border bg-app-surface'">

                    <span class="text-3xl font-medium" style="font-family: 'Nunito Sans', sans-serif;">
                        AaBbCc
                    </span>

                    <span class="text-sm font-medium text-app-text-primary">
                        Nunito Sans
                    </span>

                    <template x-if="font === 'nunito'">
                        <x-lucide-check-circle
                            class="absolute right-2 top-2 size-4 text-app-primary-600 dark:text-app-primary-400" />
                    </template>
                </button>

            </div>
        </div>

    </div>
</x-app-layout>
