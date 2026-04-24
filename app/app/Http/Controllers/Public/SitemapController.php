<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classified;
use App\Models\Event;
use App\Models\Lodging;
use App\Models\NearbyPlace;
use App\Models\News;
use App\Models\Page;
use App\Models\Recipe;
use App\Models\Rental;
use App\Models\ServiceProvider;
use App\Models\Venue;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(24), fn () => $this->build());

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function build(): string
    {
        $urls = new Collection();

        // Home y secciones estáticas (índices)
        $static = [
            ['route' => 'home',                  'priority' => 1.0, 'changefreq' => 'daily'],
            ['route' => 'novedades.index',       'priority' => 0.9, 'changefreq' => 'daily'],
            ['route' => 'eventos.index',         'priority' => 0.9, 'changefreq' => 'daily'],
            ['route' => 'hospedajes.index',      'priority' => 0.8, 'changefreq' => 'weekly'],
            ['route' => 'gastronomia.index',     'priority' => 0.8, 'changefreq' => 'weekly'],
            ['route' => 'alquileres.index',      'priority' => 0.7, 'changefreq' => 'weekly'],
            ['route' => 'servicios.index',       'priority' => 0.6, 'changefreq' => 'monthly'],
            ['route' => 'cercanos.index',        'priority' => 0.6, 'changefreq' => 'monthly'],
            ['route' => 'info-util.index',       'priority' => 0.6, 'changefreq' => 'monthly'],
            ['route' => 'clasificados.index',    'priority' => 0.7, 'changefreq' => 'daily'],
            ['route' => 'galeria.index',         'priority' => 0.6, 'changefreq' => 'weekly'],
            ['route' => 'recetas.index',         'priority' => 0.6, 'changefreq' => 'monthly'],
            ['route' => 'mareas.index',          'priority' => 0.8, 'changefreq' => 'daily'],
            ['route' => 'clima.index',           'priority' => 0.5, 'changefreq' => 'daily'],
            ['route' => 'contacto.show',         'priority' => 0.4, 'changefreq' => 'yearly'],
            ['route' => 'newsletter.form',       'priority' => 0.3, 'changefreq' => 'yearly'],
            ['route' => 'publicite.show',        'priority' => 0.3, 'changefreq' => 'yearly'],
        ];

        foreach ($static as $entry) {
            $urls->push([
                'loc'        => route($entry['route']),
                'priority'   => $entry['priority'],
                'changefreq' => $entry['changefreq'],
            ]);
        }

        // Detalles dinámicos: novedades publicadas
        News::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(fn ($n) => $urls->push([
                'loc'        => route('novedades.show', $n),
                'lastmod'    => optional($n->updated_at)?->toIso8601String(),
                'priority'   => 0.7,
                'changefreq' => 'monthly',
            ]));

        Event::get()->each(fn ($e) => $urls->push([
            'loc'        => route('eventos.show', $e),
            'lastmod'    => optional($e->updated_at)?->toIso8601String(),
            'priority'   => 0.7,
            'changefreq' => 'weekly',
        ]));

        Lodging::get()->each(fn ($l) => $urls->push([
            'loc'        => route('hospedajes.show', $l),
            'lastmod'    => optional($l->updated_at)?->toIso8601String(),
            'priority'   => 0.6,
            'changefreq' => 'monthly',
        ]));

        Venue::get()->each(fn ($v) => $urls->push([
            'loc'        => route('gastronomia.show', $v),
            'lastmod'    => optional($v->updated_at)?->toIso8601String(),
            'priority'   => 0.6,
            'changefreq' => 'monthly',
        ]));

        Rental::get()->each(fn ($r) => $urls->push([
            'loc'        => route('alquileres.show', $r),
            'lastmod'    => optional($r->updated_at)?->toIso8601String(),
            'priority'   => 0.5,
            'changefreq' => 'monthly',
        ]));

        ServiceProvider::get()->each(fn ($s) => $urls->push([
            'loc'        => route('servicios.show', $s),
            'lastmod'    => optional($s->updated_at)?->toIso8601String(),
            'priority'   => 0.4,
            'changefreq' => 'monthly',
        ]));

        NearbyPlace::get()->each(fn ($p) => $urls->push([
            'loc'        => route('cercanos.show', $p),
            'lastmod'    => optional($p->updated_at)?->toIso8601String(),
            'priority'   => 0.4,
            'changefreq' => 'monthly',
        ]));

        Classified::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(fn ($c) => $urls->push([
                'loc'        => route('clasificados.show', $c),
                'lastmod'    => optional($c->updated_at)?->toIso8601String(),
                'priority'   => 0.5,
                'changefreq' => 'weekly',
            ]));

        Recipe::get()->each(fn ($r) => $urls->push([
            'loc'        => route('recetas.show', $r),
            'lastmod'    => optional($r->updated_at)?->toIso8601String(),
            'priority'   => 0.4,
            'changefreq' => 'yearly',
        ]));

        Page::where('published', true)->get()->each(fn ($p) => $urls->push([
            'loc'        => route('pages.show', $p),
            'lastmod'    => optional($p->updated_at)?->toIso8601String(),
            'priority'   => 0.5,
            'changefreq' => 'monthly',
        ]));

        return view('public.sitemap', ['urls' => $urls])->render();
    }
}
