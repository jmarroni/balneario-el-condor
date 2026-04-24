<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::query()
            ->orderBy('title')
            ->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        $this->authorize('create', Page::class);

        return view('admin.pages.create', [
            'page' => new Page(),
        ]);
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['published'] = (bool) ($data['published'] ?? false);

        $page = Page::create($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Página creada.');
    }

    public function show(Page $page): RedirectResponse
    {
        $this->authorize('view', $page);

        return redirect()->route('admin.pages.edit', $page);
    }

    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $data['published'] = (bool) ($data['published'] ?? false);

        $page->update($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Página actualizada.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Página eliminada.');
    }
}
