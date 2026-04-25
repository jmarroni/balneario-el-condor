<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\GalleryImageResource;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', GalleryImage::class);

        $items = GalleryImage::query()
            ->when($request->filled('year'), function ($q) use ($request) {
                $year = (int) $request->integer('year');
                $q->whereYear('taken_on', $year);
            })
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('taken_on')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            GalleryImageResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }
}
