<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PageResource;
use App\Models\Page;

class PageController extends Controller
{
    public function show(Page $page): PageResource
    {
        $this->authorize('view', $page);

        return new PageResource($page->load('media'));
    }
}
