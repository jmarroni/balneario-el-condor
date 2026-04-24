<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTideRequest extends FormRequest
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
            'location' => ['nullable', 'string', 'max:100'],
            'date'     => [
                'required', 'date',
                Rule::unique('tides', 'date')->where(function ($q) {
                    $q->where('location', $this->input('location', 'El Cóndor'));
                }),
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
