<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassifiedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('classifieds.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $classified = $this->route('classified');
        $id = is_object($classified) ? $classified->getKey() : $classified;

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('classifieds', 'slug')->ignore($id)],
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
