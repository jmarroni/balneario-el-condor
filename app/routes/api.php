<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ClassifiedController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\GalleryController;
use App\Http\Controllers\Api\V1\LodgingController;
use App\Http\Controllers\Api\V1\NearbyPlaceController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\RecipeController;
use App\Http\Controllers\Api\V1\RentalController;
use App\Http\Controllers\Api\V1\ServiceProviderController;
use App\Http\Controllers\Api\V1\TideController;
use App\Http\Controllers\Api\V1\UsefulInfoController;
use App\Http\Controllers\Api\V1\VenueController;
use App\Http\Controllers\Api\V1\WeatherController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/me', function () {
        $user = auth()->user();

        return response()->json([
            'data' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'roles'     => $user->roles->pluck('name'),
                'abilities' => $user->currentAccessToken()?->abilities ?? [],
            ],
            'meta' => [
                'version'      => 'v1',
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    })->name('me');

    // Content - news / events / recipes / pages
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');

    Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');

    // Directory - lodgings / venues / rentals / service-providers / nearby-places / useful-info / classifieds
    Route::get('/lodgings', [LodgingController::class, 'index'])->name('lodgings.index');
    Route::get('/lodgings/{lodging:slug}', [LodgingController::class, 'show'])->name('lodgings.show');

    Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
    Route::get('/venues/{venue:slug}', [VenueController::class, 'show'])->name('venues.show');

    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental:slug}', [RentalController::class, 'show'])->name('rentals.show');

    Route::get('/service-providers', [ServiceProviderController::class, 'index'])->name('service-providers.index');
    Route::get('/nearby-places', [NearbyPlaceController::class, 'index'])->name('nearby-places.index');
    Route::get('/useful-info', [UsefulInfoController::class, 'index'])->name('useful-info.index');

    Route::get('/classifieds', [ClassifiedController::class, 'index'])->name('classifieds.index');
    Route::get('/classifieds/{classified:slug}', [ClassifiedController::class, 'show'])->name('classifieds.show');

    // Data - gallery / tides / weather
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

    Route::get('/tides', [TideController::class, 'index'])->name('tides.index');
    Route::get('/tides/week', [TideController::class, 'week'])->name('tides.week');

    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
});
