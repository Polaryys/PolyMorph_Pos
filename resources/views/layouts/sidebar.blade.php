<aside :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
    class="fixed inset-y-0 left-0 w-60 z-50 bg-white border-r border-gray-200 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0">

    <div class="flex justify-center items-center h-32 px-4 pt-16 pb-12 border-b border-gray-100">
        <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema" class="h-24 w-auto object-contain drop-shadow-sm">
    </div>

    <!-- sidebar principal -->
    <div class="p-4 flex-1 flex flex-col justify-between overflow-y-auto">

        <nav class="space-y-1.5">
            <x-sidebar-link :href="route('ficha-infantes.index')" :active="request()->routeIs('ficha-infantes.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-shopping-cart class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Punto de venta
            </x-sidebar-link>

            <x-sidebar-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')" wire:navigate>
                <x-slot:icon>
                    <x-lucide-box class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Inventario
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

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-settings class="w-5 h-5 shrink-0" />
                </x-slot:icon>
                Configuración
            </x-sidebar-link>
        </nav>
        <div class="pt-4 border-t border-gray-100 space-y-1">
 
            <!-- user -->
            <a href="{{ route('profile.show') }}" wire:navigate @class([
                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-indigo-50 text-indigo-700' => request()->routeIs('profile.*'),
                'text-gray-600 hover:text-gray-900 hover:bg-gray-100' => !request()->routeIs(
                    'profile.*'),
            ])>
                <div
                    class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 font-semibold text-xs">
                    {{ strtoupper(substr(Auth::user()->nombres, 0, 1) . substr(Auth::user()->apellidos, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold leading-tight">
                        {{ Auth::user()->username }}
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ Auth::user()->rol }}
                    </p>
                </div>

            </a>

            <!-- cierre de sesión -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                    <x-lucide-log-out class="w-5 h-5 shrink-0 text-red-500" />
                    <span>Cerrar sesión</span>
                </button>
            </form>

        </div>

    </div>
</aside>

<!-- Capa oscura móvil -->
<div x-show="open" @click="open = false" x-transition.opacity
    class="fixed inset-0 z-40 bg-gray-900/40 backdrop-blur-sm md:hidden"></div>
