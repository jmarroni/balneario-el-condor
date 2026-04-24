<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLodgingRequest;
use App\Http\Requests\Admin\UpdateLodgingRequest;
use App\Models\Lodging;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LodgingController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Lodging::class);

        $lodgings = Lodging::query()
            ->orderBy('name')
            ->paginate(20);

        return view('admin.lodgings.index', compact('lodgings'));
    }

    public function create(): View
    {
        $this->authorize('create', Lodging::class);

        return view('admin.lodgings.create', [
            'lodging' => new Lodging(),
        ]);
    }

    public function store(StoreLodgingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['name']);

        $lodging = Lodging::create($data);

        return redirect()
            ->route('admin.lodgings.edit', $lodging)
            ->with('success', 'Alojamiento creado.');
    }

    public function show(Lodging $lodging): RedirectResponse
    {
        $this->authorize('view', $lodging);

        return redirect()->route('admin.lodgings.edit', $lodging);
    }

    public function edit(Lodging $lodging): View
    {
        $this->authorize('update', $lodging);

        return view('admin.lodgings.edit', compact('lodging'));
    }

    public function update(UpdateLodgingRequest $request, Lodging $lodging): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $lodging->update($data);

        return redirect()
            ->route('admin.lodgings.edit', $lodging)
            ->with('success', 'Alojamiento actualizado.');
    }

    public function destroy(Lodging $lodging): RedirectResponse
    {
        $this->authorize('delete', $lodging);

        $lodging->delete();

        return redirect()
            ->route('admin.lodgings.index')
            ->with('success', 'Alojamiento eliminado.');
    }
}
