<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/novedades', [NewsController::class, 'index'])->name('novedades.index');
Route::get('/novedades/{news:slug}', [NewsController::class, 'show'])->name('novedades.show');

Route::get('/eventos', [EventController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{event:slug}', [EventController::class, 'show'])->name('eventos.show');
Route::post('/eventos/{event:slug}/inscripcion', [EventController::class, 'register'])->name('eventos.register');

// Stubs para que Route::has() del nav resuelva (se implementan en tasks 2-6)
Route::get('/hospedajes', fn () => 'pending')->name('hospedajes.index');
Route::get('/gastronomia', fn () => 'pending')->name('gastronomia.index');
Route::get('/alquileres', fn () => 'pending')->name('alquileres.index');
Route::get('/recetas', fn () => 'pending')->name('recetas.index');
Route::get('/clasificados', fn () => 'pending')->name('clasificados.index');
Route::get('/galeria', fn () => 'pending')->name('galeria.index');
Route::get('/mareas', fn () => 'pending')->name('mareas.index');
Route::get('/servicios', fn () => 'pending')->name('servicios.index');
Route::get('/cercanos', fn () => 'pending')->name('cercanos.index');
Route::get('/newsletter', fn () => 'pending')->name('newsletter.form');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Alias para forzar reset de contraseña (usado por ForcePasswordReset middleware).
    Route::get('/profile/password', [ProfileController::class, 'edit'])->name('password.edit');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
