<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Página estática pública (historia, fauna, FAQ, etc).
     *
     * Si la página existe pero está despublicada, devolvemos 404 para
     * que no aparezca el contenido en producción cuando un editor
     * decide retirarla.
     */
    public function show(Page $page): View
    {
        abort_unless($page->published, 404);

        $page->load('media');

        return view('public.pages.show', [
            'page' => $page,
        ]);
    }
}
