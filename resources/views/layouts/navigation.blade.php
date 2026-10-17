@php

    $module = match (true) {
        request()->routeIs('pos.*') => 'pos',
        request()->routeIs('inventario.*','articulos.*', 'marcas.*', 'categorias.*') => 'inventario',
        request()->routeIs('clientes.*') => 'clientes',
        request()->routeIs('proveedores.*') => 'proveedores',
        request()->routeIs('profile.*') => 'profile',
        request()->routeIs('usuarios.*') => 'usuarios',
        request()->routeIs('imagenes.*') => 'medios',
        request()->routeIs('configuracion.*', 'metodos-pago.*') => 'configuracion',
        default => 'inventario',
    };

    $menus = [
        'dashboard' => [['label' => 'Inicio', 'route' => 'dashboard', 'active' => 'dashboard']],
        'profile' => [
            ['label' => 'Perfil', 'route' => 'profile.show', 'active' => 'profile.*'],
        ],
        'inventario' => [
            ['label' => 'Artículos', 'route' => 'articulos.index', 'active' => 'articulos.*'],
            ['label' => 'Presentaciones', 'route' => null, 'disabled' => true],
            ['label' => 'Categorías', 'route' => 'categorias.index', 'active' => 'categorias.*'],
            ['label' => 'Marcas', 'route' => 'marcas.index', 'active' => 'marcas.*'],
            ['label' => 'Compras', 'route' => null, 'disabled' => true],
            ['label' => 'Ajuste', 'route' => null, 'disabled' => true],
            ['label' => 'Movimientos', 'route' => null, 'disabled' => true]
        ],
        'clientes' => [
            ['label' => 'Clientes', 'route' => 'clientes.index', 'active' => 'clientes.*']
        ],
        'proveedores' => [
            ['label' => 'Proveedores', 'route' => 'proveedores.index', 'active' => 'proveedores.*']
        ],
        'usuarios' => [
            ['label' => 'Colaboradores', 'route' => 'usuarios.index', 'active' => 'usuarios.*']
        ],
        'medios' => [
            ['label' => 'Imagenes', 'route' => 'imagenes.index', 'active' => 'imagenes.*']
        ],
        'configuracion' => [
            ['label' => 'Metodos de Pago', 'route' => 'metodos-pago.index', 'active' => 'metodos-pago.*']
        ]

    ];
@endphp

<div class="fixed top-12 left-0 right-0 md:left-56 z-30 bg-white border-b border-gray-200">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-12">
            <div class="flex w-full">

                <div class="flex space-x-8 -my-px ms-2 sm:ms-10 whitespace-nowrap overflow-x-auto no-scrollbar">
                    @foreach ($menus[$module] ?? [] as $item)
                        @if ($item['disabled'] ?? false)
                            <span
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-400 cursor-not-allowed select-none">
                                {{ __($item['label']) }}
                            </span>
                        @else
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
