@php

    $modulos = [
        'Inicio' => ['dashboard', 'inicio'],
        'Punto de venta' => ['ventas.*', 'pos.*'],
        'Inventario' => ['articulos.*','presentaciones.*', 'categorias.*','marcas.*', 'compras.*','ajuste.*', 'movimientos.*'],
        'Clientes' => ['clientes.*'],
        'Proveedores' => ['proveedores.*', 'proveedore.*'],
        'Reportes' => ['reportes.*'],
        'Usuarios' => ['usuarios.*'],
        'Medios' => ['imagenes.*'],
        'Configuración' => ['metodos-pago.*', 'configuracion.*'],
        'Mi perfil' => ['profile.*'],
    ];

    $tituloModulo = 'Inicio';




    foreach ($modulos as $titulo => $patrones) {
        if (request()->routeIs(...$patrones)) {
            $tituloModulo = $titulo;
            break;
        }
    }

@endphp

<nav class="fixed top-0 left-0 right-0 md:left-56 md:right-0 z-50 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-12 gap-3">

            <!-- amberguer -->
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

                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex items-end gap-3 min-w-0 self-stretch">
                <h1 class=" flex items-end text-3xl font-semibold text-gray-800 whitespace-nowrap">
                    {{ $tituloModulo }}
                </h1>
            </div>
        </div>
    </div>
</nav>
