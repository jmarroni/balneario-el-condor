<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ClassifiedResource;
use App\Models\Classified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassifiedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Classified::class);

        $items = Classified::query()
            ->with(['category', 'media'])
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('categoria'), function ($q) use ($request) {
                $slug = (string) $request->string('categoria');
                $q->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->orderByDesc('published_at')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            ClassifiedResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }

    public function show(Classified $classified): ClassifiedResource
    {
        $this->authorize('view', $classified);

        return new ClassifiedResource($classified->load(['category', 'media']));
    }

    /**
     * Endpoint moderable: el rol moderator tiene classifieds.delete.
     */
    public function destroy(Classified $classified): JsonResponse
    {
        $this->authorize('delete', $classified);

        $classified->delete();

        return response()->json(null, 204);
    }
}
