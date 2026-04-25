<?php

declare(strict_types=1);

namespace App\Http\Resources;

class LodgingResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'type'        => $this->type,
            'website'     => $this->website,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'address'     => $this->address,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'views'       => $this->views,
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
                'self' => route('api.v1.lodgings.show', $this->slug),
            ],
        ];
    }
}
