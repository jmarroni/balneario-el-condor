<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tides.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $tide = $this->route('tide');
        $id = is_object($tide) ? $tide->getKey() : $tide;

        return [
            'location' => ['nullable', 'string', 'max:100'],
            'date'     => [
                'required', 'date',
                Rule::unique('tides', 'date')
                    ->where(fn ($q) => $q->where('location', $this->input('location', 'El Cóndor')))
                    ->ignore($id),
            ],
            'first_high'         => ['nullable', 'date_format:H:i'],
            'first_high_height'  => ['nullable', 'string', 'max:20'],
            'first_low'          => ['nullable', 'date_format:H:i'],
            'first_low_height'   => ['nullable', 'string', 'max:20'],
            'second_high'        => ['nullable', 'date_format:H:i'],
            'second_high_height' => ['nullable', 'string', 'max:20'],
            'second_low'         => ['nullable', 'date_format:H:i'],
            'second_low_height'  => ['nullable', 'string', 'max:20'],
        ];
    }
}
