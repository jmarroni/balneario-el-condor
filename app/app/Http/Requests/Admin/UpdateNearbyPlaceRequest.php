<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNearbyPlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('nearby_places.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $place = $this->route('nearbyPlace');
        $id = is_object($place) ? $place->getKey() : $place;

        return [
            'title'       => ['required', 'string', 'max:200'],
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('nearby_places', 'slug')->ignore($id)],
            'description' => ['nullable', 'string'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'views'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
