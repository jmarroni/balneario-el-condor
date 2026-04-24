<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
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
            'email'            => ['required', 'email', 'max:200'],
            'phone'            => ['nullable', 'string', 'max:100'],
            'subject'          => ['nullable', 'string', 'max:300'],
            'message'          => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot anti-bot: el campo debe venir vacío.
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
            'message.required' => 'Escribí el mensaje que querés enviarnos.',
            'message.min'      => 'El mensaje es muy corto. Mínimo 10 caracteres.',
            'message.max'      => 'El mensaje es muy largo. Máximo 5000 caracteres.',
        ];
    }
}
