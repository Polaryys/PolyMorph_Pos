@php
    $compraPendiente = count(session('compras.borrador.detalles', [])) > 0;
@endphp

<aside
    class="fixed inset-y-0 left-0 w-56 z-40 bg-zinc-900 
           flex flex-col transform transition-transform duration-300 ease-in-out
           -translate-x-full md:translate-x-0"
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }">

    <div class="relative flex justify-center items-center h-32 px-4 py-4">

        <x-icon-sistema class="h-24 w-auto shrink-0 text-indigo-500 drop-shadow-sm transition-colors" />

        <div class="absolute bottom-0 left-4 right-4 h-[1px] bg-slate-800"></div>
    </div>

    <!-- sidebar principal -->
    <div class="p-3 flex-1 flex flex-col justify-between overflow-y-auto">

        <nav class="space-y-1.5">
            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-shopping-cart class="w-5 h-5 shrink-0" />
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
                    <x-lucide-box class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                <span class="flex flex-1 items-center justify-between">
                    Inventario
                    @if ($compraPendiente)
                        <span aria-label="Hay una compra pendiente"
                            title="Hay una compra pendiente"
                            class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs font-bold leading-none text-white">
                            +
                        </span>
                    @endif
                </span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-users class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Clientes
            </x-sidebar-link>

            <x-sidebar-link :href="route('proveedores.index')" :active="request()->routeIs('proveedores.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-truck class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Proveedores
            </x-sidebar-link>

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-file-text class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Reportes
            </x-sidebar-link>

            <x-sidebar-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-users class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Usuarios
            </x-sidebar-link>

            <x-sidebar-link :href="route('imagenes.index')" :active="request()->routeIs('imagenes.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-image class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Medios
            </x-sidebar-link>

            <x-sidebar-link :href="route('metodos-pago.index')" :active="request()->routeIs('metodos-pago.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-settings class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Configuración
            </x-sidebar-link>
        </nav>
        <div class="pt-3 border-t border-gray-800 space-y-1">

            <!-- user -->
            <a href="{{ route('profile.show') }}" wire:navigate @class([
                'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all',
                'bg-indigo-600 text-white shadow-sm' => request()->routeIs('profile.*'),
                'text-slate-300 hover:bg-slate-800/60 hover:text-white' => !request()->routeIs(
                    'profile.*'),
            ])>
                @php
                    $usuarioActual = Auth::user();
                @endphp

                <div class="shrink-0">
                    @if ($usuarioActual->imagen_id && $usuarioActual->imagen)
                        <img src="{{ asset('storage/imagenes/' . $usuarioActual->imagen->nombre_archivo) }}"
                            alt="Foto de perfil" class="w-9 h-9 rounded-full object-cover border border-slate-700">
                    @else
                        <div
                            class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                            {{ strtoupper(substr($usuarioActual->nombres, 0, 1) . substr($usuarioActual->apellidos, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p
                        class="truncate font-semibold leading-tight text-base {{ request()->routeIs('profile.*') ? 'text-white' : 'text-slate-200' }}">
                        {{ Auth::user()->username }}
                    </p>
                    <p class="text-xs text-slate-400 truncate mt-0.5">
                        {{ Auth::user()->rol }}
                    </p>
                </div>
            </a>

            <!-- cierre de sesión -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                    <x-lucide-log-out class="w-5 h-5 shrink-0 text-red-500" />
                    <span>Cerrar sesión</span>
                </button>
            </form>

        </div>

    </div>
</aside>

<!-- Fondo oscuro en móvil -->
<div x-cloak x-show="open" @click="open = false" x-transition.opacity
    class="fixed inset-0 z-30 bg-gray-900/40 backdrop-blur-sm md:hidden">
</div>
