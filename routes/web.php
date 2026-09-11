<?php

use App\Http\Controllers\ControlInfanteController;
use App\Http\Controllers\FichaInfanteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/ficha-infantes', FichaInfanteController::class);
    Route::resource('/control-infantes', ControlInfanteController::class);
    Route::post('/control-infantes/{infante}/store', [ControlInfanteController::class, 'store'])->name('infante.stores');

});

require __DIR__.'/auth.php';
