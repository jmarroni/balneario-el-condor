<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TideResource;
use App\Models\Tide;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TideController extends Controller
{
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
