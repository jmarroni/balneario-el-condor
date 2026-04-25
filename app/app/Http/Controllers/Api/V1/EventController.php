<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Event::class);

        $cuando = (string) $request->string('cuando', 'proximos');

        $items = Event::query()
            ->with('media')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($cuando === 'proximos', fn ($q) => $q->where(function ($w) {
                $w->whereNull('starts_at')->orWhere('starts_at', '>=', now());
            }))
            ->when($cuando === 'pasados', fn ($q) => $q->whereNotNull('starts_at')->where('starts_at', '<', now()))
            ->orderBy('starts_at', $cuando === 'pasados' ? 'desc' : 'asc')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($this->envelope(
            EventResource::collection($items)->resolve($request),
            [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'cuando'       => $cuando,
            ]
        ));
    }

    public function show(Event $event): EventResource
    {
        $this->authorize('view', $event);

        return new EventResource($event->load('media'));
    }
}
