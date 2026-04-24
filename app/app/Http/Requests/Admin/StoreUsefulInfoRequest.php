<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsefulInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('useful_info.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:200'],
            'phone'      => ['nullable', 'string', 'max:100'],
            'website'    => ['nullable', 'string', 'max:255'],
            'email'      => ['nullable', 'email', 'max:200'],
            'address'    => ['nullable', 'string', 'max:500'],
            'latitude'   => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'  => ['nullable', 'numeric', 'between:-180,180'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
