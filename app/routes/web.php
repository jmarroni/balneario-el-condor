<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\ClassifiedController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LodgingController;
use App\Http\Controllers\Public\NearbyPlaceController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\RecipeController;
use App\Http\Controllers\Public\RentalController;
use App\Http\Controllers\Public\ServiceProviderController;
use App\Http\Controllers\Public\TideController;
use App\Http\Controllers\Public\UsefulInfoController;
use App\Http\Controllers\Public\VenueController;
use App\Http\Controllers\Public\WeatherController;
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

// Comunidad (Fase 5 / Task 6).
Route::get('/clasificados', [ClassifiedController::class, 'index'])->name('clasificados.index');
Route::get('/clasificados/{classified:slug}', [ClassifiedController::class, 'show'])->name('clasificados.show');
Route::post('/clasificados/{classified:slug}/contacto', [ClassifiedController::class, 'storeContact'])->name('clasificados.contact');

Route::get('/galeria', [GalleryController::class, 'index'])->name('galeria.index');

Route::get('/recetas', [RecipeController::class, 'index'])->name('recetas.index');
Route::get('/recetas/{recipe:slug}', [RecipeController::class, 'show'])->name('recetas.show');

// Mareas + Clima (Fase 5 / Task 7).
Route::get('/mareas', [TideController::class, 'index'])->name('mareas.index');
Route::get('/clima', [WeatherController::class, 'index'])->name('clima.index');

// Stubs pendientes (a implementarse en tasks futuras).
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
