@php
    if (request()->routeIs('ficha-infantes.*')) {
        $module = 'infantes';
    } elseif (request()->routeIs('control-infantes.*')) {
        $module = 'inventario';
    } elseif (request()->routeIs('inventario.*')) {
        $module = 'inventario';
    } elseif (request()->routeIs('profile.*')) {
        $module = 'profile';
    } else {
        $module = 'dashboard';
    }

    $menus = [
        'dashboard' => [['label' => 'Inicio', 'route' => 'dashboard']],
        'profile' => [
            [
                'label' => 'Perfil',
                'route' => 'profile.edit',
                'active' => 'profile.edit',
            ],
            [
                'label' => 'Contraseña',
                'route' => 'profile.password',
                'disabled' => true,
            ],
        ],
        'infantes' => [
            ['label' => 'Ficha Infantes', 'route' => 'ficha-infantes.index'],
            ['label' => 'Ventas', 'route' => null, 'disabled' => true],
        ],

        'inventario' => [
            ['label' => 'Control Infantes', 'route' => 'control-infantes.index'],
            ['label' => 'Categorías', 'route' => null, 'disabled' => true],
        ],
    ];
@endphp


<nav x-data="{ open: false }"
    class="fixed top-12 left-0 right-0 md:left-56 md:right-0 z-30 bg-white border-b border-gray-100">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            <div class="flex">

                <!-- Navigation Links -->
                <div class="flex space-x-8 -my-px ms-2 sm:ms-10 whitespace-nowrap">

                    @foreach ($menus[$module] as $item)
                        @if ($item['disabled'] ?? false)
                            <span
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-400 cursor-not-allowed">
                                {{ __($item['label']) }}
                            </span>
                        @else
                            <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])">

                                {{ __($item['label']) }}

                            </x-nav-link>
                        @endif
                    @endforeach

                </div>

            </div>

        </div>

    </div>

</nav>
