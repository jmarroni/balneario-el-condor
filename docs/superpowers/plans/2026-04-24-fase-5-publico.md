# Fase 5 — Sitio Público

**Fuente:** [spec §7 Routing + §10 Integraciones](../specs/2026-04-21-migracion-laravel-design.md)
**Design preview:** [`design-preview/fase-5-home.html`](../../../design-preview/fase-5-home.html) — mockup estandalone con toda la dirección visual
**Prerequisitos:** Fase 4 mergeada (236 tests verdes, admin CRUDs completos).
**Meta:** sitio público con diseño editorial costero patagónico, rutas en español, integración Open-Meteo para clima, SEO básico y tests smoke por módulo.

## Dirección de diseño (comprometida)

**Concepto:** editorial costera patagónica — magazine moderno con alma de afiche de viajes antiguo. Ni luxury ni template genérico. Cálido, familiar, específico.

**Tipografía:**
- **Fraunces** (variable, ejes `opsz` 9-144 y `SOFT` 0-100) — display serif con personalidad artesanal. Se usa grande con `opsz: 144` y pequeño con `opsz: 14-24`. Para italic acentuado (taglines), `SOFT: 100`.
- **Instrument Sans** (variable) — cuerpo humanista, 400-700.
- **JetBrains Mono** — datos: horarios de mareas, coordenadas, grados de viento, contadores. Le da lenguaje náutico/instrumental al UI.

**Paleta:**

| Token | HEX | Uso |
|---|---|---|
| `ink` | `#0f2d5c` | Texto principal, fondos oscuros destacados |
| `ink-2` | `#1e40af` | Azul del logo, acentos secundarios |
| `ink-soft` | `#3c5a84` | Texto secundario |
| `sun` | `#d89b2a` | Ocre cálido (el amarillo del logo, envejecido por la sal) |
| `sun-deep` | `#a8751a` | Sun más profundo para texto sobre sand |
| `coral` | `#c85a3c` | Acento de calor, CTAs, enfatiza italics |
| `coral-soft` | `#e28566` | Hover de coral |
| `sand` | `#faf3e3` | Fondo dominante (crema cálida) |
| `sand-2` | `#f1e5c9` | Fondo de secciones secundarias |
| `foam` | `#ffffff` | Cards, surfaces |
| `ink-line` | `rgba(15,45,92,0.15)` | Borders sutiles |

**Principios visuales:**
1. **Asimetría editorial.** Grid magazine con cards de distinto peso — no uniformidad.
2. **Datos como decoración.** Mareas, clima y coordenadas con tipografía monospace, tratados como instrumentos náuticos.
3. **Grano sutil** sobre toda la página (SVG fractal noise 0.35 opacity, multiply blend).
4. **Fotos con ligera rotación** (-1.2deg, 4deg) tipo polaroid para lo destacado.
5. **Divisores SVG de olas** hechos a mano entre secciones mayores.
6. **Stamps/fechas** para eventos tratados como sellos de correo (ocre sobre navy).
7. **Hover states intencionales:** links con underline animada coral, cards con lift -4px + shadow.
8. **Transiciones de entrada staggered** (`animation-delay` 0.1s → 0.85s en hero).

**Anti-patterns a evitar:**
- Fonts genéricos (Inter, Roboto, system) — usar Fraunces + Instrument Sans.
- Gradientes morados sobre blanco.
- Cards uniformes alineadas en grid 3×N.
- Iconos lucide/heroicons sin personalidad — usar SVG inline con stroke personalizado.
- Dark mode por defecto (no aplica aquí — el cream es identidad).

---

## Estado inicial

Ya existe de Fase 1:
- `routes/web.php` con `/` → `welcome.blade.php` (default Breeze).
- Tailwind 3 configurado con `tailwind.config.js` default.
- Vite building `resources/js/app.js` + `resources/css/app.css`.
- `resources/views/welcome.blade.php` (default Laravel).
- Models con data real post-ETL (Fase 3).

Falta todo el sitio público.

---

## Task 1: Foundation pública — theme + layout + nav + footer

**Archivos:**
- Modify: `app/tailwind.config.js` — theme extendido
- Modify: `app/resources/css/app.css` — fonts + grain + base
- Create: `app/resources/views/components/public/layouts/main.blade.php`
- Create: `app/resources/views/components/public/nav.blade.php`
- Create: `app/resources/views/components/public/footer.blade.php`
- Create: `app/resources/views/components/public/wave-divider.blade.php`
- Create: `app/app/Http/Controllers/Public/HomeController.php`
- Create: `app/app/View/Composers/PublicComposer.php` (weather + nav state)
- Modify: `app/routes/web.php` — ruta `/` → HomeController
- Create: `app/tests/Feature/Public/PublicFoundationTest.php`

### Step 1: Theme Tailwind

Reemplazar `app/tailwind.config.js`:

```js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: {
                    DEFAULT: '#0f2d5c',
                    2: '#1e40af',
                    soft: '#3c5a84',
                    line: 'rgba(15,45,92,0.15)',
                },
                sun: {
                    DEFAULT: '#d89b2a',
                    deep: '#a8751a',
                },
                coral: {
                    DEFAULT: '#c85a3c',
                    soft: '#e28566',
                },
                sand: {
                    DEFAULT: '#faf3e3',
                    2: '#f1e5c9',
                    3: '#e8d7a9',
                },
                foam: '#ffffff',
                seaweed: '#2d4a3e',
            },
            fontFamily: {
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
                sans:    ['Instrument Sans', ...defaultTheme.fontFamily.sans],
                mono:    ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            letterSpacing: {
                tightest: '-0.035em',
                mono:     '0.18em',
            },
            boxShadow: {
                card: '0 1px 2px rgba(15,45,92,0.06), 0 20px 40px -20px rgba(15,45,92,0.18)',
                lift: '0 30px 60px -30px rgba(15,45,92,0.35)',
            },
            animation: {
                rise: 'rise 0.8s cubic-bezier(0.16, 1, 0.3, 1) backwards',
            },
            keyframes: {
                rise: {
                    from: { opacity: '0', transform: 'translateY(28px)' },
                    to:   { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [forms],
};
```

### Step 2: CSS global

Reemplazar `app/resources/css/app.css`:

```css
@import '//fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT@0,9..144,300..900,0..100;1,9..144,300..900,0..100&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=JetBrains+Mono:wght@400;500&display=swap';

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    body {
        @apply font-sans bg-sand text-ink;
        font-size: 17px;
        line-height: 1.55;
    }

    /* Grain overlay */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 100;
        opacity: 0.35;
        mix-blend-mode: multiply;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 300 300' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 0.06 0 0 0 0 0.17 0 0 0 0 0.36 0 0 0 0.25 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }
}

@layer components {
    .display-xl {
        @apply font-display font-normal leading-none tracking-tightest text-ink;
        font-variation-settings: 'opsz' 144, 'SOFT' 50;
    }
    .display-italic {
        @apply font-display italic text-coral;
        font-variation-settings: 'opsz' 144, 'SOFT' 100;
    }
    .eyebrow {
        @apply font-mono text-[11px] tracking-mono uppercase text-coral font-medium;
    }
    .btn-primary {
        @apply inline-flex items-center gap-2 px-7 py-4 bg-ink text-sand rounded font-medium transition-all;
    }
    .btn-primary:hover {
        @apply bg-coral -translate-y-0.5;
        box-shadow: 0 12px 30px -8px rgba(200, 90, 60, 0.5);
    }
    .btn-ghost {
        @apply inline-flex items-center gap-2 px-7 py-4 border-[1.5px] border-ink text-ink rounded font-medium transition-colors;
    }
    .btn-ghost:hover {
        @apply bg-ink text-sand;
    }

    .nav-link {
        @apply font-display text-ink relative transition-colors;
        font-variation-settings: 'opsz' 14, 'SOFT' 20;
    }
    .nav-link::after {
        content: '';
        @apply absolute left-0 -bottom-1 h-0.5 bg-coral transition-[right] duration-300 ease-out;
        right: 100%;
    }
    .nav-link:hover { @apply text-coral; }
    .nav-link:hover::after { right: 0; }
}
```

### Step 3: Layout component

Crear `app/resources/views/components/public/layouts/main.blade.php`:

```blade
@props([
    'title' => 'Balneario El Cóndor',
    'description' => 'Pueblo costero a 30 km de Viedma, en la desembocadura del río Negro sobre el Atlántico patagónico.',
    'image' => null,
])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} · Balneario El Cóndor</title>
    <meta name="description" content="{{ $description }}">

    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($image)
        <meta property="og:image" content="{{ $image }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body>
    <x-public.nav />

    <main>
        {{ $slot }}
    </main>

    <x-public.footer />

    @stack('scripts')
</body>
</html>
```

### Step 4: Nav component (responsive)

Crear `app/resources/views/components/public/nav.blade.php`:

```blade
@php
    $weather = cache()->get('weather:current');
    $links = [
        ['novedades.index', 'Novedades'],
        ['eventos.index', 'Eventos'],
        ['hospedajes.index', 'Hospedajes'],
        ['gastronomia.index', 'Gourmet'],
        ['mareas.index', 'Mareas'],
        ['galeria.index', 'Galería'],
    ];
@endphp
<nav x-data="{ open: false }" class="relative z-20 bg-sand border-b border-ink-line">
    <div class="max-w-[1400px] mx-auto px-5 lg:px-10 py-5 flex items-center gap-10">
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
            <img src="{{ asset('img/logo.png') }}" alt="Balneario El Cóndor"
                 class="w-[54px] h-[54px] object-contain drop-shadow-[0_1px_2px_rgba(15,45,92,0.2)]">
            <div class="flex flex-col leading-none">
                <span class="font-mono text-[10px] tracking-[0.22em] uppercase text-coral">Río Negro · Patagonia</span>
                <span class="font-display text-[22px] font-medium text-ink mt-0.5"
                      style="font-variation-settings: 'opsz' 144, 'SOFT' 30;">El Cóndor</span>
            </div>
        </a>

        <ul class="hidden lg:flex gap-7 ml-auto">
            @foreach($links as [$route, $label])
                <li><a href="{{ Route::has($route) ? route($route) : '#' }}" class="nav-link">{{ $label }}</a></li>
            @endforeach
        </ul>

        @if($weather)
            <div class="hidden md:flex items-center gap-2.5 px-3.5 py-2 bg-foam border border-ink-line rounded-full font-mono text-xs">
                <span class="w-1.5 h-1.5 bg-sun rounded-full ring-4 ring-sun/20"></span>
                <span>{{ $weather['temp'] }}°C · {{ $weather['wind_label'] }}</span>
            </div>
        @endif

        <button @click="open = !open" class="lg:hidden ml-auto p-2" aria-label="Menú">
            <svg x-show="!open" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/></svg>
            <svg x-show="open" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M6 18L18 6" stroke-linecap="round"/></svg>
        </button>
    </div>

    <div x-show="open" x-transition class="lg:hidden border-t border-ink-line bg-sand px-5 py-4">
        <ul class="flex flex-col gap-3">
            @foreach($links as [$route, $label])
                <li><a href="{{ Route::has($route) ? route($route) : '#' }}" class="nav-link text-lg">{{ $label }}</a></li>
            @endforeach
        </ul>
    </div>
</nav>
```

### Step 5: Footer component

Crear `app/resources/views/components/public/footer.blade.php`:

```blade
<footer class="bg-ink text-sand pt-24 pb-10 relative overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-5 lg:px-10">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 pb-14 border-b border-sand/10">
            <div>
                <img src="{{ asset('img/logo.png') }}" alt="" class="w-[86px] h-[86px] mb-5">
                <h4 class="font-display text-3xl leading-none mb-3"
                    style="font-variation-settings: 'opsz' 144, 'SOFT' 50;">
                    El faro,<br>el cóndor <em class="text-sun" style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">y el mar.</em>
                </h4>
                <p class="text-sand/65 max-w-[40ch] text-[15px]">Pueblo costero a 30 km de Viedma, en la desembocadura del río Negro sobre el Atlántico patagónico. Verano, otoño, invierno y primavera.</p>
            </div>

            <div>
                <h5 class="font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">Visitar</h5>
                <ul class="flex flex-col gap-2.5">
                    <li><a href="{{ route('novedades.index') }}" class="font-display text-base hover:text-coral-soft transition-colors" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Novedades</a></li>
                    <li><a href="{{ route('eventos.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Eventos</a></li>
                    <li><a href="{{ route('hospedajes.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Hospedajes</a></li>
                    <li><a href="{{ route('gastronomia.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Gourmet</a></li>
                    <li><a href="{{ route('alquileres.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Alquileres</a></li>
                    <li><a href="{{ route('recetas.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Recetas</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">Comunidad</h5>
                <ul class="flex flex-col gap-2.5">
                    <li><a href="{{ route('clasificados.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Clasificados</a></li>
                    <li><a href="{{ route('galeria.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Galería</a></li>
                    <li><a href="{{ route('servicios.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Servicios</a></li>
                    <li><a href="{{ route('cercanos.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Lugares cercanos</a></li>
                    <li><a href="{{ route('mareas.index') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Tabla de mareas</a></li>
                    <li><a href="{{ route('newsletter.form') }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Newsletter</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">Contacto</h5>
                <p class="font-mono text-[13px] text-sand/70 leading-[1.9]">
                    Turismo Municipal<br>
                    Av. Costanera s/n<br>
                    El Cóndor, Río Negro<br><br>
                    <strong class="text-sun">+54 9 2920 15 3300</strong><br>
                    turismo@elcondor.gob.ar
                </p>
            </div>
        </div>

        <div class="pt-7 flex flex-wrap justify-between gap-5 font-mono text-xs text-sand/55">
            <span>© {{ date('Y') }} Balneario El Cóndor · Río Negro, Patagonia Argentina</span>
            <span>Sitio público · v1</span>
        </div>
    </div>
</footer>
```

### Step 6: Wave divider

Crear `app/resources/views/components/public/wave-divider.blade.php`:

```blade
@props(['color' => 'ink'])
<div class="block w-full h-20 text-{{ $color }}">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none" class="w-full h-full">
        <path d="M0,40 C120,70 240,10 360,30 C480,50 600,20 720,40 C840,60 960,20 1080,40 C1200,60 1320,20 1440,40 L1440,80 L0,80 Z" fill="currentColor" opacity="0.08"/>
        <path d="M0,50 C120,80 240,20 360,40 C480,60 600,30 720,50 C840,70 960,30 1080,50 C1200,70 1320,30 1440,50" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.25"/>
    </svg>
</div>
```

### Step 7: HomeController stub

Crear `app/app/Http/Controllers/Public/HomeController.php`:

```php
<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\{Event, News, Tide};
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
```

### Step 8: Routes + home placeholder

Modificar `app/routes/web.php` — reemplazar la ruta `/` default con:

```php
use App\Http\Controllers\Public\HomeController;

Route::get('/', HomeController::class)->name('home');

// Stubs para que Route::has() del nav resuelva (se implementan en tasks 2-6)
Route::get('/novedades', fn() => 'pending')->name('novedades.index');
Route::get('/eventos', fn() => 'pending')->name('eventos.index');
Route::get('/hospedajes', fn() => 'pending')->name('hospedajes.index');
Route::get('/gastronomia', fn() => 'pending')->name('gastronomia.index');
Route::get('/alquileres', fn() => 'pending')->name('alquileres.index');
Route::get('/recetas', fn() => 'pending')->name('recetas.index');
Route::get('/clasificados', fn() => 'pending')->name('clasificados.index');
Route::get('/galeria', fn() => 'pending')->name('galeria.index');
Route::get('/mareas', fn() => 'pending')->name('mareas.index');
Route::get('/servicios', fn() => 'pending')->name('servicios.index');
Route::get('/cercanos', fn() => 'pending')->name('cercanos.index');
Route::get('/newsletter', fn() => 'pending')->name('newsletter.form');
```

### Step 9: Home view placeholder

Crear `app/resources/views/public/home.blade.php` — por ahora un placeholder que usa el layout y muestra el diseño básico (hero simplificado). La implementación completa de todas las secciones va en Task 2.

### Step 10: Logo en public

Copiar el logo al directorio público:

```bash
cp /home/juan/sitios/balneario-el-condor/htdocs/img/logo.png /home/juan/sitios/balneario-el-condor/app/public/img/logo.png
```

### Step 11: Test foundation

Crear `app/tests/Feature/Public/PublicFoundationTest.php`:

```php
<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_ok(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_home_includes_nav_links(): void
    {
        $this->get('/')->assertSee('Novedades')->assertSee('Eventos')->assertSee('El Cóndor');
    }

    public function test_home_includes_footer_contact(): void
    {
        $this->get('/')->assertSee('Turismo Municipal')->assertSee('+54 9 2920');
    }

    public function test_home_loads_fonts(): void
    {
        $this->get('/')->assertSee('Fraunces', false)->assertSee('Instrument+Sans', false);
    }
}
```

### Step 12: Build + verify

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose exec -T app npm run build
docker compose exec -T app php artisan test tests/Feature/Public/
docker compose exec -T app php artisan test
```

### Step 13: Commit

```bash
git add app/ design-preview/
git commit -m "feat(public): foundation con theme editorial costero + nav + footer"
```

---

## Task 2: Home page — hero + bento + mareas feature

Implementar la home completa replicando el diseño del preview.

**Archivos:**
- Replace: `app/resources/views/public/home.blade.php` — implementación completa
- Create: `app/resources/views/components/public/hero.blade.php`
- Create: `app/resources/views/components/public/tide-card.blade.php` (widget inline)
- Create: `app/resources/views/components/public/tide-chart.blade.php` (section mareas feature)
- Create: `app/resources/views/components/public/event-stamp.blade.php`
- Create: `app/resources/views/components/public/bento/` (5 cards: featured-news, events-list, gallery-preview, hospedajes, gourmet, recetas, alquileres, clasificados)
- Create: `app/tests/Feature/Public/HomeTest.php`

### Structure

El home tiene 5 secciones:

1. **Hero** (`<section class="hero">`) — display heading + lede + CTAs + foto con tide card overlay
2. **Wave divider** SVG
3. **Bento** con 8 cards editoriales (mirror del preview)
4. **Mareas feature** — sección full-width con fondo ink, chart SVG y 4 readings
5. **Info strip** — 4 info items con emergencias

Ver `design-preview/fase-5-home.html` para el código HTML+CSS de referencia. **La implementación es literal** — mismo markup, mismas clases Tailwind (con los tokens del theme extendido).

### Step 1: Hero component

```blade
@props(['news', 'tide'])
<section class="hero relative pt-20 pb-32 overflow-hidden">
    <div class="hero-sun absolute -top-32 -right-48 w-[640px] h-[640px] pointer-events-none
                bg-[radial-gradient(circle,_rgba(216,155,42,0.22)_0%,_transparent_55%)]"></div>

    <div class="max-w-[1400px] mx-auto px-5 lg:px-10">
        <div class="grid lg:grid-cols-[1fr_1.15fr] gap-20 items-center min-h-[680px]">

            <div class="hero-text">
                <div class="flex items-center gap-3.5 mb-7 animate-rise" style="animation-delay: 0.1s;">
                    <span class="w-12 h-0.5 bg-coral"></span>
                    <span class="eyebrow">Balneario · Desde 1921</span>
                </div>

                <h1 class="display-xl text-[clamp(52px,7.5vw,108px)] animate-rise" style="animation-delay: 0.2s;">
                    El faro,<br>
                    el cóndor<br>
                    <em class="display-italic -translate-x-2 inline-block">y el mar.</em>
                </h1>

                <p class="mt-9 text-[19px] leading-relaxed text-ink-soft max-w-[48ch] animate-rise" style="animation-delay: 0.4s;">
                    Donde la costa atlántica se vuelve patagónica. A 30 km de Viedma, entre la desembocadura del río Negro y el océano, el pueblo más austral de la provincia.
                </p>

                <div class="mt-11 flex gap-4 flex-wrap animate-rise" style="animation-delay: 0.55s;">
                    <a href="{{ route('hospedajes.index') }}" class="btn-primary">
                        Planificar la visita
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="{{ route('galeria.index') }}" class="btn-ghost">Ver la galería</a>
                </div>
            </div>

            <div class="relative h-[620px] lg:block hidden">
                <div class="absolute inset-0 rounded-md shadow-lift -rotate-1 animate-rise bg-cover bg-[center_30%]"
                     style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1400&q=85'); animation-delay: 0.3s;"></div>

                <div class="absolute top-10 -right-10 w-[200px] h-[260px] bg-cover bg-center rounded shadow-card rotate-[4deg] border-[6px] border-foam animate-rise"
                     style="background-image: url('https://images.unsplash.com/photo-1507652955-f3dcef5a3be5?w=600&q=85'); animation-delay: 0.85s;"></div>

                @if($tide)
                    <x-public.tide-card :tide="$tide" class="absolute -left-16 -bottom-8 w-[340px] animate-rise" style="animation-delay: 0.7s;" />
                @endif
            </div>
        </div>
    </div>
</section>
```

### Step 2: Tide card widget

```blade
@props(['tide'])
<div {{ $attributes->merge(['class' => 'bg-foam rounded-md p-6 shadow-lift border border-ink-line']) }}>
    <div class="flex items-baseline justify-between gap-3 pb-3.5 border-b border-ink-line">
        <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft">Mareas · Hoy</span>
        <span class="font-mono text-[11px] text-coral">{{ $tide->date->isoFormat('dddd D MMMM') }}</span>
    </div>
    <h3 class="font-display text-2xl font-medium mt-2.5"
        style="font-variation-settings: 'opsz' 48, 'SOFT' 60;">
        {{ $tide->headline ?? 'Pleamar al atardecer' }}
    </h3>
    <div class="mt-3.5 grid grid-cols-2 gap-3.5">
        @foreach([
            ['1.ª Pleamar', $tide->first_high, $tide->first_high_height],
            ['1.ª Bajamar', $tide->first_low, $tide->first_low_height],
            ['2.ª Pleamar', $tide->second_high, $tide->second_high_height],
            ['2.ª Bajamar', $tide->second_low, $tide->second_low_height],
        ] as [$k, $t, $h])
            <div class="flex flex-col">
                <span class="font-mono text-[10px] tracking-[0.15em] uppercase text-ink-soft">{{ $k }}</span>
                <span class="font-display text-[28px] font-medium text-ink mt-1" style="font-variation-settings: 'opsz' 48;">
                    {{ $t ? substr($t, 0, 5) : '—' }}
                </span>
                <span class="font-mono text-xs text-sun-deep mt-0.5">{{ $h ? "+ {$h}" : '' }}</span>
            </div>
        @endforeach
    </div>
    {{-- SVG wave sparkline --}}
    <div class="mt-4 h-15">
        <svg viewBox="0 0 300 60" preserveAspectRatio="none" class="w-full h-full">
            <path d="M0,30 Q37,0 75,30 T150,30 T225,30 T300,30" fill="none" stroke="#1e40af" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <circle cx="75" cy="8" r="3" fill="#d89b2a"/>
            <circle cx="225" cy="8" r="3" fill="#d89b2a"/>
            <circle cx="150" cy="52" r="3" fill="#c85a3c"/>
        </svg>
    </div>
</div>
```

### Step 3: Bento cards

Extraer cada card del preview como componente Blade. El implementador debe:

- Copiar el markup de cada `.card-*` del preview HTML a `components/public/bento/{name}.blade.php`
- Parametrizar con `@props(...)` las props que vengan del controller
- Mantener las clases Tailwind con los tokens del theme

Cards a crear:
- `bento/featured-news.blade.php` — recibe `$news` (News model)
- `bento/events-list.blade.php` — recibe `$events` (collection)
- `bento/gallery-preview.blade.php` — recibe `$images` (4 GalleryImage)
- `bento/hospedajes-teaser.blade.php` — recibe `$count`, `$priceFrom`
- `bento/gourmet-teaser.blade.php` — recibe `$count`
- `bento/recetas-teaser.blade.php` — recibe `$count`
- `bento/alquileres-teaser.blade.php` — recibe `$count`
- `bento/clasificados-teaser.blade.php` — recibe `$latest` (collection)

### Step 4: Mareas feature section

Dedicado, full-width con fondo ink. Replicar `section.mareas-feat` del preview. El SVG chart dinámico puede generar la curva desde las 4 horas de marea usando JS (Alpine) o renderizar server-side (sin JS). Preferir server-side con valores por defecto si no hay data.

### Step 5: Info strip

4 items con teléfonos de emergencia. Data hardcoded (viene de CLAUDE.md + Info Útil model — mejor consultar UsefulInfo top 4 por sort_order).

### Step 6: Home controller completo

```php
public function __invoke(): View
{
    return view('public.home', [
        'featuredNews'   => News::published()->latest('published_at')->first(),
        'upcomingEvents' => Event::where('starts_at', '>=', now())->orderBy('starts_at')->limit(3)->get(),
        'latestImages'   => GalleryImage::latest()->limit(4)->get(),
        'latestClassifieds' => Classified::latest('published_at')->limit(4)->get(),
        'todayTide'      => Tide::whereDate('date', today())->first() ?? Tide::latest('date')->first(),
        'stats' => [
            'hospedajes'   => Lodging::count(),
            'venues'       => Venue::count(),
            'rentals'      => Rental::count(),
            'recipes'      => Recipe::count(),
            'gallery'      => GalleryImage::count(),
        ],
        'infoTop' => UsefulInfo::orderBy('sort_order')->limit(4)->get(),
    ]);
}
```

Agregar scope `published` a News: `scopeWhere('published_at', '<=', now())`. Si el controller falla por `scopePublished` no existente, agregarlo al model.

### Step 7: Tests

Crear `app/tests/Feature/Public/HomeTest.php`:

```php
class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_hero_heading(): void
    {
        $this->get('/')->assertSee('El faro,')->assertSee('el cóndor');
    }

    public function test_home_shows_featured_news_when_exists(): void
    {
        $news = News::factory()->create(['title' => 'Fiesta del Tejo', 'published_at' => now()->subDay()]);
        $this->get('/')->assertSee('Fiesta del Tejo');
    }

    public function test_home_shows_upcoming_events(): void
    {
        $event = Event::factory()->create(['title' => 'Peña Patagónica', 'starts_at' => now()->addDays(5)]);
        $this->get('/')->assertSee('Peña Patagónica');
    }

    public function test_home_shows_today_tide(): void
    {
        Tide::factory()->create(['date' => today(), 'first_high' => '08:42:00', 'first_high_height' => '3.85 m']);
        $this->get('/')->assertSee('08:42')->assertSee('3.85');
    }

    public function test_home_empty_state_shows_gracefully(): void
    {
        $this->get('/')->assertOk(); // ningún factory, pero no crashea
    }
}
```

### Step 8: Commit

```bash
git add app/
git commit -m "feat(public): home con hero + bento editorial + mareas feature + info strip"
```

---

## Task 3: Novedades (index + show)

**Fields relevantes:** title, slug, body, news_category_id, published_at, views.

**Archivos:**
- Create: `app/app/Http/Controllers/Public/NewsController.php`
- Create: `app/resources/views/public/novedades/{index,show}.blade.php`
- Create: `app/resources/views/components/public/article-card.blade.php` (card reutilizable)
- Modify: `app/routes/web.php` — rutas reales de novedades
- Create: `app/tests/Feature/Public/NewsTest.php`

### Rutas (spec §7 §10)

```php
Route::get('/novedades', [NewsController::class, 'index'])->name('novedades.index');
Route::get('/novedades/{news:slug}', [NewsController::class, 'show'])->name('novedades.show');
Route::get('/novedades/categoria/{category:slug}', [NewsController::class, 'byCategory'])->name('novedades.category');
```

### Index

**Layout:** header editorial (h1 grande "Novedades" + lede), filtros por categoría (tabs), grid de 2 columnas con featured primero + lista abajo. Paginación 12 por página.

**Featured treatment:** card con foto grande, título gigante, categoría como eyebrow coral, fecha mono. Efecto polaroid tilt -1deg.

### Show

**Layout:** artículo editorial largo. Max-width `68ch` para lectura cómoda. Typography:
- h1 Fraunces 72-96px con SOFT alto
- subtítulo Instrument Sans italic 22px
- body Instrument Sans 19px line-height 1.7
- drop cap (first-letter Fraunces 6em) en el primer párrafo
- pull quotes con border-left coral 3px + italic Fraunces

Incluir:
- Breadcrumb: Inicio · Novedades · Título
- Metadata: autor, fecha, categoría como badges
- Media polimórfica (galería si tiene >1 imagen)
- "Leer también" (3 artículos relacionados por categoría)
- Social share buttons con SVG custom (X, WhatsApp, link copy)

### Commit

```bash
git add app/
git commit -m "feat(public): novedades (index + show con lectura editorial + galería)"
```

---

## Task 4: Eventos (index + show + registro)

**Rutas:**
```php
Route::get('/eventos', [EventController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{event:slug}', [EventController::class, 'show'])->name('eventos.show');
Route::post('/eventos/{event:slug}/inscripcion', [EventController::class, 'register'])->name('eventos.register');
```

### Index

**Layout:** calendario + lista. Header con toggle "Próximos / Pasados". Events como cards con:
- Date stamp (dd + mes) estilo sello de correo (ocre sobre navy)
- Título Fraunces
- Lugar + hora mono
- Si featured: foto grande + ribbon "Destacado" coral

Agrupar por mes en scroll (sticky headers con mes en italic Fraunces).

### Show

**Layout:** hero con foto + título + date stamp grande + location. Descripción editorial. Si `accepts_registrations = true`, form de inscripción inline.

### Register form

Form POST con FormRequest. Si el evento es Tejo o Primavera (heredados del legacy), mantener campos originales del legacy (club, alojamiento, entradas, etc.) usando JSON `extra_data`. Form dinámico según el evento.

### Commit

```bash
git add app/
git commit -m "feat(public): eventos (index con calendario + show + form de inscripción)"
```

---

## Task 5: Directorio — hospedajes, gourmet/nocturnos, alquileres, servicios, cercanos, info útil

6 módulos, misma estructura: index (listado con filtros) + show (ficha detallada con mapa Leaflet).

**Patrón común:**

- **Index:** grid editorial. Filtros por tipo/categoría según módulo. Paginación.
- **Show:** hero con galería de fotos + ficha con contacto + descripción + mapa Leaflet centrado en lat/lng + "Lugares cercanos" relacionados.

**Leaflet:** usar tiles OpenStreetMap. CDN del CSS y JS en el layout o como component `<x-public.map :lat="..." :lng="..." />`.

```blade
@props(['lat', 'lng', 'zoom' => 15, 'label'])
<div x-data="{
    init() {
        const map = L.map(this.$el).setView([{{ $lat }}, {{ $lng }}], {{ $zoom }});
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
        }).addTo(map);
        L.marker([{{ $lat }}, {{ $lng }}]).addTo(map)
            .bindPopup(@js($label));
    }
}" class="h-[400px] rounded shadow-card overflow-hidden border border-ink-line">
</div>
```

Agregar Leaflet CDN al layout main.blade.php en el `{{ $head ?? '' }}`.

### Rutas

```php
Route::get('/hospedajes', ...)->name('hospedajes.index');
Route::get('/hospedajes/{lodging:slug}', ...)->name('hospedajes.show');
Route::get('/gastronomia', ...)->name('gastronomia.index');  // unifica gourmet+nocturnos
Route::get('/gastronomia/{venue:slug}', ...)->name('gastronomia.show');
Route::get('/alquileres', ...)->name('alquileres.index');
Route::get('/alquileres/{rental:slug}', ...)->name('alquileres.show');
Route::get('/servicios', ...)->name('servicios.index');
Route::get('/servicios/{serviceProvider:slug}', ...)->name('servicios.show');
Route::get('/cercanos', ...)->name('cercanos.index');
Route::get('/cercanos/{nearbyPlace:slug}', ...)->name('cercanos.show');
Route::get('/informacion-util', ...)->name('info-util.index');
```

### Commit

```bash
git add app/
git commit -m "feat(public): directorio (hospedajes, gastronomía, alquileres, servicios, cercanos, info útil) con mapas Leaflet"
```

---

## Task 6: Comunidad — clasificados, galería, recetas

### Clasificados

- **Index:** grid con filtros por categoría. Card: thumbnail (primera media), título, descripción corta, precio (si hay), fecha mono, "ubicación" mini.
- **Show:** hero con galería + ficha + form "Contactar al anunciante" (envía a `ClassifiedContact` creando fila + email al dueño).
- **Submit nuevo clasificado:** NO (no es requerido por spec — son moderados desde admin).

### Galería

- **Index:** masonry grid (flex wrap con aspect-ratio variado). Lazy-loading de imágenes. Filter por año.
- **Show:** lightbox con Alpine + keyboard navigation (← → esc). Atribución + fecha.

### Recetas

- **Index:** cards editoriales con foto, tiempo total (prep+cook), porciones mono.
- **Show:** layout editorial dividido en dos columnas: ingredientes sticky a la izquierda, instrucciones paginadas a la derecha. Foto hero top.

### Commit

```bash
git add app/
git commit -m "feat(public): comunidad (clasificados con contacto, galería lightbox, recetas editoriales)"
```

---

## Task 7: Mareas + Clima (Open-Meteo)

### Mareas

- **Ruta:** `/mareas` → tabla + chart SVG dinámico.
- **Layout:** fecha selector (hoy / mes / año), tabla con 4 columnas (pleamares/bajamares), chart full-width con 7 días.
- **Data:** viene del model `Tide`.

### Clima — integración Open-Meteo

**Command** `app/app/Console/Commands/SyncWeatherCommand.php`:

```php
class SyncWeatherCommand extends Command
{
    protected $signature = 'weather:sync';

    public function handle(): int
    {
        $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => -41.05,
            'longitude' => -62.82,
            'current' => 'temperature_2m,wind_speed_10m,wind_direction_10m,weather_code',
            'timezone' => 'America/Argentina/Buenos_Aires',
        ]);

        if ($response->failed()) {
            $this->error('Open-Meteo falló');
            return self::FAILURE;
        }

        $data = $response->json('current');
        $summary = [
            'temp' => (int) round($data['temperature_2m']),
            'wind' => (int) round($data['wind_speed_10m']),
            'wind_dir' => $this->compassFromDegrees($data['wind_direction_10m']),
            'wind_label' => sprintf('Viento %s %dkm/h',
                $this->compassFromDegrees($data['wind_direction_10m']),
                (int) round($data['wind_speed_10m'])),
            'code' => $data['weather_code'],
            'updated_at' => now()->toIso8601String(),
        ];

        Cache::put('weather:current', $summary, now()->addHours(2));
        $this->info("Actualizado: {$summary['temp']}°C · {$summary['wind_label']}");
        return self::SUCCESS;
    }

    private function compassFromDegrees(int $deg): string
    {
        $dirs = ['N', 'NE', 'E', 'SE', 'S', 'SO', 'O', 'NO'];
        return $dirs[(int) round($deg / 45) % 8];
    }
}
```

**Scheduler:** en `app/routes/console.php`:

```php
Schedule::command('weather:sync')->everyThirtyMinutes()->onOneServer();
```

**Uso:** el nav component ya lee `cache()->get('weather:current')` y renderiza el badge.

**Página clima:** `/clima` con la vista extendida (hoy + próximos 3 días con íconos SVG custom).

### Commit

```bash
git add app/
git commit -m "feat(public): mareas (tabla + chart SVG) + integración Open-Meteo con cache Redis"
```

---

## Task 8: Pages + contacto + newsletter (forms)

### Pages

- **Ruta:** `/pagina/{page:slug}`.
- CRUD genérico para páginas como "Historia", "Fauna", "Cómo llegar". Admin ya las gestiona (Fase 4).
- **Layout:** layout editorial con h1 + content (stored como HTML o Markdown parseado) + meta_description.

### Contacto

- **Ruta:** GET `/contacto`, POST `/contacto/enviar`.
- Form con name, email, phone, subject, message + checkbox "Acepto términos".
- FormRequest valida + crea `ContactMessage` row + envía email al admin via `Mail::send(new ContactFormMail($message))`.
- Success: redirect con flash "Mensaje enviado".

### Newsletter

- **Ruta:** GET `/newsletter`, POST `/newsletter/suscribir`.
- Form simple: email.
- Double opt-in: al submit crea `NewsletterSubscriber` status `pending` + genera `confirmation_token` + envía email con link de confirmación.
- **Confirmación:** GET `/newsletter/confirmar/{token}` marca status `confirmed` + `confirmed_at`.
- **Baja:** GET `/newsletter/baja/{token}` marca status `unsubscribed` + `unsubscribed_at`.

### Publicite

- **Ruta:** GET `/publicite`, POST `/publicite/enviar`.
- Form: name, last_name, email, message, zone.
- Crea `AdvertisingContact`.

### Commit

```bash
git add app/
git commit -m "feat(public): pages genéricas + forms de contacto, newsletter (double opt-in) y publicite"
```

---

## Task 9: SEO + sitemap + robots + schema.org

### Sitemap

Generar sitemap XML dinámico:

- **Ruta:** `/sitemap.xml` → returns `text/xml`.
- Incluye todas las URLs públicas: home, novedades individuales, eventos individuales, hospedajes, venues, etc.
- Cache 24 hs.

### Robots

`app/public/robots.txt`:

```
User-agent: *
Allow: /
Disallow: /admin
Disallow: /login
Sitemap: https://elcondor.gob.ar/sitemap.xml
```

### Meta tags

Ya incluidos en el layout main.blade.php de Task 1 (og:title, og:description, og:image, canonical). Cada página pasa `title`, `description`, `image` como slots.

### Schema.org JSON-LD

Agregar en el layout un `<script type="application/ld+json">` por tipo de página:

- Home → `TouristDestination`
- Event → `Event` con startDate, location
- Lodging → `LodgingBusiness`
- Venue → `Restaurant` o `NightClub`
- Recipe → `Recipe` con ingredients, instructions, prepTime, cookTime

Crear component `app/resources/views/components/public/jsonld/{type}.blade.php` por tipo.

### Commit

```bash
git add app/
git commit -m "feat(public): SEO (sitemap dinámico + robots + meta + JSON-LD por tipo)"
```

---

## Task 10: Tests E2E + smoke + accesibilidad básica

### Smoke test de navegación pública

Crear `app/tests/Feature/Public/PublicSmokeTest.php`:

```php
public function test_all_public_index_routes_respond_200(): void
{
    // Crear data mínima para que las páginas no crasheen en empty state
    $this->seed(\Database\Seeders\DemoDataSeeder::class);

    $routes = [
        '/', '/novedades', '/eventos', '/hospedajes', '/gastronomia',
        '/alquileres', '/clasificados', '/galeria', '/mareas',
        '/servicios', '/cercanos', '/informacion-util',
        '/contacto', '/newsletter', '/publicite',
        '/sitemap.xml',
    ];
    foreach ($routes as $r) {
        $this->get($r)->assertStatus(200, "Route {$r} failed");
    }
}

public function test_show_pages_render_for_random_records(): void
{
    $this->seed(\Database\Seeders\DemoDataSeeder::class);
    $news = \App\Models\News::first();
    $event = \App\Models\Event::first();
    $lodging = \App\Models\Lodging::first();

    $this->get(route('novedades.show', $news))->assertOk();
    $this->get(route('eventos.show', $event))->assertOk();
    $this->get(route('hospedajes.show', $lodging))->assertOk();
}

public function test_forms_validate_required(): void
{
    $this->post('/contacto/enviar', [])->assertSessionHasErrors(['name', 'email', 'message']);
    $this->post('/newsletter/suscribir', [])->assertSessionHasErrors(['email']);
}
```

### Accesibilidad

- Todos los images tienen `alt`.
- Todos los forms usan `<label>` asociado.
- Contraste verificado manualmente (cream/ink = ratio 8.3, pasa AAA).
- Focus states visibles (Tailwind `focus-visible:ring-2 ring-coral`).
- Skip link al main content.

### Commit final

```bash
git add app/
git commit -m "feat(public): tests smoke E2E + accesibilidad básica"
```

---

## Criterios de aceptación de Fase 5

- [ ] Home con hero + bento + mareas + info strip funcional y visualmente idéntica al preview.
- [ ] Nav responsive con clima en vivo (Open-Meteo cache 2h).
- [ ] 12+ módulos con index + show funcionando (novedades, eventos, hospedajes, gastronomía, alquileres, clasificados, galería, recetas, mareas, servicios, cercanos, info útil, pages).
- [ ] 4 forms funcionales: contacto, newsletter (double opt-in), publicite, inscripción a eventos.
- [ ] SEO: sitemap.xml dinámico, robots.txt, meta tags + JSON-LD por tipo.
- [ ] Tests: PublicFoundationTest + HomeTest + PublicSmokeTest + por módulo (~80 tests nuevos).
- [ ] `php artisan test` verde (316+ tests totales: 236 admin + 80 público).
- [ ] Accesibilidad: contraste AAA, alt en imágenes, focus visible, skip link.
- [ ] Performance: CWV verde (LCP < 2.5s, CLS < 0.1) en Lighthouse para la home.

## Riesgos

1. **Fuentes variables de Google Fonts** pesan ~180kb combinadas. Self-host en prod o usar `font-display: swap`.
2. **Leaflet tiles de OpenStreetMap** tienen límites de uso. Para prod, usar un CDN tile o MapTiler con API key.
3. **Unsplash URLs** en el preview son placeholders. Reemplazar con media real de `storage/app/public/legacy/**` para prod.
4. **Grain SVG** puede afectar performance en móviles viejos. Alternativa: imagen PNG optimizada en lugar del data-URI.
5. **Mareas viejas:** el model tiene ~3000 rows. La tabla `/mareas` necesita índice en `date` (ya existe en migración) y paginación.
6. **Open-Meteo puede caer:** el nav debe renderizar sin el badge si `cache('weather:current')` es null (ya contemplado).
7. **Schema.org JSON-LD** requiere que las URLs sean absolutas y las imágenes también.

## Preview de diseño

La dirección visual completa está implementada en [`design-preview/fase-5-home.html`](../../../design-preview/fase-5-home.html). Abrir el archivo en browser para ver el resultado renderizado antes de implementar en Blade. El preview usa el logo real del legacy (`design-preview/logo.png` copiado desde `htdocs/img/logo.png`).

Con todo esto verde, se puede pasar al **Plan 6 — API pública + integración Resend + deploy** (Fase 6 del spec).
