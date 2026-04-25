<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TideResource;
use App\Models\Tide;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Mareas
 *
 * Información de mareas (alta y baja) para la zona del Balneario.
 */
class TideController extends Controller
{
    /**
     * Mareas del día
     *
     * Devuelve la información de mareas para una fecha determinada (default: hoy).
     *
     * @queryParam date string Fecha en formato YYYY-MM-DD. Default: hoy. Example: 2026-04-25
     */
    public function index(Request $request): JsonResponse
    {
        $date = $this->parseDate((string) $request->string('date', ''));

        $tide = Tide::query()
            ->whereDate('date', $date->toDateString())
            ->first();

        return response()->json($this->envelope(
            $tide ? (new TideResource($tide))->resolve($request) : null,
            [
                'date' => $date->toDateString(),
            ]
        ));
    }

    /**
     * Mareas de la semana
     *
     * Devuelve las mareas de la semana (lunes a domingo) que contiene la fecha consultada.
     *
     * @queryParam date string Fecha de referencia en formato YYYY-MM-DD. Default: hoy. Example: 2026-04-25
     */
    public function week(Request $request): JsonResponse
    {
        $reference = $this->parseDate((string) $request->string('date', ''));
        $monday    = $reference->startOfWeek();
        $sunday    = $monday->addDays(6);

        $tides = Tide::query()
            ->whereBetween('date', [$monday->toDateString(), $sunday->toDateString()])
            ->orderBy('date')
            ->get();

        return response()->json($this->envelope(
            TideResource::collection($tides)->resolve($request),
            [
                'week_start' => $monday->toDateString(),
                'week_end'   => $sunday->toDateString(),
                'count'      => $tides->count(),
            ]
        ));
    }

    private function parseDate(string $raw): CarbonImmutable
    {
        if ($raw === '') {
            return CarbonImmutable::today();
        }

        try {
            return CarbonImmutable::parse($raw)->startOfDay();
        } catch (\Throwable) {
            return CarbonImmutable::today();
        }
    }
}
