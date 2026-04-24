<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class WeatherController extends Controller
{
    /**
     * Página /clima: lee el snapshot cacheado en Redis bajo `weather:current`.
     * Si no hay cache (cold start), intenta sincronizar on-demand una vez.
     */
    public function index(): View
    {
        $weather = Cache::get('weather:current');

        if (! $weather) {
            // Sync on-demand. No falla la página si Open-Meteo está caído.
            try {
                Artisan::call('weather:sync');
                $weather = Cache::get('weather:current');
            } catch (\Throwable $e) {
                $weather = null;
            }
        }

        return view('public.clima.index', [
            'weather' => $weather,
        ]);
    }
}
