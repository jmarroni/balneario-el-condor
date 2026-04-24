<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    /**
     * Categorías legacy: gourmet (gastronomía) / nightlife (vida nocturna).
     *
     * @var array<int, string>
     */
    private const CATEGORIES = ['gourmet', 'nightlife'];

    public function index(Request $request): View
    {
        $category = $request->string('categoria')->toString();
        if (! in_array($category, self::CATEGORIES, true)) {
            $category = 'gourmet';
        }

        $base = Venue::query()
            ->with('media')
            ->where('category', $category)
            ->orderByDesc('views')
            ->orderBy('name');

        if ($q = $request->string('q')->toString()) {
            $base->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        return view('public.gastronomia.index', [
            'items'    => $base->paginate(12)->withQueryString(),
            'current'  => $category,
            'q'        => $q,
        ]);
    }

    public function show(Venue $venue): View
    {
        $venue->load('media');
        $venue->increment('views');

        $related = Venue::query()
            ->where('id', '!=', $venue->id)
            ->where('category', $venue->category)
            ->with('media')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('public.gastronomia.show', [
            'item'    => $venue,
            'related' => $related,
        ]);
    }
}
