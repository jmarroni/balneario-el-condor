<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('weather:current');
    }

    /**
     * Sample mínimo válido del payload de Open-Meteo.
     */
    private function fakePayload(): array
    {
        return [
            'current' => [
                'temperature_2m'        => 18.4,
                'relative_humidity_2m'  => 72,
                'wind_speed_10m'        => 23.7,
                'wind_direction_10m'    => 90, // E
                'weather_code'          => 0,  // Despejado
            ],
            'daily' => [
                'time'              => ['2026-04-24', '2026-04-25', '2026-04-26', '2026-04-27'],
                'temperature_2m_max' => [22.0, 21.5, 19.0, 17.0],
                'temperature_2m_min' => [12.0, 11.0, 10.0, 9.0],
                'weather_code'      => [0, 3, 61, 2],
            ],
        ];
    }

    public function test_sync_command_caches_weather(): void
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response($this->fakePayload(), 200),
        ]);

        $this->artisan('weather:sync')->assertSuccessful();

        $cached = Cache::get('weather:current');

        $this->assertNotNull($cached);
        $this->assertSame(18, $cached['temp']);
        $this->assertSame(72, $cached['humidity']);
        $this->assertSame(24, $cached['wind']); // round(23.7)
        $this->assertSame('E', $cached['wind_dir']);
        $this->assertSame('Despejado', $cached['description']);
        $this->assertCount(4, $cached['forecast']);
        $this->assertSame(22, $cached['forecast'][0]['max']);
    }

    public function test_sync_command_handles_api_failure(): void
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response(['error' => true], 503),
        ]);

        $this->artisan('weather:sync')->assertFailed();

        $this->assertNull(Cache::get('weather:current'));
    }

    public function test_clima_page_shows_current_when_cached(): void
    {
        Cache::put('weather:current', [
            'temp'        => 21,
            'humidity'    => 60,
            'wind'        => 18,
            'wind_dir'    => 'SO',
            'wind_label'  => 'Viento SO 18km/h',
            'code'        => 0,
            'description' => 'Despejado',
            'forecast'    => [
                ['date' => '2026-04-24', 'max' => 22, 'min' => 12, 'code' => 0],
            ],
            'updated_at'  => now()->toIso8601String(),
        ], now()->addHour());

        $this->get('/clima')
            ->assertOk()
            ->assertSee('Clima')
            ->assertSee('21')
            ->assertSee('Viento SO 18km/h')
            ->assertSee('Despejado');
    }

    public function test_clima_page_handles_missing_cache(): void
    {
        // No cache + Http::fake con error → la página renderiza el fallback.
        Http::fake([
            'api.open-meteo.com/*' => Http::response(['error' => true], 503),
        ]);

        $this->get('/clima')
            ->assertOk()
            ->assertSee('Servicio de clima temporalmente no disponible');
    }
}
