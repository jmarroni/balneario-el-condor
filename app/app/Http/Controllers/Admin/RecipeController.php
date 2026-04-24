<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecipeRequest;
use App\Http\Requests\Admin\UpdateRecipeRequest;
use App\Models\Recipe;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Recipe::class);

        $recipes = Recipe::query()
            ->orderByDesc('published_on')
            ->paginate(20);

        return view('admin.recipes.index', compact('recipes'));
    }

    public function create(): View
    {
        $this->authorize('create', Recipe::class);

        return view('admin.recipes.create', [
            'recipe' => new Recipe(),
        ]);
    }

    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title']);

        $recipe = Recipe::create($data);

        return redirect()
            ->route('admin.recipes.edit', $recipe)
            ->with('success', 'Receta creada.');
    }

    public function show(Recipe $recipe): RedirectResponse
    {
        $this->authorize('view', $recipe);

        return redirect()->route('admin.recipes.edit', $recipe);
    }

    public function edit(Recipe $recipe): View
    {
        $this->authorize('update', $recipe);

        return view('admin.recipes.edit', [
            'recipe' => $recipe,
        ]);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $recipe->update($data);

        return redirect()
            ->route('admin.recipes.edit', $recipe)
            ->with('success', 'Receta actualizada.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $this->authorize('delete', $recipe);

        $recipe->delete();

        return redirect()
            ->route('admin.recipes.index')
            ->with('success', 'Receta eliminada.');
    }
}
