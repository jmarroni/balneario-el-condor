<?php

use App\Http\Controllers\Admin\ClassifiedContactController;
use App\Http\Controllers\Admin\ClassifiedController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\GalleryImageController;
use App\Http\Controllers\Admin\LodgingController;
use App\Http\Controllers\Admin\NearbyPlaceController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\RentalController;
use App\Http\Controllers\Admin\ServiceProviderController;
use App\Http\Controllers\Admin\TideController;
use App\Http\Controllers\Admin\TideImportController;
use App\Http\Controllers\Admin\UsefulInfoController;
use App\Http\Controllers\Admin\VenueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'force.password.reset', 'role:admin|editor|moderator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Contenido
        Route::resource('news', NewsController::class);
        // TODO: descomentar cuando se cree el controller correspondiente en Task 4
        // Route::resource('news-categories', NewsCategoryController::class);
        Route::resource('events', EventController::class);
        Route::resource('events.registrations', EventRegistrationController::class)
            ->shallow()->only(['index', 'show', 'destroy']);
        Route::resource('pages', PageController::class);
        Route::resource('recipes', RecipeController::class);

        // Directorio
        Route::resource('lodgings', LodgingController::class);
        Route::resource('venues', VenueController::class);
        Route::resource('rentals', RentalController::class);
        Route::resource('service-providers', ServiceProviderController::class)
            ->parameters(['service-providers' => 'serviceProvider']);
        Route::resource('nearby-places', NearbyPlaceController::class)
            ->parameters(['nearby-places' => 'nearbyPlace']);
        Route::resource('useful-info', UsefulInfoController::class)
            ->parameters(['useful-info' => 'usefulInfo']);

        // Tides: rutas de import antes del resource para que no choquen con {tide}
        Route::get('tides/import', [TideImportController::class, 'form'])->name('tides.import.form');
        Route::post('tides/import', [TideImportController::class, 'import'])->name('tides.import');
        Route::resource('tides', TideController::class);

        // Comunidad
        Route::resource('classifieds', ClassifiedController::class);
        Route::resource('classifieds.contacts', ClassifiedContactController::class)
            ->shallow()->only(['index', 'show', 'destroy']);
        Route::resource('gallery', GalleryImageController::class)
            ->parameters(['gallery' => 'galleryImage']);

        // Engagement
        // TODO: descomentar cuando se cree el controller correspondiente en Task 7
        // Route::resource('surveys', SurveyController::class);
        // Route::resource('surveys.responses', SurveyResponseController::class)
        //     ->shallow()->only(['index', 'show', 'destroy']);
        // Route::resource('newsletter-subscribers', NewsletterSubscriberController::class)
        //     ->parameters(['newsletter-subscribers' => 'subscriber'])
        //     ->except(['show']);
        // Route::resource('newsletter-campaigns', NewsletterCampaignController::class)
        //     ->parameters(['newsletter-campaigns' => 'campaign']);
        // Route::resource('contact-messages', ContactMessageController::class)
        //     ->parameters(['contact-messages' => 'message'])
        //     ->only(['index', 'show', 'destroy']);
        // Route::resource('advertising-contacts', AdvertisingContactController::class)
        //     ->parameters(['advertising-contacts' => 'adContact'])
        //     ->only(['index', 'show', 'destroy']);

        // Sistema
        // TODO: descomentar cuando se cree el controller correspondiente en Task 8
        // Route::resource('users', UserController::class)->middleware('role:admin');
    });
