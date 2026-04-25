<?php

declare(strict_types=1);

namespace App\Http\Resources;

class RentalResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'places'       => $this->places,
            'contact_name' => $this->contact_name,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'address'      => $this->address,
            'description'  => $this->description,
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
                'self' => route('api.v1.rentals.show', $this->slug),
            ],
        ];
    }
}
