<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsletterCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('newsletter_campaigns.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'subject'      => ['required', 'string', 'max:300'],
            'body_html'    => ['required', 'string'],
            'body_text'    => ['nullable', 'string'],
            'status'       => ['nullable', Rule::in(['draft', 'sending', 'sent'])],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }
}
