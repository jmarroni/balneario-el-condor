<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

/**
 * Endpoint público de contacto para apps externas.
 * Reusa los rules del FormRequest del frontend público (Task 8 Fase 5):
 * name/email/phone/subject/message + honeypot anti-bot.
 */
class StoreContactMessageRequest extends \App\Http\Requests\Public\StoreContactMessageRequest
{
}
