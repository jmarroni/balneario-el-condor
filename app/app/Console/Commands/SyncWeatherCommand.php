<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncWeatherCommand extends Command
{
    protected $signature = 'weather:sync';

    protected $description = 'Sincroniza clima actual y forecast desde Open-Meteo y cachea en Redis 2h.';

    /**
     * Coordenadas El Cóndor (Río Negro, Argentina).
     */
    private const LAT = -41.05;
    private const LON = -62.82;

    private const TIMEZONE = 'America/Argentina/Buenos_Aires';
    private const CACHE_KEY = 'weather:current';

    public function handle(): int
    {
        try {
            $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'      => self::LAT,
                'longitude'     => self::LON,
                'current'       => 'temperature_2m,wind_speed_10m,wind_direction_10m,weather_code,relative_humidity_2m',
                'daily'         => 'temperature_2m_max,temperature_2m_min,weather_code',
                'timezone'      => self::TIMEZONE,
                'forecast_days' => 4,
            ]);
        } catch (\Throwable $e) {
            Log::warning('weather:sync HTTP exception: '.$e->getMessage());
            $this->error('Error de red: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($response->failed()) {
            Log::warning('weather:sync API error status='.$response->status());
            $this->error('Open-Meteo respondió '.$response->status());

            return self::FAILURE;
        }

        $current = $response->json('current');
        $daily = $response->json('daily');

        if (! is_array($current) || ! is_array($daily)) {
            Log::warning('weather:sync payload malformado');
            $this->error('Payload inválido de Open-Meteo');

            return self::FAILURE;
        }

        $windDir = (int) round($current['wind_direction_10m'] ?? 0);
        $windKmh = (int) round($current['wind_speed_10m'] ?? 0);
        $compass = $this->compassFromDegrees($windDir);

        $forecast = collect($daily['time'] ?? [])
            ->map(fn ($day, $i) => [
                'date' => $day,
                'max'  => (int) round($daily['temperature_2m_max'][$i] ?? 0),
                'min'  => (int) round($daily['temperature_2m_min'][$i] ?? 0),
                'code' => (int) ($daily['weather_code'][$i] ?? 0),
            ])
            ->all();

        $summary = [
            'temp'        => (int) round($current['temperature_2m'] ?? 0),
            'humidity'    => (int) round($current['relative_humidity_2m'] ?? 0),
            'wind'        => $windKmh,
            'wind_dir'    => $compass,
            'wind_label'  => sprintf('Viento %s %dkm/h', $compass, $windKmh),
            'code'        => (int) ($current['weather_code'] ?? 0),
            'description' => $this->describeCode((int) ($current['weather_code'] ?? 0)),
            'forecast'    => $forecast,
            'updated_at'  => now()->toIso8601String(),
        ];

        Cache::put(self::CACHE_KEY, $summary, now()->addHours(2));

        $this->info(sprintf(
            'Clima actualizado: %d°C · %s · %s',
            $summary['temp'],
            $summary['description'],
            $summary['wind_label']
        ));

        return self::SUCCESS;
    }

    /**
     * Convierte grados meteorológicos (0=N, 90=E, 180=S, 270=O) a brújula ES.
     */
    private function compassFromDegrees(int $deg): string
    {
        $dirs = ['N', 'NE', 'E', 'SE', 'S', 'SO', 'O', 'NO'];

        return $dirs[(int) round($deg / 45) % 8];
    }

    /**
     * WMO weather code → descripción ES (simplificada).
     * Ref: https://open-meteo.com/en/docs (códigos 0..99).
     */
    private function describeCode(int $code): string
    {
        return match (true) {
            $code === 0       => 'Despejado',
            $code <= 3        => 'Parcialmente nublado',
            $code <= 48       => 'Niebla',
            $code <= 57       => 'Llovizna',
            $code <= 67       => 'Lluvia',
            $code <= 77       => 'Nevadas',
            $code <= 82       => 'Chubascos',
            $code <= 86       => 'Chubascos de nieve',
            default           => 'Tormenta',
        };
    }
}
