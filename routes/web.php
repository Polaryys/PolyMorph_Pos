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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('marcas', MarcaController::class)
        ->except(['create', 'edit', 'show']);

    Route::resource('categorias', CategoriaController::class)
        ->except(['create', 'edit', 'show']);
    Route::resource('clientes', ClienteController::class);
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    Route::resource('usuarios', UserController::class);
    Route::resource('imagenes', ImagenController::class)
        ->except(['show', 'edit'])
        ->parameters(['imagenes' => 'imagen']);
    Route::get('/imagenes/selector', [ImagenController::class, 'selector'])
        ->name('imagenes.selector');
    Route::resource('metodos-pago', MetodoPagoController::class)
        ->parameters(['metodos-pago' => 'metodoPago']);

    Route::resource('articulos', InventarioController::class)
        ->parameters(['articulos' => 'inventario']);
    ;

    Route::get(
        '/presentaciones/buscar-productos',
        [PresentacionController::class, 'buscarProductos']
    )->name('presentaciones.buscar-productos');
    Route::resource('presentaciones', PresentacionController::class)
        ->parameters(['presentaciones' => 'presentacion']);



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

});

require __DIR__ . '/auth.php';
