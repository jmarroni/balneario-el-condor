<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Venue::class);

        $items = Venue::query()
            ->with('media')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category', (string) $request->string('category')))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            VenueResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }

    public function show(Venue $venue): VenueResource
    {
        $this->authorize('view', $venue);

        return new VenueResource($venue->load('media'));
    }
}
