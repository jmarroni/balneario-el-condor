<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\AbstractPaginator;

abstract class Controller extends \App\Http\Controllers\Controller
{
    use AuthorizesRequests;

    protected function envelope($data, array $meta = []): array
    {
        if ($data instanceof AbstractPaginator) {
            return [
                'data' => $data->items(),
                'meta' => array_merge([
                    'total'        => $data->total(),
                    'per_page'     => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                ], $meta),
            ];
        }

        return ['data' => $data, 'meta' => $meta];
    }
}
