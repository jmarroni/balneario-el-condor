<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classified;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Lodging;
use App\Models\News;
use App\Models\Recipe;
use App\Models\Rental;
use App\Models\Tide;
use App\Models\UsefulInfo;
use App\Models\Venue;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'featuredNews' => News::whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->first(),

            'upcomingEvents' => Event::where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(3)
                ->get(),

            'latestImages' => GalleryImage::latest()->limit(4)->get(),

            'latestClassifieds' => Classified::whereNotNull('published_at')
                ->latest('published_at')
                ->limit(4)
                ->get(),

            'todayTide' => Tide::whereDate('date', today())->first()
                ?? Tide::where('date', '>=', today())->orderBy('date')->first(),

            'stats' => [
                'lodgings' => Lodging::count(),
                'venues'   => Venue::count(),
                'rentals'  => Rental::count(),
                'recipes'  => Recipe::count(),
                'gallery'  => GalleryImage::count(),
            ],

            'infoTop' => UsefulInfo::orderBy('sort_order')->limit(4)->get(),
        ]);
    }
}
