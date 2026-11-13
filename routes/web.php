<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PresentacionController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\Reportes\ReporteInventarioController;
use Illuminate\Support\Facades\Route;

// Entrada pública y acceso al panel; el dashboard requiere sesión verificada.
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil del usuario autenticado y operaciones de cuenta.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD de catálogos usados por inventario y transacciones.
    Route::resource('marcas', MarcaController::class)
        ->except(['create', 'edit', 'show']);

    Route::resource('categorias', CategoriaController::class)
        ->except(['create', 'edit', 'show']);
    Route::resource('clientes', ClienteController::class);
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    Route::resource('usuarios', UserController::class);

    // Administración y selector de imágenes; gestión de métodos de pago.
    Route::resource('imagenes', ImagenController::class)
        ->except(['show', 'edit'])
        ->parameters(['imagenes' => 'imagen']);
    Route::get('/imagenes/selector', [ImagenController::class, 'selector'])
        ->name('imagenes.selector');
    Route::resource('metodos-pago', MetodoPagoController::class)
        ->parameters(['metodos-pago' => 'metodoPago']);

    // CRUD de artículos y carga/descarga del inventario inicial mediante CSV.
    Route::get('/articulos/exportar-csv', [InventarioController::class, 'exportarCsv'])
        ->name('articulos.exportar-csv');
    Route::post('/articulos/importar-csv', [InventarioController::class, 'importarCsv'])
        ->name('articulos.importar-csv');
    Route::resource('articulos', InventarioController::class)
        ->parameters(['articulos' => 'inventario']);

    // Ajustes de existencias, búsqueda de artículos y consulta de movimientos.
    Route::get('/inventario/ajuste', [InventarioController::class, 'mostrarAjusteInventario'])
        ->name('inventario.ajuste');
    Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos'])
        ->name('inventario.movimientos');
    Route::get('/inventario/buscar-productos-ajuste', [InventarioController::class, 'buscarProductosParaAjuste'])
        ->name('inventario.buscar-productos-ajuste');
    Route::post('/inventario/ajuste', [InventarioController::class, 'ajustarInventario'])
        ->name('inventario.ajustar');

    // Búsqueda y CRUD de presentaciones adicionales a la base.
    Route::get(
        '/presentaciones/buscar-productos',
        [PresentacionController::class, 'buscarProductos']
    )->name('presentaciones.buscar-productos');
    Route::resource('presentaciones', PresentacionController::class)
        ->parameters(['presentaciones' => 'presentacion']);

    // Búsqueda, registro, consulta, anulación y pagos de ventas.
    Route::get('/ventas/create', [VentasController::class, 'create'])
        ->name('ventas.create');
    Route::post('/ventas/carrito', [VentasController::class, 'guardarCarrito'])
        ->name('ventas.carrito.guardar');
    Route::get('/ventas/buscar-productos', [VentasController::class, 'buscarProductos'])
        ->name('ventas.buscar-productos');
    Route::get('/ventas/buscar-clientes', [VentasController::class, 'buscarClientes'])
        ->name('ventas.buscar-clientes');
    Route::get('/ventas/metodos-pago', [VentasController::class, 'metodosPago'])
        ->name('ventas.metodos-pago');
    Route::get('/ventas', [VentasController::class, 'index'])
        ->name('ventas.index');
    Route::post('/ventas', [VentasController::class, 'store'])
        ->name('ventas.store');
    Route::post('/ventas/{venta}/anular', [VentasController::class, 'anular'])
        ->name('ventas.anular');
    Route::post('/ventas/{venta}/pagos', [VentasController::class, 'registrarPago'])
        ->name('ventas.pagos.store');
    Route::get('/ventas/{venta}', [VentasController::class, 'show'])
        ->name('ventas.show');

    // Búsqueda de productos y ciclo de vida de compras a proveedores.
    Route::get('/compras/buscar-productos', [ComprasController::class, 'buscarProductos'])
        ->name('compras.buscar-productos');
    Route::post('/compras', [ComprasController::class, 'store'])
        ->name('compras.store');
    Route::put('/compras/{compra}', [ComprasController::class, 'update'])
        ->name('compras.update');
    Route::delete('/compras/{compra}', [ComprasController::class, 'destroy'])
        ->name('compras.destroy');
    Route::post('/compras/{compra}/registrar', [ComprasController::class, 'registrarCompra'])
        ->name('compras.registrar');
    Route::post('/compras/{compra}/revertir', [ComprasController::class, 'revertirCompra'])
        ->name('compras.revertir');
    Route::get('/compras', [ComprasController::class, 'index'])
        ->name('compras.index');

    Route::get('/compras/create', [ComprasController::class, 'create'])
        ->name('compras.create');
    Route::get('/compras/{compra}/edit', [ComprasController::class, 'edit'])
        ->name('compras.edit');
    Route::get('/compras/{compra}', [ComprasController::class, 'show'])
        ->name('compras.show');

    Route::get('/reportes/inventario', [
        ReporteInventarioController::class,
        'inventarioReporte'
    ])->name('reportes.inventario');

});

// Rutas de inicio de sesión, registro y recuperación de acceso.
require __DIR__ . '/auth.php';
