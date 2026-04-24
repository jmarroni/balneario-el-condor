<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('service_providers.create') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:200'],
            'slug'          => ['nullable', 'string', 'max:255', 'unique:service_providers,slug'],
            'description'   => ['nullable', 'string'],
            'contact_name'  => ['nullable', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:200'],
            'phone'         => ['nullable', 'string', 'max:100'],
            'address'       => ['nullable', 'string', 'max:500'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
