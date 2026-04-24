<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tide;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TideController extends Controller
{
    /**
     * Página /mareas: muestra el día solicitado (?fecha=YYYY-MM-DD), navegación
     * por día y la tabla de la semana actual. Default: hoy.
     */
    public function index(Request $request): View
    {
        // request->date() devuelve Carbon o null. Si no hay fecha o es inválida, hoy.
        $date = $request->date('fecha') ?? today();

        $tide = Tide::whereDate('date', $date->toDateString())->first();

        $week = Tide::whereBetween('date', [
                $date->copy()->startOfWeek()->toDateString(),
                $date->copy()->endOfWeek()->toDateString(),
            ])
            ->orderBy('date')
            ->get();

        // Fallbacks: si no hay datos del día, buscar más cercano en ±7 días.
        $nearestTide = null;
        if (! $tide) {
            $nearestTide = Tide::whereBetween('date', [
                    $date->copy()->subDays(7)->toDateString(),
                    $date->copy()->addDays(7)->toDateString(),
                ])
                ->orderByRaw('ABS(DATEDIFF(date, ?))', [$date->toDateString()])
                ->first();
        }

        return view('public.mareas.index', [
            'date'        => $date,
            'tide'        => $tide,
            'nearestTide' => $nearestTide,
            'week'        => $week,
            'prevDay'     => $date->copy()->subDay(),
            'nextDay'     => $date->copy()->addDay(),
            'today'       => today(),
        ]);
    }
}
