<?php

namespace App\Http\ViewComposers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class NavigationComposer
{
    public function compose(View $view): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        $module = $this->resolveModule();
        $menus = $this->resolveMenus($user);

        $view->with([
            'navModule' => $module,
            'navMenus' => $menus[$module] ?? [],
        ]);
    }

    private function resolveModule(): string
    {
        return match (true) {
            Request::routeIs('pos.*', 'ventas.*') => 'pos',
            Request::routeIs('inventario.*', 'articulos.*', 'marcas.*', 'categorias.*', 'compras.*', 'ajuste.*', 'movimientos.*','presentaciones.*') => 'inventario',
            Request::routeIs('clientes.*') => 'clientes',
            Request::routeIs('proveedores.*') => 'proveedores',
            Request::routeIs('profile.*') => 'profile',
            Request::routeIs('reportes.*') => 'reportes',
            Request::routeIs('usuarios.*') => 'usuarios',
            Request::routeIs('imagenes.*') => 'medios',
            Request::routeIs('configuracion', 'configuracion.*', 'metodos-pago.*') => 'configuracion',
            default => 'dashboard',
        };
    }

    private function resolveMenus(?User $user): array
    {
        return [
            'pos' => [
                ['label' => 'Punto de venta', 'route' => 'ventas.create', 'active' => 'ventas.create'],
                [
                    'label' => 'Ventas registradas',
                    'route' => 'ventas.index',
                    'active' => [
                        'ventas.index',
                        'ventas.show',
                        'ventas.edit',
                    ]
                ],
            ],
            'dashboard' => [['label' => 'Inicio', 'route' => 'dashboard', 'active' => 'dashboard']],
            'profile' => [['label' => 'Perfil', 'route' => 'profile.show', 'active' => 'profile.*']],
            'inventario' => [
                [
                    'label' => 'Artículos',
                    'route' => 'articulos.index',
                    'active' => ['articulos.*'],
                ],
                [
                    'label' => 'Presentaciones',
                    'route' => 'presentaciones.index',
                    'active' => ['presentaciones.*'],
                ],

                // Opciones exclusivas del administrador
                ...($user?->esAdministrador() ? [
                    [
                        'label' => 'Categorías',
                        'route' => 'categorias.index',
                        'active' => ['categorias.*'],
                    ],
                    [
                        'label' => 'Marcas',
                        'route' => 'marcas.index',
                        'active' => ['marcas.*'],
                    ],
                    [
                        'label' => 'Compras',
                        'route' => 'compras.index',
                        'active' => ['compras.*'],
                    ],
                    [
                        'label' => 'Ajuste',
                        'route' => 'inventario.ajuste',
                        'active' => ['inventario.ajuste', 'inventario.ajustar'],
                    ],
                    [
                        'label' => 'Movimientos',
                        'route' => 'inventario.movimientos',
                        'active' => ['inventario.movimientos'],
                    ],
                ] : []),
            ],
            'clientes' => [['label' => 'Clientes', 'route' => 'clientes.index', 'active' => 'clientes.*']],
            'proveedores' => [['label' => 'Proveedores', 'route' => 'proveedores.index', 'active' => 'proveedores.*']],
            'usuarios' => [['label' => 'Colaboradores', 'route' => 'usuarios.index', 'active' => 'usuarios.*']],
            'reportes' => [
                ['label' => 'Resumen general', 'route' => null, 'active' => '', 'disabled' => true],
                ['label' => 'Inventario', 'route' => 'reportes.inventario', 'active' => 'reportes.inventario'],
                ['label' => 'Pagos', 'route' => null, 'active' => '', 'disabled' => true],
            ],
            'medios' => [['label' => 'Medios', 'route' => 'imagenes.index', 'active' => 'imagenes.*']],
            'configuracion' => array_filter([
                ['label' => 'Temas', 'route' => 'configuracion', 'active' => 'configuracion'],

                $user?->esAdministrador()
                ? ['label' => 'Metodos de Pago', 'route' => 'metodos-pago.index', 'active' => 'metodos-pago.*']
                : null,
            ]),
        ];
    }
}