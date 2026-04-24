<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincroniza el clima desde Open-Meteo cada 30 minutos. Cachea en Redis 2h.
Schedule::command('weather:sync')
    ->everyThirtyMinutes()
    ->onOneServer()
    ->runInBackground();
