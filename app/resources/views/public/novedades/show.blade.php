@php
    use Illuminate\Support\Str;

    $hero      = $news->media->first();
    $heroUrl   = $hero?->path
        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
            ? $hero->path
            : asset('storage/' . ltrim($hero->path, '/')))
        : null;

    // Resto de la galería (después del hero).
    $gallery = $news->media->skip(1)->values();

    // Body parser: separa por dobles newlines a párrafos. Escapa HTML pero
    // preserva saltos de línea simples como <br>.
    $paragraphs = collect(preg_split("/\r\n\r\n|\r\n|\n\n|\n/u", trim((string) $news->body)))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();

    $shareText = rawurlencode($news->title . ' — Balneario El Cóndor');
    $shareUrl  = rawurlencode(url()->current());
@endphp

<x-public.layouts.main :title="$news->title" :description="$news->excerpt" :image="$heroUrl">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li><a href="{{ route('novedades.index') }}" class="hover:text-coral transition-colors">Novedades</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink truncate max-w-[40ch]">{{ Str::limit($news->title, 30) }}</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HERO ARTICLE =============== --}}
    <article class="bg-sand">
        <header class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-10">
            <div class="max-w-[68ch] mx-auto text-center">
                @if ($news->category)
                    <a href="{{ route('novedades.index', ['categoria' => $news->category->slug]) }}"
                       class="inline-block eyebrow mb-6 hover:text-ink transition-colors">
                        {{ $news->category->name }}
                    </a>
                @endif

                <h1 class="font-display font-normal leading-[0.95] text-ink mb-8"
                    style="font-size: clamp(40px, 6.4vw, 96px); letter-spacing: -0.03em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    {{ $news->title }}
                </h1>

                <div class="flex flex-wrap justify-center items-center gap-x-5 gap-y-2 font-mono text-[11px] tracking-[0.16em] uppercase text-ink-soft">
                    <time datetime="{{ $news->published_at?->toIso8601String() }}">
                        {{ $news->published_at?->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                    </time>
                    <span aria-hidden="true" class="text-ink-line">·</span>
                    <span>Lectura · {{ $news->reading_minutes }} min</span>
                    @if ($news->views > 0)
                        <span aria-hidden="true" class="text-ink-line">·</span>
                        <span>{{ number_format($news->views, 0, ',', '.') }} lecturas</span>
                    @endif
                </div>
            </div>
        </header>

        {{-- Hero image --}}
        @if ($heroUrl)
            <div class="max-w-[1240px] mx-auto px-5 lg:px-8 mb-16">
                <figure class="relative aspect-[16/9] rounded-md overflow-hidden border border-ink-line shadow-card">
                    <img src="{{ $heroUrl }}"
                         alt="{{ $hero->alt ?? $news->title }}"
                         class="w-full h-full object-cover"
                         loading="eager"
                         fetchpriority="high">
                    @if ($hero->alt)
                        <figcaption class="absolute left-5 bottom-5 right-5 bg-sand/90 backdrop-blur-sm py-2 px-4 rounded
                                           font-mono text-[11px] text-ink-soft border border-ink-line">
                            {{ $hero->alt }}
                        </figcaption>
                    @endif
                </figure>
            </div>
        @endif

        {{-- =============== ARTICLE BODY =============== --}}
        <div class="max-w-[68ch] mx-auto px-5 lg:px-8 pb-12">
            <div class="prose-lg text-ink"
                 style="font-size: 19px; line-height: 1.7;">
                @foreach ($paragraphs as $i => $p)
                    @if ($i === 0)
                        <p class="mb-6 first-letter:float-left first-letter:font-display
                                  first-letter:text-[5em] first-letter:mr-3 first-letter:mt-1
                                  first-letter:leading-[0.8] first-letter:text-coral
                                  first-letter:font-medium">
                            {{ $p }}
                        </p>
                    @else
                        <p class="mb-6">{{ $p }}</p>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- =============== VIDEO (optional) =============== --}}
        @if ($news->video_url)
            <div class="max-w-[1024px] mx-auto px-5 lg:px-8 pb-16">
                <div class="aspect-video rounded-md overflow-hidden border border-ink-line shadow-card bg-ink">
                    <iframe src="{{ $news->video_url }}"
                            title="Video adjunto a {{ $news->title }}"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy"></iframe>
                </div>
            </div>
        @endif

        {{-- =============== GALLERY (extra media) =============== --}}
        @if ($gallery->isNotEmpty())
            <div class="max-w-[1240px] mx-auto px-5 lg:px-8 pb-16">
                <span class="eyebrow block mb-5">Galería</span>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
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
                                     alt="{{ $img->alt ?? $news->title }}"
                                     class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700"
                                     loading="lazy">
                            </figure>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- =============== SOCIAL SHARE =============== --}}
        <div class="max-w-[68ch] mx-auto px-5 lg:px-8 pb-16"
             x-data="{ copied: false, copy() { navigator.clipboard.writeText(window.location.href).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); }); } }">
            <div class="border-t border-b border-ink-line py-6 flex flex-wrap items-center justify-between gap-5">
                <span class="eyebrow">Compartir esta nota</span>
                <div class="flex items-center gap-3">
                    {{-- X / Twitter --}}
                    <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="Compartir en X"
                       class="w-10 h-10 rounded-full bg-foam border border-ink-line flex items-center justify-center
                              text-ink hover:bg-ink hover:text-sand hover:border-ink transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    {{-- WhatsApp --}}
                    <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="Compartir por WhatsApp"
                       class="w-10 h-10 rounded-full bg-foam border border-ink-line flex items-center justify-center
                              text-ink hover:bg-seaweed hover:text-sand hover:border-seaweed transition-colors">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.668 5.5l.247.392-1.013 3.674 3.787-.99zm6.875-5.396c.146.245.658 1.052.842 1.337.183.286.366.367.61.245.243-.122 1.034-.405 1.964-1.231.928-.826.928-1.643.65-1.762-.272-.122-1.18-.567-1.378-.628-.198-.061-.343-.061-.487.061-.144.122-.539.628-.66.756-.122.122-.244.122-.488 0-.245-.122-1.034-.367-1.97-1.198-.728-.65-1.22-1.452-1.363-1.696-.143-.243-.015-.376.107-.498.11-.108.244-.286.366-.428.122-.143.165-.247.246-.408.082-.163.04-.305-.02-.428-.062-.122-.487-1.171-.667-1.605-.176-.422-.353-.366-.487-.366-.122 0-.265-.02-.408-.02s-.378.061-.576.305c-.198.245-.756.74-.756 1.808 0 1.067.776 2.1.884 2.243.122.143 1.524 2.327 3.694 3.265.516.222.92.354 1.235.453.519.165.99.142 1.364.086.416-.062 1.282-.524 1.464-1.029.183-.508.183-.944.122-1.034-.061-.092-.244-.143-.488-.265z"/>
                        </svg>
                    </a>
                    {{-- Copy link --}}
                    <button @click="copy"
                            type="button"
                            aria-label="Copiar enlace"
                            class="relative w-10 h-10 rounded-full bg-foam border border-ink-line flex items-center justify-center
                                   text-ink hover:bg-coral hover:text-sand hover:border-coral transition-colors">
                        <svg x-show="!copied" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                        <svg x-show="copied" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span x-show="copied" x-cloak
                              class="absolute -top-9 left-1/2 -translate-x-1/2 bg-ink text-sand
                                     font-mono text-[10px] tracking-[0.1em] uppercase px-2 py-1 rounded whitespace-nowrap">
                            Copiado
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </article>

    {{-- =============== RELATED =============== --}}
    @if ($related->isNotEmpty())
        <section class="bg-sand-2 border-t border-ink-line py-20">
            <div class="max-w-[1360px] mx-auto px-5 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-6 mb-12">
                    <h2 class="font-display font-normal leading-[0.95] text-ink"
                        style="font-size: clamp(36px, 4vw, 56px); letter-spacing: -0.025em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Leer <em class="not-italic font-display italic text-sun-deep"
                                 style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">también</em>
                    </h2>
                    <a href="{{ route('novedades.index') }}"
                       class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                        Todas las novedades
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($related as $item)
                        <x-public.article-card :news="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-public.layouts.main>
