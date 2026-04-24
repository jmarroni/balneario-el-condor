<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gallery.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:gallery_images,slug'],
            'description' => ['nullable', 'string'],
            'taken_on' => ['nullable', 'date'],
            'image' => ['required', 'file', 'image', 'max:10240'],
        ];
    }
}
