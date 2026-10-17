<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\PresentacionController;
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
    ->except(['create', 'edit','show']);

    Route::resource('categorias', CategoriaController::class)
    ->except(['create', 'edit','show']);
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

    Route::resource('articulos', PresentacionController::class);



});

require __DIR__ . '/auth.php';
