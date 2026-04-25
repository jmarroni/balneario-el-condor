<?php

declare(strict_types=1);

namespace App\Http\Resources;

class UsefulInfoResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'phone'      => $this->phone,
            'website'    => $this->website,
            'email'      => $this->email,
            'address'    => $this->address,
            'latitude'   => $this->latitude,
            'longitude'  => $this->longitude,
            'sort_order' => $this->sort_order,
        ];
    }
}
