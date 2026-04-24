@php
    use Illuminate\Support\Str;

    $description = $page->meta_description
        ?: Str::limit(strip_tags((string) $page->content), 160);

    $hero = $page->media->first();
    $heroUrl = $hero?->path
        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
            ? $hero->path
            : asset('storage/' . ltrim($hero->path, '/')))
        : null;
@endphp

<x-public.layouts.main :title="$page->title" :description="$description" :image="$heroUrl">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink truncate max-w-[40ch]">{{ Str::limit($page->title, 40) }}</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HEADER =============== --}}
    <article class="bg-sand">
        <header class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-20 pb-12 text-center">
            <h1 class="font-display font-normal leading-[0.95] text-ink mx-auto"
                style="font-size: clamp(40px, 6.4vw, 96px); letter-spacing: -0.025em;
                       font-variation-settings: 'opsz' 144, 'SOFT' 40; max-width: 18ch;">
                {{ $page->title }}
            </h1>
        </header>

        {{-- =============== CONTENT =============== --}}
        <div class="max-w-[1240px] mx-auto px-5 lg:px-8 pb-20">
            <div class="max-w-[68ch] mx-auto text-ink"
                 style="font-size: 19px; line-height: 1.75;">
                @if ($page->content)
                    <div class="page-content [&>*:first-child]:first-letter:font-display
                                [&>*:first-child]:first-letter:text-[5.5rem]
                                [&>*:first-child]:first-letter:leading-[0.85]
                                [&>*:first-child]:first-letter:float-left
                                [&>*:first-child]:first-letter:pr-3
                                [&>*:first-child]:first-letter:pt-1
                                [&>*:first-child]:first-letter:text-coral
                                [&_p]:mb-6">
                        @php
                            $paragraphs = collect(preg_split("/\r\n\r\n|\n\n|\r\n|\n/u", trim((string) $page->content)))
                                ->map(fn ($p) => trim($p))
                                ->filter()
                                ->values();
                        @endphp
                        @foreach ($paragraphs as $p)
                            <p>{!! nl2br(e($p)) !!}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-ink-soft italic">Esta página todavía no tiene contenido.</p>
                @endif
            </div>

            {{-- =============== GALERÍA (si hay media adicional) =============== --}}
            @if ($page->media->count() > 1)
                <div class="max-w-[1240px] mx-auto mt-16">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($page->media->skip(1) as $m)
                            @php
                                $url = Str::startsWith((string) $m->path, ['http://', 'https://', '/'])
                                    ? $m->path
                                    : asset('storage/' . ltrim((string) $m->path, '/'));
                            @endphp
                            <figure class="aspect-[4/3] rounded-md overflow-hidden border border-ink-line bg-sand-2 shadow-card">
                                <img src="{{ $url }}"
                                     alt="{{ $m->alt ?? $page->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover">
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <x-public.wave-divider />
    </article>

</x-public.layouts.main>
