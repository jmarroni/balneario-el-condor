<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreEventRequest;
use App\Http\Requests\Api\V1\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @group Eventos
 *
 * Gestión de eventos y agenda cultural del Balneario.
 */
class EventController extends Controller
{
    /**
     * Listar eventos
     *
     * Devuelve un listado paginado de eventos. Por defecto trae los próximos.
     *
     * @queryParam q string Búsqueda parcial por título. Example: feria
     * @queryParam cuando string Filtra por temporalidad: "proximos" | "pasados". Example: proximos
     * @queryParam per_page integer Cantidad de items por página (default 20). Example: 20
     */
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

    /**
     * Mostrar evento
     *
     * Devuelve un evento identificado por slug.
     *
     * @urlParam event string required Slug del evento. Example: feria-del-libro-2026
     */
    public function show(Event $event): EventResource
    {
        $this->authorize('view', $event);

        return new EventResource($event->load('media'));
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug((string) $data['title']);

        $event = Event::create($data);

        return (new EventResource($event->load('media')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        $event->update($data);

        return new EventResource($event->fresh()->load('media'));
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return response()->json(null, 204);
    }
}
