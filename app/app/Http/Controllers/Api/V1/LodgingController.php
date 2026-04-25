<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LodgingResource;
use App\Models\Lodging;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Alojamientos
 *
 * Directorio de alojamientos turísticos: hoteles, hosterías, departamentos y campings.
 */
class LodgingController extends Controller
{
    /**
     * Listar alojamientos
     *
     * @queryParam q string Búsqueda parcial por nombre. Example: hostería
     * @queryParam type string Filtrar por tipo: hotel | hosteria | departamento | camping. Example: hotel
     * @queryParam per_page integer Cantidad por página. Example: 20
     */
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

    /**
     * Mostrar alojamiento
     *
     * @urlParam lodging string required Slug del alojamiento. Example: hosteria-del-mar
     */
    public function show(Lodging $lodging): LodgingResource
    {
        $this->authorize('view', $lodging);

        return new LodgingResource($lodging->load('media'));
    }
}
