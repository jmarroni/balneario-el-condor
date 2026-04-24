<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNearbyPlaceRequest;
use App\Http\Requests\Admin\UpdateNearbyPlaceRequest;
use App\Models\NearbyPlace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NearbyPlaceController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', NearbyPlace::class);

        $places = NearbyPlace::query()
            ->orderBy('title')
            ->paginate(20);

        return view('admin.nearby-places.index', compact('places'));
    }

    public function create(): View
    {
        $this->authorize('create', NearbyPlace::class);

        return view('admin.nearby-places.create', [
            'place' => new NearbyPlace(),
        ]);
    }

    public function store(StoreNearbyPlaceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title']);

        $place = NearbyPlace::create($data);

        return redirect()
            ->route('admin.nearby-places.edit', $place)
            ->with('success', 'Lugar cercano creado.');
    }

    public function show(NearbyPlace $nearbyPlace): RedirectResponse
    {
        $this->authorize('view', $nearbyPlace);

        return redirect()->route('admin.nearby-places.edit', $nearbyPlace);
    }

    public function edit(NearbyPlace $nearbyPlace): View
    {
        $this->authorize('update', $nearbyPlace);

        return view('admin.nearby-places.edit', [
            'place' => $nearbyPlace,
        ]);
    }

    public function update(UpdateNearbyPlaceRequest $request, NearbyPlace $nearbyPlace): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $nearbyPlace->update($data);

        return redirect()
            ->route('admin.nearby-places.edit', $nearbyPlace)
            ->with('success', 'Lugar cercano actualizado.');
    }

    public function destroy(NearbyPlace $nearbyPlace): RedirectResponse
    {
        $this->authorize('delete', $nearbyPlace);

        $nearbyPlace->delete();

        return redirect()
            ->route('admin.nearby-places.index')
            ->with('success', 'Lugar cercano eliminado.');
    }
}
