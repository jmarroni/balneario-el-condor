<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\NearbyPlaceResource;
use App\Models\NearbyPlace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NearbyPlaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', NearbyPlace::class);

        $items = NearbyPlace::query()
            ->with('media')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderBy('title')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            NearbyPlaceResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }
}
