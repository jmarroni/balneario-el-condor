<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLodgingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('lodgings.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:200'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:lodgings,slug'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', Rule::in(['hotel', 'casa', 'camping', 'hostel', 'other'])],
            'website'     => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:200'],
            'phone'       => ['nullable', 'string', 'max:100'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'views'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
