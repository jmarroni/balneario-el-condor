<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('news.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:200'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:news,slug'],
            'body'             => ['required', 'string'],
            'news_category_id' => ['nullable', 'integer', 'exists:news_categories,id'],
            'video_url'        => ['nullable', 'string', 'max:500'],
            'published_at'     => ['nullable', 'date'],
            'views'            => ['nullable', 'integer', 'min:0'],
        ];
    }
}
