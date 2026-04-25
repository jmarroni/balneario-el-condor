<?php

declare(strict_types=1);

namespace App\Http\Resources;

class GalleryImageResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'url'           => $this->path ? url('storage/'.$this->path) : null,
            'thumb_url'     => $this->thumb_path ? url('storage/'.$this->thumb_path) : null,
            'original_url'  => $this->original_path ? url('storage/'.$this->original_path) : null,
            'taken_on'      => optional($this->taken_on)->toDateString(),
            'views'         => $this->views,
        ];
    }
}
