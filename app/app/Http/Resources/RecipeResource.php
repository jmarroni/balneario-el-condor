<?php

declare(strict_types=1);

namespace App\Http\Resources;

class RecipeResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'prep_minutes' => $this->prep_minutes,
            'cook_minutes' => $this->cook_minutes,
            'servings'     => $this->servings,
            'cost'         => $this->cost,
            'ingredients'  => $this->ingredients,
            'instructions' => $this->instructions,
            'author'       => $this->author,
            'published_on' => optional($this->published_on)->toDateString(),
            'media' => $this->whenLoaded(
                'media',
                fn () => $this->media->map(fn ($m) => [
                    'url'        => url('storage/'.$m->path),
                    'alt'        => $m->alt,
                    'type'       => $m->type,
                    'sort_order' => $m->sort_order,
                ])->all()
            ),
            'links' => [
                'self' => route('api.v1.recipes.show', $this->slug),
            ],
        ];
    }
}
