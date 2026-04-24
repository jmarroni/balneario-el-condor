<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsefulInfoRequest;
use App\Http\Requests\Admin\UpdateUsefulInfoRequest;
use App\Models\UsefulInfo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsefulInfoController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', UsefulInfo::class);

        $items = UsefulInfo::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(30);

        return view('admin.useful-info.index', compact('items'));
    }

    public function create(): View
    {
        $this->authorize('create', UsefulInfo::class);

        return view('admin.useful-info.create', [
            'item' => new UsefulInfo(),
        ]);
    }

    public function store(StoreUsefulInfoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $item = UsefulInfo::create($data);

        return redirect()
            ->route('admin.useful-info.edit', $item)
            ->with('success', 'Información útil creada.');
    }

    public function show(UsefulInfo $usefulInfo): RedirectResponse
    {
        $this->authorize('view', $usefulInfo);

        return redirect()->route('admin.useful-info.edit', $usefulInfo);
    }

    public function edit(UsefulInfo $usefulInfo): View
    {
        $this->authorize('update', $usefulInfo);

        return view('admin.useful-info.edit', [
            'item' => $usefulInfo,
        ]);
    }

    public function update(UpdateUsefulInfoRequest $request, UsefulInfo $usefulInfo): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $usefulInfo->update($data);

        return redirect()
            ->route('admin.useful-info.edit', $usefulInfo)
            ->with('success', 'Información útil actualizada.');
    }

    public function destroy(UsefulInfo $usefulInfo): RedirectResponse
    {
        $this->authorize('delete', $usefulInfo);

        $usefulInfo->delete();

        return redirect()
            ->route('admin.useful-info.index')
            ->with('success', 'Información útil eliminada.');
    }
}
