<aside
    class="fixed inset-y-0 left-0 z-40 flex w-56 flex-col
           border-r border-slate-200 bg-white
           transform transition-transform duration-300 ease-in-out
           -translate-x-full md:translate-x-0"
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }">

    {{-- Encabezado --}}
    <div
        class="flex h-32 shrink-0 flex-col items-center justify-center
                gap-1 border-b border-slate-200 px-4">

        <x-icon-sistema class="h-20 w-auto shrink-0 text-indigo-500 drop-shadow-sm" />

        <p class="text-sm font-bold tracking-wide text-slate-700">
            MultiServicios Eben-Ezer
        </p>

    </div>

    {{-- Navegación --}}
    <div class="flex min-h-0 flex-1 flex-col p-3">

        <nav class="flex-1 space-y-1.5 overflow-y-auto">

            <x-sidebar-link href="#" wire:navigate>
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

            <x-sidebar-link href="#" wire:navigate>
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

            <x-sidebar-link :href="route('metodos-pago.index')" :active="request()->routeIs('metodos-pago.*')" wire:navigate>

                <x-slot:icon>
                    <x-lucide-settings class="h-5 w-5" />
                </x-slot:icon>

                Configuración
            </x-sidebar-link>

        </nav>

        {{-- Zona inferior --}}
        <div class="shrink-0 space-y-1 border-t border-slate-200 pt-3">

            @php
                $usuarioActual = Auth::user();
                $perfilActivo = request()->routeIs('profile.*');
            @endphp

            {{-- Perfil --}}
            <a href="{{ route('profile.show') }}" wire:navigate @class([
                'flex items-center gap-3 rounded-xl p-2.5 transition-colors',
                'bg-indigo-50 text-indigo-700' => $perfilActivo,
                'text-slate-600 hover:bg-slate-100 hover:text-slate-800' => !$perfilActivo,
            ])>

                <div class="shrink-0">

                    @if ($usuarioActual->imagen_id && $usuarioActual->imagen)
                        <img src="{{ asset('storage/imagenes/' . $usuarioActual->imagen->nombre_archivo) }}"
                            alt="Foto de perfil" class="h-9 w-9 rounded-full border border-slate-200 object-cover">
                    @else
                        <div
                            class="flex h-9 w-9 items-center justify-center
                                    rounded-full bg-indigo-600 text-xs font-semibold
                                    text-white shadow-sm">

                            {{ strtoupper(substr($usuarioActual->nombres, 0, 1) . substr($usuarioActual->apellidos, 0, 1)) }}

                        </div>
                    @endif

                </div>

                <div class="min-w-0 flex-1">

                    <p class="truncate text-sm font-semibold leading-tight">
                        {{ $usuarioActual->username }}
                    </p>

                    <p class="mt-0.5 truncate text-xs text-slate-500">
                        {{ $usuarioActual->rol }}
                    </p>

                </div>

                <x-lucide-chevron-right class="h-4 w-4 shrink-0 text-slate-400" />

            </a>

            {{-- Cerrar sesión --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="flex w-full items-center gap-3 rounded-lg
                           px-3 py-2.5 text-sm font-medium
                           text-red-600 transition-colors
                           hover:bg-red-50 hover:text-red-700
                           focus-visible:outline-none
                           focus-visible:ring-2 focus-visible:ring-red-400">

                    <x-lucide-log-out class="h-5 w-5 shrink-0" />

                    <span>Cerrar sesión</span>

                </button>
            </form>

        </div>

    </div>

</aside>

{{-- Fondo oscuro en móvil --}}
<div x-cloak x-show="open" @click="open = false" x-transition.opacity
    class="fixed inset-0 z-30 bg-slate-900/40
           backdrop-blur-sm md:hidden">
</div>
