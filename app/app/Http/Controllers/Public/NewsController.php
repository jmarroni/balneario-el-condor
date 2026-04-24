<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Listado público de novedades. Featured = primera publicada (no hay
     * columna `featured` en la tabla; el destacado emerge del orden).
     */
    public function index(Request $request): View
    {
        $base = News::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['category', 'media'])
            ->latest('published_at');

        if ($request->filled('categoria')) {
            $base->whereHas(
                'category',
                fn ($q) => $q->where('slug', $request->string('categoria'))
            );
        }

        $featured = (clone $base)->first();

        $news = (clone $base)
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->paginate(12)
            ->withQueryString();

        return view('public.novedades.index', [
            'featured' => $featured,
            'news' => $news,
            'categories' => NewsCategory::orderBy('name')->get(),
            'current' => $request->string('categoria')->toString() ?: null,
        ]);
    }

    /**
     * Vista de detalle (artículo de revista). 404 para no publicadas o
     * con published_at en el futuro.
     */
    public function show(News $news): View
    {
        abort_unless(
            $news->published_at !== null && $news->published_at->lessThanOrEqualTo(now()),
            404
        );

        $news->increment('views');
        $news->load(['category', 'media']);

        $related = News::query()
            ->where('news_category_id', $news->news_category_id)
            ->where('id', '!=', $news->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['category', 'media'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.novedades.show', [
            'news' => $news,
            'related' => $related,
        ]);
    }
}
