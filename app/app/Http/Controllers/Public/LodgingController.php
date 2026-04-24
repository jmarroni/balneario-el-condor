<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lodging;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LodgingController extends Controller
{
    /**
     * Tipos válidos del enum (alineado con migration). 'other' existe pero no se
     * exhibe como tab por sí mismo — se incluye en el listado general.
     *
     * @var array<int, string>
     */
    private const TYPES = ['hotel', 'casa', 'camping', 'hostel'];

    public function index(Request $request): View
    {
        $type = $request->string('tipo')->toString();
        $type = in_array($type, self::TYPES, true) ? $type : null;

        $base = Lodging::query()->with('media')->orderByDesc('views')->orderBy('name');

        if ($type !== null) {
            $base->where('type', $type);
        }

        if ($q = $request->string('q')->toString()) {
            $base->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Conteos por tipo (para mostrar en los tabs).
        $counts = Lodging::query()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        return view('public.hospedajes.index', [
            'items'   => $base->paginate(12)->withQueryString(),
            'types'   => self::TYPES,
            'current' => $type,
            'counts'  => $counts,
            'q'       => $q,
        ]);
    }

    public function show(Lodging $lodging): View
    {
        $lodging->load('media');
        $lodging->increment('views');

        $related = Lodging::query()
            ->where('id', '!=', $lodging->id)
            ->where('type', $lodging->type)
            ->with('media')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('public.hospedajes.show', [
            'item'    => $lodging,
            'related' => $related,
        ]);
    }
}
