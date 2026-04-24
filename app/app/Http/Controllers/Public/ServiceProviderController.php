<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceProviderController extends Controller
{
    public function index(Request $request): View
    {
        $base = ServiceProvider::query()->with('media')->orderBy('name');

        if ($q = $request->string('q')->toString()) {
            $base->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('contact_name', 'like', "%{$q}%");
            });
        }

        return view('public.servicios.index', [
            'items' => $base->paginate(12)->withQueryString(),
            'q'     => $q,
        ]);
    }

    public function show(ServiceProvider $serviceProvider): View
    {
        $serviceProvider->load('media');

        $related = ServiceProvider::query()
            ->where('id', '!=', $serviceProvider->id)
            ->with('media')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('public.servicios.show', [
            'item'    => $serviceProvider,
            'related' => $related,
        ]);
    }
}
