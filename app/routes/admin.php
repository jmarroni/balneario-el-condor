<?php

use App\Http\Controllers\Admin\AdvertisingContactController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ClassifiedContactController;
use App\Http\Controllers\Admin\ClassifiedController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\GalleryImageController;
use App\Http\Controllers\Admin\LodgingController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NearbyPlaceController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsletterCampaignController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\RentalController;
use App\Http\Controllers\Admin\ServiceProviderController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\SurveyResponseController;
use App\Http\Controllers\Admin\TideController;
use App\Http\Controllers\Admin\TideImportController;
use App\Http\Controllers\Admin\UsefulInfoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VenueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'force.password.reset', 'require.2fa.admin', 'role:admin|editor|moderator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Configuración de autenticación de dos factores (Fortify)
        Route::get('two-factor', function () {
            return view('admin.profile.two-factor', ['user' => auth()->user()->fresh()]);
        })->name('two-factor.show');

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
        Route::resource('surveys', SurveyController::class);
        Route::resource('surveys.responses', SurveyResponseController::class)
            ->shallow()->only(['index', 'show', 'destroy']);

        // Export debe ir antes del resource para no chocar con {subscriber}
        Route::get('newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])
            ->name('newsletter-subscribers.export');
        Route::resource('newsletter-subscribers', NewsletterSubscriberController::class)
            ->parameters(['newsletter-subscribers' => 'subscriber'])
            ->only(['index', 'destroy']);

        Route::resource('newsletter-campaigns', NewsletterCampaignController::class)
            ->parameters(['newsletter-campaigns' => 'campaign']);
        Route::post('newsletter-campaigns/{campaign}/send', [NewsletterCampaignController::class, 'send'])
            ->name('newsletter-campaigns.send');

        Route::resource('contact-messages', ContactMessageController::class)
            ->parameters(['contact-messages' => 'message'])
            ->only(['index', 'show', 'destroy']);
        Route::patch('contact-messages/{message}/mark-read', [ContactMessageController::class, 'markRead'])
            ->name('contact-messages.mark-read');

        Route::resource('advertising-contacts', AdvertisingContactController::class)
            ->parameters(['advertising-contacts' => 'adContact'])
            ->only(['index', 'show', 'destroy']);

        // Media (polimórfico, usado por múltiples recursos)
        // reorder primero para que no matchee {media} como string 'reorder'
        Route::patch('media/reorder', [MediaController::class, 'reorder'])->name('media.reorder');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Sistema
        Route::resource('users', UserController::class)->middleware('role:admin');

        // Bitácora de cambios — solo admin
        Route::get('audit-log', [AuditLogController::class, 'index'])
            ->name('audit-log.index')
            ->middleware('role:admin');

        // Tokens API personales (cualquier usuario logueado al admin)
        Route::get('tokens', [ApiTokenController::class, 'index'])->name('tokens.index');
        Route::post('tokens', [ApiTokenController::class, 'store'])->name('tokens.store');
        Route::delete('tokens/{id}', [ApiTokenController::class, 'destroy'])->name('tokens.destroy');
    });
