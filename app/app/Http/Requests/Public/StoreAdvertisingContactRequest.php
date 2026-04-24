<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdvertisingContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:200'],
            'last_name'        => ['nullable', 'string', 'max:200'],
            'email'            => ['required', 'email', 'max:200'],
            'message'          => ['required', 'string', 'min:20', 'max:3000'],
            'zone'             => ['nullable', 'string', 'in:home-top,sidebar,footer,events-page,other'],
            'captcha_honeypot' => ['nullable', 'prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'    => 'Por favor ingresá tu nombre.',
            'email.required'   => 'Necesitamos un email para responderte.',
            'email.email'      => 'El email no parece válido.',
            'message.required' => 'Contanos qué te interesa publicitar.',
            'message.min'      => 'El mensaje es muy corto. Mínimo 20 caracteres.',
            'message.max'      => 'El mensaje es muy largo. Máximo 3000 caracteres.',
            'zone.in'          => 'Elegí una zona válida del listado.',
        ];
    }
}
