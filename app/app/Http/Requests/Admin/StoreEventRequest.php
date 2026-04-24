<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('events.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'                 => ['required', 'string', 'max:200'],
            'slug'                  => ['nullable', 'string', 'max:255', 'unique:events,slug'],
            'description'           => ['nullable', 'string'],
            'location'              => ['nullable', 'string', 'max:500'],
            'starts_at'             => ['nullable', 'date'],
            'ends_at'               => ['nullable', 'date', 'after_or_equal:starts_at'],
            'all_day'               => ['nullable', 'boolean'],
            'featured'              => ['nullable', 'boolean'],
            'accepts_registrations' => ['nullable', 'boolean'],
            'external_url'          => ['nullable', 'string', 'max:500'],
            'sort_order'            => ['nullable', 'integer', 'min:0'],
        ];
    }
}
