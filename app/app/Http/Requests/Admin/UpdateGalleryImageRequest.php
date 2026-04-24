<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGalleryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gallery.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $galleryImage = $this->route('galleryImage');
        $id = is_object($galleryImage) ? $galleryImage->getKey() : $galleryImage;

        return [
            'title' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('gallery_images', 'slug')->ignore($id)],
            'description' => ['nullable', 'string'],
            'taken_on' => ['nullable', 'date'],
            'image' => ['nullable', 'file', 'image', 'max:10240'],
        ];
    }
}
