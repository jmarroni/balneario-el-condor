<?php

declare(strict_types=1);

namespace App\Http\Resources;

class NearbyPlaceResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'description' => $this->description,
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
        ];
    }
}
