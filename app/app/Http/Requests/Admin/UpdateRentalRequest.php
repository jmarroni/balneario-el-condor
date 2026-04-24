<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('rentals.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rental = $this->route('rental');
        $id = is_object($rental) ? $rental->getKey() : $rental;

        return [
            'title'        => ['required', 'string', 'max:200'],
            'slug'         => ['nullable', 'string', 'max:255', Rule::unique('rentals', 'slug')->ignore($id)],
            'places'       => ['nullable', 'integer', 'min:0'],
            'contact_name' => ['nullable', 'string', 'max:200'],
            'phone'        => ['nullable', 'string', 'max:100'],
            'email'        => ['nullable', 'email', 'max:200'],
            'address'      => ['nullable', 'string', 'max:500'],
            'description'  => ['nullable', 'string'],
        ];
    }
}
