<?php

declare(strict_types=1);

namespace App\Http\Resources;

class ServiceProviderResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'contact_name'  => $this->contact_name,
            'contact_email' => $this->contact_email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
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
