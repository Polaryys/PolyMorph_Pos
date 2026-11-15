<aside
    class="fixed inset-y-0 left-0 z-40 flex w-56 flex-col
           overflow-y-auto border-r border-border bg-surface
           transform transition-transform duration-300 ease-in-out
           -translate-x-full md:translate-x-0"
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }">

    {{-- Encabezado --}}
    <div class="flex h-32 shrink-0 flex-col items-center justify-center
                 border-border px-4">

        <x-icon-sistema class="h-24 w-auto shrink-0 text-primary drop-shadow-sm" />

        <p class="text-sm font-semibold tracking-wide text-text-primary">
            MultiServicios Eben-Ezer
        </p>

    </div>
    {{-- Separador --}}
    <div class="py-">
        <hr class="border-border mx-4">
    </div>

    {{-- Navegación --}}
    <div class="flex min-h-0 flex-1 flex-col px-4 py-4">


        <nav class="flex-1 space-y-2 overflow-y-auto">

            {{-- Sección principal --}}
            <p class="px-3 mb-2 text-xs font-semibold
                      tracking-wider text-text-primary uppercase">
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
                <hr class="border-border">
            </div>

            <p class="px-3 mb-3 text-xs font-semibold
                      tracking-wider text-text-primary uppercase">
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
        <div class="shrink-0 space-y-2 border-t border-border pt-2 mt-2">

            @php
                $usuarioActual = Auth::user();
                $perfilActivo = request()->routeIs('profile.*');
            @endphp

            {{-- Perfil --}}
            <a href="{{ route('profile.show') }}" wire:navigate @class([
                'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-colors',
                'bg-primary text-text-on-primary' => $perfilActivo,
                'text-text-secondary hover:bg-surface-hover hover:text-text-primary' => !$perfilActivo,
            ])>

                <div class="shrink-0">
                    @if ($usuarioActual->imagen_id && $usuarioActual->imagen)
                        <img src="{{ asset('storage/imagenes/' . $usuarioActual->imagen->nombre_archivo) }}"
                            alt="Foto de perfil" class="h-9 w-9 rounded-full border border-border object-cover">
                    @else
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-semibold text-white shadow-sm">
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
                        'text-text-on-primary/80' => $perfilActivo,
                        'text-text-muted' => !$perfilActivo,
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
                           text-red-500 transition-colors
                           hover:bg-red-500/80 hover:text-white
                           focus-visible:outline-none
                           focus-visible:ring-2 focus-visible:ring-red-500">

                    <x-lucide-log-out class="h-5 w-5 shrink-0" />

                    <span>Cerrar sesión</span>

                </button>
            </form>

        </div>

    </div>

</aside>

{{-- Fondo oscuro en móvil --}}
<div x-cloak x-show="open" @click="open = false" x-transition.opacity
    class="fixed inset-0 z-30 bg-inverse/60
           backdrop-blur-sm md:hidden">
</div>
