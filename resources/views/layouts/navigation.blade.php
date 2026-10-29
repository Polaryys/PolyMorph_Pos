@php
    $compraPendiente = \App\Models\Compra::query()
        ->where('estado', 'PENDIENTE')
        ->exists();

    $module = match (true) {
        request()->routeIs('pos.*') => 'pos',
        request()->routeIs('inventario.*', 'articulos.*', 'marcas.*', 'categorias.*','compras.*') => 'inventario',
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
        'profile' => [['label' => 'Perfil', 'route' => 'profile.show', 'active' => 'profile.*']],
        'inventario' => [
            ['label' => 'Artículos', 'route' => 'articulos.index', 'active' => 'articulos.*'],
            ['label' => 'Presentaciones', 'route' => 'presentaciones.index', 'active' => 'presentaciones.*'],
            ['label' => 'Categorías', 'route' => 'categorias.index', 'active' => 'categorias.*'],
            ['label' => 'Marcas', 'route' => 'marcas.index', 'active' => 'marcas.*'],
            ['label' => 'Compras', 'route' => 'compras.index', 'active' => 'compras.*'],
            ['label' => 'Ajuste', 'route' => null, 'disabled' => true],
            ['label' => 'Movimientos', 'route' => null, 'disabled' => true],
        ],
        'clientes' => [['label' => 'Clientes', 'route' => 'clientes.index', 'active' => 'clientes.*']],
        'proveedores' => [['label' => 'Proveedores', 'route' => 'proveedores.index', 'active' => 'proveedores.*']],
        'usuarios' => [['label' => 'Colaboradores', 'route' => 'usuarios.index', 'active' => 'usuarios.*']],
        'medios' => [['label' => 'Medios', 'route' => 'imagenes.index', 'active' => 'imagenes.*']],
        'configuracion' => [
            ['label' => 'Metodos de Pago', 'route' => 'metodos-pago.index', 'active' => 'metodos-pago.*'],
        ],
    ];
@endphp

<div class="fixed top-0 left-0 right-0 md:left-56 z-20 bg-zinc-800 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex w-full">
                <!-- hamburguer -->
                <div class="flex items-center md:hidden shrink-0">
                    <button type="button" @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-gray-400 hover:text-gray-500 hover:bg-gray-100
                               focus:outline-none transition duration-150 ease-in-out"
                        :aria-expanded="open.toString()" aria-label="Abrir o cerrar menú">

                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />

                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex space-x-8 -my-px ms-2 items-end sm:ms-10 whitespace-nowrap overflow-x-auto no-scrollbar">
                    @foreach ($menus[$module] ?? [] as $item)
                        @if ($item['disabled'] ?? false)
                            <span class="inline-flex px-1 pt-1 pb-3 text-sm font-medium text-gray-400 cursor-not-allowed select-none">
                                {{ __($item['label']) }}
                            </span>
                        @else
                            @php
                                $activePattern = $item['active'] ?? $item['route'];
                            @endphp

                            <x-nav-link :href="route($item['route'])" :active="request()->routeIs($activePattern)">
                                <span class="inline-flex items-center gap-2">
                                    {{ __($item['label']) }}
                                    @if (($item['route'] ?? null) === 'compras.index' && $compraPendiente)
                                        <span aria-label="Hay una compra pendiente"
                                            title="Hay una compra pendiente"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs font-bold leading-none text-white">
                                            +
                                        </span>
                                    @endif
                                </span>
                            </x-nav-link>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
