<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRentalRequest;
use App\Http\Requests\Admin\UpdateRentalRequest;
use App\Models\Rental;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RentalController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Rental::class);

        $rentals = Rental::query()
            ->orderBy('title')
            ->paginate(20);

        return view('admin.rentals.index', compact('rentals'));
    }

    public function create(): View
    {
        $this->authorize('create', Rental::class);

        return view('admin.rentals.create', [
            'rental' => new Rental(),
        ]);
    }

    public function store(StoreRentalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title']);

        $rental = Rental::create($data);

        return redirect()
            ->route('admin.rentals.edit', $rental)
            ->with('success', 'Alquiler creado.');
    }

    public function show(Rental $rental): RedirectResponse
    {
        $this->authorize('view', $rental);

        return redirect()->route('admin.rentals.edit', $rental);
    }

    public function edit(Rental $rental): View
    {
        $this->authorize('update', $rental);

        return view('admin.rentals.edit', compact('rental'));
    }

    public function update(UpdateRentalRequest $request, Rental $rental): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $rental->update($data);

        return redirect()
            ->route('admin.rentals.edit', $rental)
            ->with('success', 'Alquiler actualizado.');
    }

    public function destroy(Rental $rental): RedirectResponse
    {
        $this->authorize('delete', $rental);

        $rental->delete();

        return redirect()
            ->route('admin.rentals.index')
            ->with('success', 'Alquiler eliminado.');
    }
}
