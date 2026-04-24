<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pages.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $page = $this->route('page');
        // Page uses slug as route key; fall back to id if we actually hold the model
        $pageId = is_object($page) ? $page->getKey() : null;

        $uniqueRule = Rule::unique('pages', 'slug');
        if ($pageId !== null) {
            $uniqueRule = $uniqueRule->ignore($pageId);
        }

        return [
            'slug'             => ['required', 'string', 'max:255', $uniqueRule],
            'title'            => ['required', 'string', 'max:200'],
            'content'          => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published'        => ['nullable', 'boolean'],
        ];
    }
}
