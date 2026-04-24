<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NearbyPlace;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NearbyPlaceController extends Controller
{
    public function index(Request $request): View
    {
        $base = NearbyPlace::query()->with('media')->orderByDesc('views')->orderBy('title');

        if ($q = $request->string('q')->toString()) {
            $base->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        return view('public.cercanos.index', [
            'items' => $base->paginate(12)->withQueryString(),
            'q'     => $q,
        ]);
    }

    public function show(NearbyPlace $nearbyPlace): View
    {
        $nearbyPlace->load('media');
        $nearbyPlace->increment('views');

        $related = NearbyPlace::query()
            ->where('id', '!=', $nearbyPlace->id)
            ->with('media')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('public.cercanos.show', [
            'item'    => $nearbyPlace,
            'related' => $related,
        ]);
    }
}
