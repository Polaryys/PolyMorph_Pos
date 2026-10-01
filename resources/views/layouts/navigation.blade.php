@php

    $module = match (true) {
        request()->routeIs('ficha-infantes.*') => 'infantes',
        request()->routeIs('control-infantes.*', 'inventario.*') => 'inventario',
        request()->routeIs('profile.*') => 'profile',
        default => 'dashboard',
    };

    $menus = [
        'dashboard' => [['label' => 'Inicio', 'route' => 'dashboard', 'active' => 'dashboard']],
        'profile' => [
            ['label' => 'Perfil', 'route' => 'profile.edit', 'active' => 'profile.*'],
            ['label' => 'Contraseña', 'route' => null, 'disabled' => true],
        ],
        'infantes' => [
            ['label' => 'Ficha Infantes', 'route' => 'ficha-infantes.index', 'active' => 'ficha-infantes.*'],
            ['label' => 'Ventas', 'route' => null, 'disabled' => true],
        ],
        'inventario' => [
            ['label' => 'Control Infantes', 'route' => 'control-infantes.index', 'active' => 'control-infantes.*'],
            ['label' => 'Categorías', 'route' => null, 'disabled' => true],
        ],
    ];
@endphp

<nav x-data="{ open: false }" class="fixed top-12 left-0 right-0 md:left-56 z-30 bg-white border-b border-gray-100">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex w-full">
                <!-- Agregado: overflow-x-auto para scroll suave en móviles -->
                <div class="flex space-x-8 -my-px ms-2 sm:ms-10 whitespace-nowrap overflow-x-auto no-scrollbar">
                    @foreach ($menus[$module] ?? [] as $item)
                        @if ($item['disabled'] ?? false)
                            <span
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-400 cursor-not-allowed select-none">
                                {{ __($item['label']) }}
                            </span>
                        @else
                            {{-- Se evalúa 'active' si existe; de lo contrario usa 'route' --}}
                            @php
                                $activePattern = $item['active'] ?? $item['route'];
                            @endphp

                            <x-nav-link :href="route($item['route'])" :active="request()->routeIs($activePattern)">
                                {{ __($item['label']) }}
                            </x-nav-link>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</nav>
