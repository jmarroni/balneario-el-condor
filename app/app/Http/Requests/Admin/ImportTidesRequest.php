<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportTidesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tides.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'file'     => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'location' => ['nullable', 'string', 'max:100'],
        ];
    }
}
