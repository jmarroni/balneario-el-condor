<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('recipes.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $recipe = $this->route('recipe');
        $recipeId = is_object($recipe) ? $recipe->getKey() : $recipe;

        return [
            'title'        => ['required', 'string', 'max:200'],
            'slug'         => ['nullable', 'string', 'max:255', Rule::unique('recipes', 'slug')->ignore($recipeId)],
            'prep_minutes' => ['nullable', 'integer', 'min:0'],
            'cook_minutes' => ['nullable', 'integer', 'min:0'],
            'servings'     => ['nullable', 'string', 'max:100'],
            'cost'         => ['nullable', 'string', 'max:100'],
            'ingredients'  => ['required', 'string'],
            'instructions' => ['required', 'string'],
            'author'       => ['nullable', 'string', 'max:200'],
            'published_on' => ['nullable', 'date'],
        ];
    }
}
