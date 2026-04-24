<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('venues.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $venue = $this->route('venue');
        $id = is_object($venue) ? $venue->getKey() : $venue;

        return [
            'name'        => ['required', 'string', 'max:200'],
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('venues', 'slug')->ignore($id)],
            'category'    => ['required', Rule::in(['gourmet', 'nightlife'])],
            'description' => ['nullable', 'string'],
            'phone'       => ['nullable', 'string', 'max:100'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'views'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
