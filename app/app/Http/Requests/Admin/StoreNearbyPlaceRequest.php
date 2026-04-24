<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNearbyPlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('nearby_places.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:200'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:nearby_places,slug'],
            'description' => ['nullable', 'string'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'views'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
