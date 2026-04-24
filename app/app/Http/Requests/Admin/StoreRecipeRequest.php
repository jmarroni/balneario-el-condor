<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('recipes.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:200'],
            'slug'         => ['nullable', 'string', 'max:255', 'unique:recipes,slug'],
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
