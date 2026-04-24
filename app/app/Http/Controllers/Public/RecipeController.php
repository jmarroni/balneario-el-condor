<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecipeController extends Controller
{
    /**
     * Recetario público — listado con búsqueda por título / autor.
     */
    public function index(Request $request): View
    {
        $base = Recipe::query()
            ->with('media')
            ->latest('published_on')
            ->orderByDesc('id');

        $q = $request->string('q')->toString() ?: null;

        if ($q !== null) {
            $base->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('ingredients', 'like', "%{$q}%");
            });
        }

        return view('public.recetas.index', [
            'recipes'    => $base->paginate(12)->withQueryString(),
            'q'          => $q,
            'totalCount' => Recipe::count(),
        ]);
    }

    /**
     * Detalle editorial de la receta — split de ingredientes / preparación.
     */
    public function show(Recipe $recipe): View
    {
        $recipe->load('media');

        $related = Recipe::query()
            ->where('id', '!=', $recipe->id)
            ->with('media')
            ->latest('published_on')
            ->limit(3)
            ->get();

        return view('public.recetas.show', [
            'recipe'  => $recipe,
            'related' => $related,
        ]);
    }
}
