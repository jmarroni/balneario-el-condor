<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreClassifiedContactRequest;
use App\Models\Classified;
use App\Models\ClassifiedCategory;
use App\Models\ClassifiedContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassifiedController extends Controller
{
    /**
     * Listado público de clasificados con filtro por categoría y búsqueda.
     */
    public function index(Request $request): View
    {
        $base = Classified::query()
            ->with(['category', 'media'])
            ->latest('published_at')
            ->orderByDesc('id');

        $categorySlug = $request->string('categoria')->toString() ?: null;

        if ($categorySlug !== null) {
            $base->whereHas(
                'category',
                fn ($q) => $q->where('slug', $categorySlug)
            );
        }

        $q = $request->string('q')->toString() ?: null;

        if ($q !== null) {
            $base->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Conteos por categoría (sobre el set total, sin filtros).
        $counts = Classified::query()
            ->selectRaw('classified_category_id, COUNT(*) as total')
            ->groupBy('classified_category_id')
            ->pluck('total', 'classified_category_id')
            ->toArray();

        return view('public.clasificados.index', [
            'items'      => $base->paginate(12)->withQueryString(),
            'categories' => ClassifiedCategory::orderBy('name')->get(),
            'current'    => $categorySlug,
            'counts'     => $counts,
            'q'          => $q,
            'totalCount' => Classified::count(),
        ]);
    }

    /**
     * Ficha de un clasificado con form de contacto al anunciante.
     */
    public function show(Classified $classified): View
    {
        $classified->load(['category', 'media']);
        $classified->increment('views');

        $related = Classified::query()
            ->where('id', '!=', $classified->id)
            ->when(
                $classified->classified_category_id,
                fn ($q) => $q->where('classified_category_id', $classified->classified_category_id)
            )
            ->with(['category', 'media'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.clasificados.show', [
            'item'    => $classified,
            'related' => $related,
        ]);
    }

    /**
     * POST /clasificados/{classified:slug}/contacto
     *
     * Crea un ClassifiedContact con destination_email = $classified->contact_email
     * (al owner). El envío real de mail viene en Plan 6.
     */
    public function storeContact(
        StoreClassifiedContactRequest $request,
        Classified $classified
    ): RedirectResponse {
        $data = $request->validated();

        ClassifiedContact::create([
            'classified_id'     => $classified->id,
            'contact_name'      => $data['name'],
            'contact_email'     => $data['email'],
            'contact_phone'     => $data['phone'] ?? null,
            'message'           => $data['message'],
            'destination_email' => $classified->contact_email,
            'ip_address'        => $request->ip(),
            'legacy_id'         => null,
        ]);

        return redirect()
            ->route('clasificados.show', $classified)
            ->with('success', '¡Mensaje enviado! El anunciante te responderá pronto.');
    }
}
