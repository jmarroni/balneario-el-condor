<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LodgingController;
use App\Http\Controllers\Public\NearbyPlaceController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\RentalController;
use App\Http\Controllers\Public\ServiceProviderController;
use App\Http\Controllers\Public\UsefulInfoController;
use App\Http\Controllers\Public\VenueController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/novedades', [NewsController::class, 'index'])->name('novedades.index');
Route::get('/novedades/{news:slug}', [NewsController::class, 'show'])->name('novedades.show');

Route::get('/eventos', [EventController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{event:slug}', [EventController::class, 'show'])->name('eventos.show');
Route::post('/eventos/{event:slug}/inscripcion', [EventController::class, 'register'])->name('eventos.register');

// Directorio público (Fase 5 / Task 5).
Route::get('/hospedajes', [LodgingController::class, 'index'])->name('hospedajes.index');
Route::get('/hospedajes/{lodging:slug}', [LodgingController::class, 'show'])->name('hospedajes.show');

Route::get('/gastronomia', [VenueController::class, 'index'])->name('gastronomia.index');
Route::get('/gastronomia/{venue:slug}', [VenueController::class, 'show'])->name('gastronomia.show');

Route::get('/alquileres', [RentalController::class, 'index'])->name('alquileres.index');
Route::get('/alquileres/{rental:slug}', [RentalController::class, 'show'])->name('alquileres.show');

Route::get('/servicios', [ServiceProviderController::class, 'index'])->name('servicios.index');
Route::get('/servicios/{serviceProvider:slug}', [ServiceProviderController::class, 'show'])->name('servicios.show');

Route::get('/cercanos', [NearbyPlaceController::class, 'index'])->name('cercanos.index');
Route::get('/cercanos/{nearbyPlace:slug}', [NearbyPlaceController::class, 'show'])->name('cercanos.show');

Route::get('/informacion-util', [UsefulInfoController::class, 'index'])->name('info-util.index');

// Stubs pendientes (a implementarse en tasks futuras).
Route::get('/recetas', fn () => 'pending')->name('recetas.index');
Route::get('/clasificados', fn () => 'pending')->name('clasificados.index');
Route::get('/galeria', fn () => 'pending')->name('galeria.index');
Route::get('/mareas', fn () => 'pending')->name('mareas.index');
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
