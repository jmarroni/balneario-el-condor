<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreNewsRequest;
use App\Http\Requests\Api\V1\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', News::class);

        $items = News::query()
            ->with(['category', 'media'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('categoria'), function ($q) use ($request) {
                $slug = (string) $request->string('categoria');
                $q->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->orderByDesc('published_at')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            NewsResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }

    public function show(News $news): NewsResource
    {
        $this->authorize('view', $news);

        return new NewsResource($news->load(['category', 'media']));
    }

    public function store(StoreNewsRequest $request): JsonResponse
    {
        // authorize() del FormRequest ya valida el permiso news.create.
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug((string) $data['title']);

        $news = News::create($data);

        return (new NewsResource($news->load(['category', 'media'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateNewsRequest $request, News $news): NewsResource
    {
        // authorize() del FormRequest valida news.update.
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        $news->update($data);

        return new NewsResource($news->fresh()->load(['category', 'media']));
    }

    public function destroy(News $news): JsonResponse
    {
        $this->authorize('delete', $news);

        $news->delete();

        return response()->json(null, 204);
    }
}
