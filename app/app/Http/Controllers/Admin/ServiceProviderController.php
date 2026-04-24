<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceProviderRequest;
use App\Http\Requests\Admin\UpdateServiceProviderRequest;
use App\Models\ServiceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceProviderController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', ServiceProvider::class);

        $providers = ServiceProvider::query()
            ->orderBy('name')
            ->paginate(20);

        return view('admin.service-providers.index', compact('providers'));
    }

    public function create(): View
    {
        $this->authorize('create', ServiceProvider::class);

        return view('admin.service-providers.create', [
            'provider' => new ServiceProvider(),
        ]);
    }

    public function store(StoreServiceProviderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['name']);

        $provider = ServiceProvider::create($data);

        return redirect()
            ->route('admin.service-providers.edit', $provider)
            ->with('success', 'Prestador creado.');
    }

    public function show(ServiceProvider $serviceProvider): RedirectResponse
    {
        $this->authorize('view', $serviceProvider);

        return redirect()->route('admin.service-providers.edit', $serviceProvider);
    }

    public function edit(ServiceProvider $serviceProvider): View
    {
        $this->authorize('update', $serviceProvider);

        return view('admin.service-providers.edit', [
            'provider' => $serviceProvider,
        ]);
    }

    public function update(UpdateServiceProviderRequest $request, ServiceProvider $serviceProvider): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $serviceProvider->update($data);

        return redirect()
            ->route('admin.service-providers.edit', $serviceProvider)
            ->with('success', 'Prestador actualizado.');
    }

    public function destroy(ServiceProvider $serviceProvider): RedirectResponse
    {
        $this->authorize('delete', $serviceProvider);

        $serviceProvider->delete();

        return redirect()
            ->route('admin.service-providers.index')
            ->with('success', 'Prestador eliminado.');
    }
}
