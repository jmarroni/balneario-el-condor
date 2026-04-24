<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('service_providers.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $provider = $this->route('serviceProvider');
        $id = is_object($provider) ? $provider->getKey() : $provider;

        return [
            'name'          => ['required', 'string', 'max:200'],
            'slug'          => ['nullable', 'string', 'max:255', Rule::unique('service_providers', 'slug')->ignore($id)],
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
