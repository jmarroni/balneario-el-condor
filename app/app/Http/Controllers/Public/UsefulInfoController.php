<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\UsefulInfo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsefulInfoController extends Controller
{
    /**
     * Solo index: directorio telefónico de servicios públicos / utilidad. Sin show.
     */
    public function index(Request $request): View
    {
        $base = UsefulInfo::query()->orderBy('sort_order')->orderBy('title');

        if ($q = $request->string('q')->toString()) {
            $base->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        return view('public.info-util.index', [
            'items' => $base->limit(48)->get(),
            'q'     => $q,
        ]);
    }
}
