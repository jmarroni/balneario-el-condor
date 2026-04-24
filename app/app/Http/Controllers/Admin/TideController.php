<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTideRequest;
use App\Http\Requests\Admin\UpdateTideRequest;
use App\Models\Tide;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TideController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Tide::class);

        $tides = Tide::query()
            ->orderByDesc('date')
            ->paginate(30);

        return view('admin.tides.index', compact('tides'));
    }

    public function create(): View
    {
        $this->authorize('create', Tide::class);

        return view('admin.tides.create', [
            'tide' => new Tide(['location' => 'El Cóndor']),
        ]);
    }

    public function store(StoreTideRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['location'])) {
            $data['location'] = 'El Cóndor';
        }

        $tide = Tide::create($data);

        return redirect()
            ->route('admin.tides.edit', $tide)
            ->with('success', 'Marea creada.');
    }

    public function show(Tide $tide): RedirectResponse
    {
        $this->authorize('view', $tide);

        return redirect()->route('admin.tides.edit', $tide);
    }

    public function edit(Tide $tide): View
    {
        $this->authorize('update', $tide);

        return view('admin.tides.edit', compact('tide'));
    }

    public function update(UpdateTideRequest $request, Tide $tide): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['location'])) {
            $data['location'] = 'El Cóndor';
        }

        $tide->update($data);

        return redirect()
            ->route('admin.tides.edit', $tide)
            ->with('success', 'Marea actualizada.');
    }

    public function destroy(Tide $tide): RedirectResponse
    {
        $this->authorize('delete', $tide);

        $tide->delete();

        return redirect()
            ->route('admin.tides.index')
            ->with('success', 'Marea eliminada.');
    }
}
