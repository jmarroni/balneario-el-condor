<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassifiedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('classifieds.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:classifieds,slug'],
            'description' => ['required', 'string'],
            'classified_category_id' => ['nullable', 'integer', 'exists:classified_categories,id'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:200'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'views' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
