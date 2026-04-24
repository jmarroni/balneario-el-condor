<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassifiedContactRequest extends FormRequest
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
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:200'],
            'phone'   => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'    => 'Por favor ingresá tu nombre.',
            'email.required'   => 'Necesitamos un email para que el anunciante pueda responderte.',
            'email.email'      => 'El email no parece válido.',
            'message.required' => 'Escribí un mensaje para el anunciante.',
            'message.min'      => 'El mensaje es muy corto. Mínimo 10 caracteres.',
            'message.max'      => 'El mensaje es muy largo. Máximo 2000 caracteres.',
        ];
    }
}
