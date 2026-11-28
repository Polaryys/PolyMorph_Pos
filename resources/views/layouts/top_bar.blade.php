@php
    $modulos = [
        'Registrar Venta' => ['ventas.create'],
        'Transacciones' => ['ventas.index','ventas.show'],
        'Inventario' => ['ajuste.*', 'movimientos.*'],
        'Categorías registradas' => ['categorias.*'],
        'Productos y Servicios' => ['articulos.*'],
        'Productos por lotes' => ['presentaciones.*'],
        'Marcas registradas' => ['marcas.*'],
        'Ingreso de mercadería' => ['compras.*'],
        'Ajuste de Existencias' => ['inventario.ajuste'],
        'Movimientos de inventario' => ['inventario.movimientos'],
        'Clientes registrados' => ['clientes.*'],

        'Proveedores registrados' => ['proveedores.*', 'proveedor.*'],
        'Visión general de Inventario' => ['reportes.inventario'],
        'Distribución de cobros' => ['reportes.pagos'],
        'Estadísticas Generales' => ['reportes.resumen-general'],
        'Usuarios' => ['usuarios.*'],
        'Imágenes del sistema' => ['imagenes.*'],
        'Apariencia del Sistema' => ['configuracion'],
        'Métodos de pago' => ['metodos-pago.*'],
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
    } elseif (request()->routeIs('*.create') && !request()->routeIs('ventas.create')) {
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
<nav>
    <div class="max-w-7xl px-6 mb-2 mt-8 lg:px-8">
        <div class="flex items-center h-12 gap-2 text-2xl">

            @if ($tituloModulo)
                @if ($accionKey && $accionKey !== 'index' && $indexRoute)
                    <a href="{{ $indexRoute }}"
                        class="text-app-text-secondary hover:text-app-text-primary transition-colors font-medium">
                        {{ $tituloModulo }}
                    </a>
                @else
                    <span class="text-app-text-primary font-medium">
                        {{ $tituloModulo }}
                    </span>
                @endif
            @endif

            @if ($accion && $accionKey !== 'index')
                <span class="text-app-text-muted">/</span>
                <span class="text-app-primary-500 font-semibold">
                    {{ $accion }}
                </span>
            @endif

        </div>
    </div>
</nav>
