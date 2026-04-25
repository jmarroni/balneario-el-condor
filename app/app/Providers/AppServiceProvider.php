<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configurePulse();
    }

    /**
     * Restrict Pulse dashboard to admin role and label entries by user.
     */
    protected function configurePulse(): void
    {
        Gate::define('viewPulse', function ($user) {
            return $user !== null && method_exists($user, 'hasRole') && $user->hasRole('admin');
        });

        Pulse::user(fn ($user) => [
            'name' => $user->name ?? '',
            'extra' => $user->email ?? '',
            'avatar' => null,
        ]);
    }

    /**
     * Configure dynamic rate limiting per role for the API.
     *
     * Limits (requests per minute):
     *  - admin:     300
     *  - editor:    180
     *  - moderator: 120
     *  - other authenticated users: 60
     *  - guest (unauthenticated): 30 (keyed by IP)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            if (! $user) {
                return Limit::perMinute(30)->by($request->ip());
            }

            if ($user->hasRole('admin')) {
                return Limit::perMinute(300)->by('user:'.$user->id);
            }

            if ($user->hasRole('editor')) {
                return Limit::perMinute(180)->by('user:'.$user->id);
            }

            if ($user->hasRole('moderator')) {
                return Limit::perMinute(120)->by('user:'.$user->id);
            }

            return Limit::perMinute(60)->by('user:'.$user->id);
        });
    }
}
