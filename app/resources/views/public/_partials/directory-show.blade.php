@php
    use Illuminate\Support\Str;

    /**
     * Variables esperadas:
     * - $item             modelo
     * - $title            string (item->name o item->title)
     * - $eyebrow          string (categoría / tipo)
     * - $description      string (cuerpo del texto)
     * - $contact          array{phone?, email?, website?, contact_name?, address?, places?}
     * - $hasMap           bool
     * - $lat, $lng        nullable
     * - $breadcrumb       array{label, route}
     * - $relatedHeading   string
     * - $relatedRoute     string (índice del módulo)
     * - $related          collection (cards)
     * - $relatedView      string ruta blade del card del módulo (uno por item)
     * - $relatedRouteName string (route name show, e.g. 'hospedajes.show')
     * - $relatedTitleField string ('name' o 'title')
     */

    $hero = $item->media?->first();
    $heroUrl = $hero?->path
        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
            ? $hero->path
            : asset('storage/' . ltrim($hero->path, '/')))
        : null;

    $gallery = $item->media ? $item->media->skip(1)->values() : collect();

    $paragraphs = collect(preg_split("/\r\n\r\n|\r\n|\n\n|\n/u", trim((string) ($description ?? ''))))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();
@endphp

{{-- =============== BREADCRUMB =============== --}}
<nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
    <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
        <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
            <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
            <li aria-hidden="true" class="text-ink-line">·</li>
            <li><a href="{{ route($breadcrumb['route']) }}" class="hover:text-coral transition-colors">{{ $breadcrumb['label'] }}</a></li>
            <li aria-hidden="true" class="text-ink-line">·</li>
            <li class="text-ink truncate max-w-[40ch]">{{ Str::limit($title, 30) }}</li>
        </ol>
    </div>
</nav>

{{-- =============== HERO =============== --}}
<article class="bg-sand">
    <header class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-10">
        <div class="max-w-[68ch] mx-auto text-center">
            @if (! empty($eyebrow))
                <div class="eyebrow mb-6">{{ $eyebrow }}</div>
            @endif

            <h1 class="font-display font-normal leading-[0.95] text-ink mb-8"
                style="font-size: clamp(40px, 6.4vw, 96px); letter-spacing: -0.03em;
                       font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                {{ $title }}
            </h1>

            @if (! empty($contact['address']))
                <div class="font-mono text-[12px] tracking-[0.16em] uppercase text-ink-soft">
                    {{ $contact['address'] }}
                </div>
            @endif
        </div>
    </header>

    @if ($heroUrl)
        <div class="max-w-[1240px] mx-auto px-5 lg:px-8 mb-16">
            <figure class="relative aspect-[16/9] rounded-md overflow-hidden border border-ink-line shadow-card">
                <img src="{{ $heroUrl }}"
                     alt="{{ $hero->alt ?? $title }}"
                     class="w-full h-full object-cover"
                     loading="eager"
                     fetchpriority="high">
            </figure>
        </div>
    @else
        <div class="max-w-[1240px] mx-auto px-5 lg:px-8 mb-16">
            <div class="aspect-[16/9] rounded-md border border-ink-line bg-sand-2 flex items-center justify-center text-ink-line shadow-card">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <circle cx="9" cy="11" r="1.5"/>
                    <path d="M3 17l5-5 4 4 3-3 6 6"/>
                </svg>
            </div>
        </div>
    @endif

    {{-- =============== BODY + CONTACT GRID =============== --}}
    <div class="max-w-[1240px] mx-auto px-5 lg:px-8 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            {{-- Left: description + galería --}}
            <div class="lg:col-span-2">
                @if ($paragraphs->isNotEmpty())
                    <div class="prose-lg text-ink"
                         style="font-size: 19px; line-height: 1.7;">
                        @foreach ($paragraphs as $p)
                            <p class="mb-6">{{ $p }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-ink-soft text-[17px] italic">
                        Próximamente más información sobre este lugar.
                    </p>
                @endif

                @if ($gallery->isNotEmpty())
                    <div class="mt-12">
                        <span class="eyebrow block mb-5">Galería</span>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($gallery as $img)
                                @php
                                    $imgUrl = $img->path
                                        ? (Str::startsWith($img->path, ['http://', 'https://', '/'])
                                            ? $img->path
                                            : asset('storage/' . ltrim($img->path, '/')))
                                        : null;
                                @endphp
                                @if ($imgUrl)
                                    <figure class="aspect-[4/3] rounded overflow-hidden border border-ink-line bg-sand-2">
                                        <img src="{{ $imgUrl }}"
                                             alt="{{ $img->alt ?? $title }}"
                                             class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700"
                                             loading="lazy">
                                    </figure>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: ficha de contacto --}}
            <aside class="lg:sticky lg:top-24 self-start">
                <div class="bg-foam border border-ink-line rounded-md p-7 shadow-card">
                    <span class="eyebrow block mb-5">Contacto</span>

                    @if (! empty($contact['contact_name']))
                        <div class="mb-5">
                            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">A cargo de</div>
                            <div class="text-ink text-[16px] font-medium">{{ $contact['contact_name'] }}</div>
                        </div>
                    @endif

                    @if (! empty($contact['places']))
                        <div class="mb-5">
                            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Capacidad</div>
                            <div class="text-ink text-[16px]">{{ $contact['places'] }} {{ $contact['places'] == 1 ? 'persona' : 'personas' }}</div>
                        </div>
                    @endif

                    @if (! empty($contact['phone']))
                        <div class="mb-5">
                            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Teléfono</div>
                            <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}"
                               class="font-mono text-[20px] text-ink hover:text-coral transition-colors">
                                {{ $contact['phone'] }}
                            </a>
                        </div>
                    @endif

                    @if (! empty($contact['email']))
                        <div class="mb-5">
                            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Email</div>
                            <a href="mailto:{{ $contact['email'] }}"
                               class="text-ink text-[15px] underline decoration-ink-line underline-offset-4 hover:decoration-coral hover:text-coral transition-colors break-all">
                                {{ $contact['email'] }}
                            </a>
                        </div>
                    @endif

                    @if (! empty($contact['website']))
                        <div class="mb-5">
                            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Sitio web</div>
                            <a href="{{ $contact['website'] }}"
                               target="_blank" rel="noopener noreferrer"
                               class="text-ink text-[15px] underline decoration-ink-line underline-offset-4 hover:decoration-coral hover:text-coral transition-colors break-all">
                                {{ $contact['website'] }}
                            </a>
                        </div>
                    @endif

                    @if (! empty($contact['address']))
                        <div class="mb-5">
                            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Dirección</div>
                            <div class="text-ink text-[15px]">{{ $contact['address'] }}</div>
                        </div>
                    @endif

                    @if ($hasMap && $lat && $lng)
                        <a href="#mapa"
                           class="inline-flex items-center gap-2 mt-3 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                            Ver en el mapa
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </aside>
        </div>
    </div>

    {{-- =============== MAP =============== --}}
    @if ($hasMap && $lat && $lng)
        <div id="mapa" class="max-w-[1240px] mx-auto px-5 lg:px-8 pb-20 scroll-mt-24">
            <span class="eyebrow block mb-5">Ubicación</span>
            <x-public.map :lat="$lat" :lng="$lng" :label="$title" />
        </div>
    @endif
</article>

{{-- =============== RELATED =============== --}}
@if ($related->isNotEmpty())
    <section class="bg-sand-2 border-t border-ink-line py-20">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-6 mb-12">
                <h2 class="font-display font-normal leading-[0.95] text-ink"
                    style="font-size: clamp(36px, 4vw, 56px); letter-spacing: -0.025em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    {{ $relatedHeading }}
                </h2>
                <a href="{{ route($relatedRoute) }}"
                   class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                    Ver todos
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($related as $r)
                    <x-public.directory-card
                        :item="$r"
                        :route="$relatedRouteName"
                        :titleField="$relatedTitleField"
                        :description="$r->description ?? null"
                        :meta="$r->address ?? null" />
                @endforeach
            </div>
        </div>
    </section>
@endif
