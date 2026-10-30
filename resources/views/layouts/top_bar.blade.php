@php
    $modulos = [
        'Inicio' => ['dashboard', 'inicio'],
        'Punto de venta' => ['ventas.*', 'pos.*'],
        'Inventario' => ['ajuste.*', 'movimientos.*'],
        'Categorías registradas' => ['categorias.*'],
        'Productos y Servicios' => ['articulos.*'],
        'Productos por lotes' => ['presentaciones.*'],
        'Marcas registradas' => ['marcas.*'],
        'Ingreso de mercadería' => ['compras.*'],
        'Ajuste de Existencias' => ['inventario.ajuste'],
        'Movimientos de inventario' => ['inventario.movimientos'],
        'Clientes registradas' => ['clientes.*'],

        'Proveedores' => ['proveedores.*', 'proveedor.*'],
        'Reportes' => ['reportes.*'],
        'Usuarios' => ['usuarios.*'],
        'Imagenes' => ['imagenes.*'],
        'Configuración' => ['metodos-pago.*', 'configuracion.*'],
        'Mi perfil' => ['profile.*'],
    ];

    $tituloModulo = '';
    foreach ($modulos as $titulo => $patrones) {
        if (request()->routeIs(...$patrones)) {
            $tituloModulo = $titulo;
            break;
        }
    }
    $accion = null;
    $accionKey = null;

    if (request()->routeIs('*.index')) {
        $accion = 'Listado';
        $accionKey = 'index';
    } elseif (request()->routeIs('*.create')) {
        $accion = 'Crear';
        $accionKey = 'create';
    } elseif (request()->routeIs('*.edit')) {
        $accion = 'Editar';
        $accionKey = 'edit';
    } elseif (request()->routeIs('*.show')) {
        $accion = 'Detalles';
        $accionKey = 'show';
    }

    $routeName = request()->route()?->getName();
    $routeBase = $routeName ? Str::before($routeName, '.') : null;
    $indexRoute =
        $routeBase && \Illuminate\Support\Facades\Route::has($routeBase . '.index')
            ? route($routeBase . '.index')
            : null;
@endphp

<nav class="bg-white">
    <div class="max-w-7xl px-6 mb-2 lg:px-8">
        <div class="flex items-center pt-2 h-12 gap-2 text-lg">

            @if ($tituloModulo)
                @if ($accionKey && $accionKey !== 'index' && $indexRoute)
                    <a href="{{ $indexRoute }}" class="text-gray-500 hover:text-gray-800 transition-colors font-medium">
                        {{ $tituloModulo }}
                    </a>
                @else
                    <span class="text-gray-800 font-medium">
                        {{ $tituloModulo }}
                    </span>
                @endif
            @endif

            @if ($accion && $accionKey !== 'index')
                <span class="text-gray-400">/</span>
                <span class="text-indigo-700 font-semibold">
                    {{ $accion }}
                </span>
            @endif

        </div>
    </div>
</nav>
