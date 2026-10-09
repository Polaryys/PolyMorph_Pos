<?php

use App\Http\Controllers\ControlInfanteController;
use App\Http\Controllers\FichaInfanteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
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
    Route::resource('/ficha-infantes', FichaInfanteController::class);
    Route::resource('/control-infantes', ControlInfanteController::class);
    Route::post('/control-infantes/{infante}/store', [ControlInfanteController::class, 'store'])->name('infante.stores');
    Route::resource('marcas', MarcaController::class);

    Route::resource('categorias', CategoriaController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    Route::resource('usuarios', UserController::class);
    Route::resource('imagenes', ImagenController::class)
    ->except(['show']);

});

require __DIR__ . '/auth.php';
