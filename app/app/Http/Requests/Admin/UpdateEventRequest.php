<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('events.update') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $event = $this->route('event');
        $eventId = is_object($event) ? $event->getKey() : $event;

        return [
            'title'                 => ['required', 'string', 'max:200'],
            'slug'                  => ['nullable', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($eventId)],
            'description'           => ['nullable', 'string'],
            'location'              => ['nullable', 'string', 'max:500'],
            'starts_at'             => ['nullable', 'date'],
            'ends_at'               => ['nullable', 'date', 'after_or_equal:starts_at'],
            'all_day'               => ['nullable', 'boolean'],
            'featured'              => ['nullable', 'boolean'],
            'accepts_registrations' => ['nullable', 'boolean'],
            'external_url'          => ['nullable', 'string', 'max:500'],
            'sort_order'            => ['nullable', 'integer', 'min:0'],
        ];
    }
}
