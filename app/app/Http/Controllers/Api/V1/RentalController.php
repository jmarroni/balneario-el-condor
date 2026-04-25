<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\RentalResource;
use App\Models\Rental;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Rental::class);

        $items = Rental::query()
            ->with('media')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderBy('title')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            RentalResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ]
        ));
    }

    public function show(Rental $rental): RentalResource
    {
        $this->authorize('view', $rental);

        return new RentalResource($rental->load('media'));
    }
}
