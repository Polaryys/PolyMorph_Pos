<x-app-layout>
<div
    x-data="{
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
    }"
    class="space-y-4"
>
    {{-- Apariencia --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-app-text-primary">
            Apariencia
        </label>

        <select
            x-model="theme"
            @change="apply()"
            class="w-full rounded-md border border-app-border bg-app-surface
                   p-2 text-sm text-app-text-primary focus:border-app-border-focus
                   focus:ring-1 focus:ring-app-border-focus"
        >
            <option value="light">Claro</option>
            <option value="dark">Oscuro</option>
        </select>
    </div>

    {{-- Color de acento --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-app-text-primary">
            Color principal
        </label>

        <select
            x-model="accent"
            @change="apply()"
            class="w-full rounded-md border border-app-border bg-app-surface
                   p-2 text-sm text-app-text-primary focus:border-app-border-focus
                   focus:ring-1 focus:ring-app-border-focus"
        >
            <option value="indigo">Indigo</option>
            <option value="blue">Blue</option>
        </select>
    </div>
</div>
</x-app-layout>