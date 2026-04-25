<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    public function index(): JsonResponse
    {
        $weather = Cache::get('weather:current');

        if ($weather === null) {
            return response()->json($this->envelope(null, [
                'message' => 'No disponible',
            ]));
        }

        return response()->json($this->envelope($weather, [
            'source' => 'cache',
        ]));
    }
}
