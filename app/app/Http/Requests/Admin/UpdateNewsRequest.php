<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('news.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $news = $this->route('news');
        $newsId = is_object($news) ? $news->getKey() : $news;

        return [
            'title'            => ['required', 'string', 'max:200'],
            'slug'             => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($newsId)],
            'body'             => ['required', 'string'],
            'news_category_id' => ['nullable', 'integer', 'exists:news_categories,id'],
            'video_url'        => ['nullable', 'string', 'max:500'],
            'published_at'     => ['nullable', 'date'],
            'views'            => ['nullable', 'integer', 'min:0'],
        ];
    }
}
