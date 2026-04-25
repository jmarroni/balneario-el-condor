<?php

declare(strict_types=1);

namespace App\Http\Resources;

class TideResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'location'           => $this->location,
            'date'               => optional($this->date)->toDateString(),
            'first_high'         => $this->first_high,
            'first_high_height'  => $this->first_high_height,
            'first_low'          => $this->first_low,
            'first_low_height'   => $this->first_low_height,
            'second_high'        => $this->second_high,
            'second_high_height' => $this->second_high_height,
            'second_low'         => $this->second_low,
            'second_low_height'  => $this->second_low_height,
        ];
    }
}
