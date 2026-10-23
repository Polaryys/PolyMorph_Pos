@php
    $modulos = [
        'Inicio' => ['dashboard', 'inicio'],
        'Punto de venta' => ['ventas.*', 'pos.*'],
        'Inventario' => ['presentaciones.*','marcas.*', 'compras.*','ajuste.*', 'movimientos.*'],
        'Registros' => ['categorias.*','clientes.*'],
        'Productos y Servicios' => ['articulos.*'],
        'Proveedores' => ['proveedores.*', 'proveedore.*'],
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
    $indexRoute = $routeBase && \Illuminate\Support\Facades\Route::has($routeBase . '.index') 
                    ? route($routeBase . '.index') 
                    : null;
@endphp

<nav class="fixed top-14 left-0 right-0 md:left-56 z-20 bg-white">
    <div class="max-w-7xl px-6 lg:px-8">
        <div class="flex items-center pt-1 h-12 gap-2 text-2xl">

            @if ($tituloModulo)
                @if ($accionKey && $accionKey !== 'index' && $indexRoute)
                    <a href="{{ $indexRoute }}" class="text-gray-400 hover:text-indigo-600 transition-colors font-medium">
                        {{ $tituloModulo }}
                    </a>
                @else
                    <span class="text-gray-700 font-medium">
                        {{ $tituloModulo }}
                    </span>
                @endif
            @endif

            @if ($accion && $accionKey !== 'index')
                <span class="text-gray-400">/</span>
                <span class="text-indigo-600 font-semibold">
                    {{ $accion }}
                </span>
            @endif

        </div>
    </div>
</nav>