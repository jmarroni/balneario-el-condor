<?php

declare(strict_types=1);

namespace App\Http\Resources;

class ClassifiedResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'contact_name'  => $this->contact_name,
            'contact_email' => $this->contact_email,
            'address'       => $this->address,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'video_url'     => $this->video_url,
            'views'         => $this->views,
            'published_at'  => optional($this->published_at)->toIso8601String(),
            'category'      => $this->whenLoaded('category', fn () => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]),
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
                'self' => route('api.v1.classifieds.show', $this->slug),
            ],
        ];
    }
}
