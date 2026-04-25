<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LodgingResource;
use App\Models\Lodging;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LodgingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Lodging::class);

        $items = Lodging::query()
            ->with('media')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('type'), fn ($q) => $q->where('type', (string) $request->string('type')))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            LodgingResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }

    public function show(Lodging $lodging): LodgingResource
    {
        $this->authorize('view', $lodging);

        return new LodgingResource($lodging->load('media'));
    }
}
