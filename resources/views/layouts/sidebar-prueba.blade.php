
<aside
    class="fixed inset-y-0 left-0 z-40 flex w-56 flex-col
           overflow-y-auto border-r border-app-border bg-app-surface
           transform transition-transform duration-300 ease-in-out
           -translate-x-full md:translate-x-0"
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }">

    {{-- Encabezado --}}
    <div class="flex h-32 shrink-0 flex-col items-center justify-center
                 px-4">

        <x-icon-sistema class="h-24 w-auto shrink-0 text-app-primary-600 drop-shadow-sm" />

        <p class="text-sm font-semibold tracking-wide text-app-text-primary">
            EBEN-EZER
        </p>

    </div>

    {{-- Separador --}}
    <div class="py-1">
        <hr class="mx-4 border-app-border">
    </div>

    {{-- Navegación --}}
    <div class="flex min-h-0 flex-1 flex-col px-4 py-4">

        <nav class="flex-1 space-y-2 overflow-y-auto">

            {{-- Sección principal --}}
            <p class="mb-2 px-3 text-xs font-semibold
                      tracking-wider text-app-text-muted uppercase">
                General
            </p>

            <x-sidebar-link :href="route('ventas.create')" :active="request()->routeIs('ventas.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-shopping-cart class="h-5 w-5" />
                </x-slot:icon>

                Punto de venta
            </x-sidebar-link>

            <x-sidebar-link :href="route('articulos.index')" :active="request()->routeIs(
                'articulos.*',
                'presentaciones.*',
                'categorias.*',
                'marcas.*',
                'compras.*',
                'ajuste.*',
                'movimientos.*',
            )" wire:navigate>

                <x-slot:icon>
                    <x-lucide-box class="h-5 w-5" />
                </x-slot:icon>

                Inventario
            </x-sidebar-link>

            <x-sidebar-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-users class="h-5 w-5" />
                </x-slot:icon>

                Clientes
            </x-sidebar-link>

            <x-sidebar-link :href="route('proveedores.index')" :active="request()->routeIs('proveedores.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-truck class="h-5 w-5" />
                </x-slot:icon>

                Proveedores
            </x-sidebar-link>

            {{-- Separador --}}
            <div class="py-2">
                <hr class="border-app-border">
            </div>

            {{-- Sección de administración --}}
            <p class="mb-3 px-3 text-xs font-semibold
                      tracking-wider text-app-text-muted uppercase">
                Administración
            </p>

            <x-sidebar-link :href="route('reportes.inventario')" :active="request()->routeIs('reportes.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-file-text class="h-5 w-5" />
                </x-slot:icon>

                Reportes
            </x-sidebar-link>

            <x-sidebar-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-user class="h-5 w-5" />
                </x-slot:icon>

                Usuarios
            </x-sidebar-link>

            <x-sidebar-link :href="route('imagenes.index')" :active="request()->routeIs('imagenes.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-image class="h-5 w-5" />
                </x-slot:icon>

                Medios
            </x-sidebar-link>

            <x-sidebar-link :href="route('metodos-pago.index')" :active="request()->routeIs('metodos-pago.*', 'configuracion.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-settings class="h-5 w-5" />
                </x-slot:icon>

                Configuración
            </x-sidebar-link>

        </nav>

        {{-- Zona inferior --}}
        <div class="mt-2 shrink-0 space-y-2 border-t border-app-border pt-2">

            @php
                $usuarioActual = Auth::user();
                $perfilActivo = request()->routeIs('profile.*');
            @endphp

            {{-- Perfil --}}
            <a href="{{ route('profile.show') }}" wire:navigate @class([
                'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-colors duration-200',
                'bg-app-primary-600 text-white' => $perfilActivo,
                'text-app-text-secondary hover:bg-app-surface-hover hover:text-app-text-primary' => !$perfilActivo,
            ])>

                <div class="shrink-0">
                    @if ($usuarioActual->imagen_id && $usuarioActual->imagen)
                        <img src="{{ asset('storage/imagenes/' . $usuarioActual->imagen->nombre_archivo) }}"
                            alt="Foto de perfil"
                            class="h-9 w-9 rounded-full border border-app-border object-cover">
                    @else
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-app-primary-600 text-xs font-semibold text-white shadow-sm">
                            {{ strtoupper(substr($usuarioActual->nombres, 0, 1) . substr($usuarioActual->apellidos, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold leading-tight">
                        {{ $usuarioActual->username }}
                    </p>

                    <p @class([
                        'mt-0.5 truncate text-xs',
                        'text-white/80' => $perfilActivo,
                        'text-app-text-muted' => !$perfilActivo,
                    ])>
                        {{ $usuarioActual->rol }}
                    </p>
                </div>

                <x-lucide-chevron-right class="h-4 w-4 shrink-0 opacity-60" />

            </a>

            {{-- Cerrar sesión --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="flex w-full items-center gap-3 rounded-lg
                           px-3 py-2.5 text-sm font-medium
                           text-app-text-secondary transition-colors duration-200
                           hover:bg-red-500/10 hover:text-red-500
                           active:bg-red-500/20 active:text-red-600
                           focus-visible:outline-none
                           focus-visible:ring-2 focus-visible:ring-red-500/50">

                    <x-lucide-log-out class="h-5 w-5 shrink-0" />

                    <span>Cerrar sesión</span>

                </button>
            </form>

        </div>

    </div>

</aside>

{{-- Fondo oscuro en móvil --}}
<div x-cloak x-show="open" @click="open = false" x-transition.opacity
    class="fixed inset-0 z-30 bg-slate-950/60
           backdrop-blur-sm md:hidden">
</div>
