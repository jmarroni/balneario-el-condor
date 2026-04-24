<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function index(Request $request): View
    {
        $base = Rental::query()->with('media')->orderBy('title');

        if ($q = $request->string('q')->toString()) {
            $base->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('contact_name', 'like', "%{$q}%");
            });
        }

        if ($places = $request->integer('plazas')) {
            $base->where('places', '>=', $places);
        }

        return view('public.alquileres.index', [
            'items' => $base->paginate(12)->withQueryString(),
            'q'     => $q,
        ]);
    }

    public function show(Rental $rental): View
    {
        $rental->load('media');

        $related = Rental::query()
            ->where('id', '!=', $rental->id)
            ->with('media')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('public.alquileres.show', [
            'item'    => $rental,
            'related' => $related,
        ]);
    }
}
