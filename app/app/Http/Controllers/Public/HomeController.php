<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\News;
use App\Models\Tide;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'featuredNews' => News::latest('published_at')->limit(1)->first(),
            'upcomingEvents' => Event::where('starts_at', '>=', now())
                ->orderBy('starts_at')->limit(3)->get(),
            'todayTides' => Tide::whereDate('date', today())->first(),
        ]);
    }
}
