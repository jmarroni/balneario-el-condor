<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Http\Requests\Admin\UpdateNewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', News::class);

        $news = News::query()
            ->with('category')
            ->latest('published_at')
            ->paginate(20);

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('admin.news.create', [
            'news'       => new News(),
            'categories' => NewsCategory::orderBy('name')->get(),
        ]);
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title']);

        $news = News::create($data);

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('success', 'Noticia creada.');
    }

    public function show(News $news): RedirectResponse
    {
        $this->authorize('view', $news);

        return redirect()->route('admin.news.edit', $news);
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);

        return view('admin.news.edit', [
            'news'       => $news,
            'categories' => NewsCategory::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $news->update($data);

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('success', 'Noticia actualizada.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);

        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Noticia eliminada.');
    }
}
