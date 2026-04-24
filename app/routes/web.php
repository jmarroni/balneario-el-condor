<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AdvertisingController;
use App\Http\Controllers\Public\ClassifiedController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LodgingController;
use App\Http\Controllers\Public\NearbyPlaceController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\RecipeController;
use App\Http\Controllers\Public\RentalController;
use App\Http\Controllers\Public\ServiceProviderController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\TideController;
use App\Http\Controllers\Public\UsefulInfoController;
use App\Http\Controllers\Public\VenueController;
use App\Http\Controllers\Public\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    $path = public_path('robots.txt');

    if (! is_file($path)) {
        abort(404);
    }

    return response(file_get_contents($path), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

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

// Forms públicos + páginas estáticas (Fase 5 / Task 8).
Route::get('/contacto', [ContactController::class, 'show'])->name('contacto.show');
Route::post('/contacto', [ContactController::class, 'store'])->name('contacto.store');

Route::get('/newsletter', [NewsletterController::class, 'show'])->name('newsletter.form');
Route::post('/newsletter', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/confirmar/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/baja/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/publicite', [AdvertisingController::class, 'show'])->name('publicite.show');
Route::post('/publicite', [AdvertisingController::class, 'store'])->name('publicite.store');

Route::get('/pagina/{page:slug}', [PageController::class, 'show'])->name('pages.show');

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
