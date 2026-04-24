<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('surveys.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:200'],
            'question'         => ['required', 'string'],
            'options'          => ['required', 'array', 'min:2'],
            'options.*.key'    => ['required', 'integer'],
            'options.*.label'  => ['required', 'string', 'max:200'],
            'active'           => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
        ]);
    }
}
