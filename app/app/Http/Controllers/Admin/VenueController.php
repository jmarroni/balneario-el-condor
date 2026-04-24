<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVenueRequest;
use App\Http\Requests\Admin\UpdateVenueRequest;
use App\Models\Venue;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VenueController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Venue::class);

        $venues = Venue::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.venues.index', compact('venues'));
    }

    public function create(): View
    {
        $this->authorize('create', Venue::class);

        return view('admin.venues.create', [
            'venue' => new Venue(),
        ]);
    }

    public function store(StoreVenueRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['name']);

        $venue = Venue::create($data);

        return redirect()
            ->route('admin.venues.edit', $venue)
            ->with('success', 'Local creado.');
    }

    public function show(Venue $venue): RedirectResponse
    {
        $this->authorize('view', $venue);

        return redirect()->route('admin.venues.edit', $venue);
    }

    public function edit(Venue $venue): View
    {
        $this->authorize('update', $venue);

        return view('admin.venues.edit', compact('venue'));
    }

    public function update(UpdateVenueRequest $request, Venue $venue): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $venue->update($data);

        return redirect()
            ->route('admin.venues.edit', $venue)
            ->with('success', 'Local actualizado.');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $this->authorize('delete', $venue);

        $venue->delete();

        return redirect()
            ->route('admin.venues.index')
            ->with('success', 'Local eliminado.');
    }
}
