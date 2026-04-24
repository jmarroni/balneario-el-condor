<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pages.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug'             => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'title'            => ['required', 'string', 'max:200'],
            'content'          => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published'        => ['nullable', 'boolean'],
        ];
    }
}
