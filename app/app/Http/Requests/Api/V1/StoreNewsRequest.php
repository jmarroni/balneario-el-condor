<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

/**
 * Reusa los rules y authorize() del FormRequest admin.
 * El authorize() ya chequea $this->user()?->can('news.create'),
 * lo que funciona idénticamente para Sanctum (inyecta el usuario).
 */
class StoreNewsRequest extends \App\Http\Requests\Admin\StoreNewsRequest
{
}
