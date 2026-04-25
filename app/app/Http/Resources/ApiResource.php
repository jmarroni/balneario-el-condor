<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiResource extends JsonResource
{
    public function with($request): array
    {
        return [
            'meta' => [
                'version'      => 'v1',
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
