<aside
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
    class="fixed inset-y-0 left-0 w-56 z-50 bg-white border-r flex flex-col
           transform -translate-x-full transition-transform duration-300 ease-in-out
           md:translate-x-0">

    <!-- Logo eben ezer -->
    <div class="flex justify-center items-center h-28">
        <img src="{{ asset('icon_sistema.png') }}" alt="Logo Sistema" class="h-24 w-auto object-contain drop-shadow-md">
    </div>

    <!-- Botones de navegacion de sidebar -->
    <div class="p-4 flex-1 flex flex-col">

        <nav class="space-y-1">

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-shopping-cart class="w-5 h-5 shrink-0" />
                </x-slot:icon>

                Punto de venta
            </x-sidebar-link>

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-users class="w-5 h-5 shrink-0" />
                </x-slot:icon>

                Usuarios
            </x-sidebar-link>
            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-box class="w-5 h-5 shrink-0" />
                </x-slot:icon>

                Inventario
            </x-sidebar-link>

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-users class="w-5 h-5 shrink-0" />
                </x-slot:icon>

                Clientes
            </x-sidebar-link>

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-truck class="w-5 h-5 shrink-0" />
                </x-slot:icon>

                Proveedores
            </x-sidebar-link>

            <x-sidebar-link href="#" wire:navigate>
                <x-slot:icon>
                    <x-lucide-file-text class="w-5 h-5 shrink-0" />
                </x-slot:icon>

                Reporte
            </x-sidebar-link>

            <x-sidebar-link href="#" wire:navigate>
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
            </a>

        </nav>
        <div class="mt-auto space-y-1">

            <!-- usuario -->
            <a href="{{ route('profile.edit') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100">

                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                    <x-lucide-user-round class="w-4 h-4 text-gray-600" />
                </div>

                <div class="min-w-0">
                    <p class="truncate">
                        {{ Auth::user()->name }}
                    </p>

                    <p class="text-xs text-gray-400 truncate">
                        Perfil
                    </p>
                </div>
            </a>

            <!-- cerrar sesion -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100">

                    <x-lucide-log-out class="w-5 h-5 shrink-0" />

                    <span>Cerrar sesión</span>
                </button>
            </form>

        </div>

    </div>
</aside>

<!-- capa oscuro de movil -->
<div x-show="open" @click="open = false" x-transition.opcity class="fixed inset-0 z-40 bg-black/40 md:hidden"></div>
