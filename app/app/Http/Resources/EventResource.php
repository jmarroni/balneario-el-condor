<?php

declare(strict_types=1);

namespace App\Http\Resources;

class EventResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'location'              => $this->location,
            'starts_at'             => optional($this->starts_at)->toIso8601String(),
            'ends_at'               => optional($this->ends_at)->toIso8601String(),
            'all_day'               => (bool) $this->all_day,
            'featured'              => (bool) $this->featured,
            'accepts_registrations' => (bool) $this->accepts_registrations,
            'external_url'          => $this->external_url,
            'is_past'               => $this->is_past,
            'is_upcoming'           => $this->is_upcoming,
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
                'self' => route('api.v1.events.show', $this->slug),
            ],
        ];
    }
}
