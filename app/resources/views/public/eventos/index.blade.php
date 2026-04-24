@php
    use Illuminate\Support\Str;

    // Agrupar eventos por mes (clave 'YYYY-MM' para orden, label legible para UI).
    $grouped = $events->getCollection()->groupBy(function ($event) {
        return $event->starts_at
            ? $event->starts_at->locale('es')->isoFormat('YYYY-MM')
            : 'sin-fecha';
    });

    $monthLabel = function (string $key): string {
        if ($key === 'sin-fecha') {
            return 'Sin fecha confirmada';
        }
        [$y, $m] = explode('-', $key);

        return ucfirst(\Carbon\Carbon::create((int) $y, (int) $m, 1)->locale('es')->isoFormat('MMMM YYYY'));
    };

    $featuredHero = $featured?->media->first();
    $featuredHeroUrl = $featuredHero?->path
        ? (Str::startsWith($featuredHero->path, ['http://', 'https://', '/'])
            ? $featuredHero->path
            : asset('storage/'.ltrim($featuredHero->path, '/')))
        : null;
@endphp

<x-public.layouts.main title="Agenda · Eventos">

    {{-- =============== EDITORIAL HEADER =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-20 pb-14">
            <div class="flex flex-wrap items-end justify-between gap-10">
                <div class="max-w-[18ch]">
                    <span class="eyebrow block mb-4">Qué pasa en el pueblo</span>
                    <h1 class="font-display font-normal leading-[0.92] text-ink"
                        style="font-size: clamp(56px, 8.4vw, 128px); letter-spacing: -0.035em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        A<em class="not-italic font-display italic text-sun-deep"
                             style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">genda</em>
                    </h1>
                </div>
                <div class="max-w-[44ch] text-ink-soft text-[19px] leading-[1.55]">
                    Fiestas populares, ferias, encuentros y torneos. Lo que se viene en El Cóndor —
                    para anotar en la heladera o sumarse de improvisto.
                </div>
            </div>
        </div>
    </section>

    {{-- =============== TOGGLE PRÓXIMOS / PASADOS =============== --}}
    <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 flex gap-2.5 items-center">
            <a href="{{ route('eventos.index', ['cuando' => 'proximos']) }}"
               @class([
                   'inline-flex items-center font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                   'bg-coral text-sand border-coral'        => $filter === 'proximos',
                   'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $filter !== 'proximos',
               ])>
                Próximos
            </a>
            <a href="{{ route('eventos.index', ['cuando' => 'pasados']) }}"
               @class([
                   'inline-flex items-center font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                   'bg-coral text-sand border-coral'        => $filter === 'pasados',
                   'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $filter !== 'pasados',
               ])>
                Pasados
            </a>
        </div>
    </section>

    {{-- =============== FEATURED =============== --}}
    @if ($featured)
        <section class="bg-sand">
            <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-12">
                <a href="{{ route('eventos.show', $featured) }}"
                   class="group grid lg:grid-cols-[1.1fr_1fr] gap-10 items-stretch bg-foam border border-ink-line rounded-md overflow-hidden shadow-card hover:shadow-lift transition-shadow">
                    <div class="relative aspect-[4/3] lg:aspect-auto bg-sand-2 overflow-hidden">
                        @if ($featuredHeroUrl)
                            <img src="{{ $featuredHeroUrl }}"
                                 alt="{{ $featuredHero->alt ?? $featured->title }}"
                                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700"
                                 loading="eager">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-ink-line">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-4 left-4 inline-flex items-center gap-1.5 bg-coral text-sand px-3 py-1.5 rounded-full font-mono text-[10px] tracking-[0.2em] uppercase">
                            Destacado
                        </span>
                    </div>
                    <div class="p-8 lg:p-12 flex flex-col justify-center">
                        <span class="eyebrow mb-4">Próxima fiesta mayor</span>
                        <h2 class="font-display font-normal text-ink leading-[0.95] mb-5"
                            style="font-size: clamp(32px, 4.4vw, 60px);
                                   font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                            {{ $featured->title }}
                        </h2>
                        @if ($featured->starts_at)
                            <div class="flex items-start gap-5 mb-5">
                                <x-public.event-stamp :date="$featured->starts_at"
                                                      class="w-[72px] shrink-0 text-[28px]" />
                                <div class="font-mono text-[13px] tracking-[0.1em] uppercase text-ink-soft leading-[1.7]">
                                    <div>{{ $featured->starts_at->locale('es')->isoFormat('dddd D [de] MMMM') }}</div>
                                    @if (! $featured->all_day)
                                        <div>{{ $featured->starts_at->format('H:i') }} hs</div>
                                    @endif
                                    @if ($featured->location)
                                        <div class="text-coral">{{ $featured->location }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if ($featured->description)
                            <p class="text-ink-soft leading-[1.6] line-clamp-3">
                                {{ Str::limit(strip_tags((string) $featured->description), 220) }}
                            </p>
                        @endif
                        <span class="inline-flex items-center gap-2 mt-6 font-mono text-[11px] tracking-[0.18em] uppercase text-coral group-hover:text-ink transition-colors">
                            Ver evento
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- =============== LIST GROUPED BY MONTH =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-24">

            @if ($events->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Agenda vacía</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        @if ($filter === 'pasados')
                            No hay eventos pasados registrados
                        @else
                            No hay eventos próximos
                        @endif
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[44ch] mx-auto">
                        Estamos preparando la próxima temporada. Volvé pronto.
                    </p>
                </div>
            @else
                @foreach ($grouped as $monthKey => $items)
                    <div class="mb-14 last:mb-0">
                        <h3 class="font-display italic text-ink-soft mb-6 sticky top-[58px] bg-sand py-2 z-[5]"
                            style="font-size: clamp(28px, 3vw, 40px);
                                   font-variation-settings: 'opsz' 144, 'SOFT' 100;">
                            {{ $monthLabel($monthKey) }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($items as $event)
                                @php
                                    $hero = $event->media->first();
                                    $heroUrl = $hero?->path
                                        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
                                            ? $hero->path
                                            : asset('storage/'.ltrim($hero->path, '/')))
                                        : null;
                                @endphp
                                <a href="{{ route('eventos.show', $event) }}"
                                   class="group block bg-foam border border-ink-line rounded-md overflow-hidden shadow-card hover:shadow-lift hover:-translate-y-1 transition-all">
                                    @if ($heroUrl)
                                        <div class="relative aspect-[16/10] overflow-hidden bg-sand-2">
                                            <img src="{{ $heroUrl }}"
                                                 alt="{{ $hero->alt ?? $event->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-700"
                                                 loading="lazy">
                                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                                @if ($event->featured)
                                                    <span class="inline-flex items-center bg-coral text-sand px-2.5 py-1 rounded-full font-mono text-[9px] tracking-[0.2em] uppercase">Destacado</span>
                                                @endif
                                                @if ($event->accepts_registrations)
                                                    <span class="inline-flex items-center bg-sun text-ink px-2.5 py-1 rounded-full font-mono text-[9px] tracking-[0.2em] uppercase">Abierto a inscripciones</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="p-5 flex gap-4">
                                        @if ($event->starts_at)
                                            <x-public.event-stamp :date="$event->starts_at" class="shrink-0" />
                                        @else
                                            <div class="bg-ink/10 text-ink text-center px-1 py-1.5 rounded-[3px] font-mono leading-none h-fit shrink-0">
                                                <span class="block text-[24px] font-medium">·</span>
                                                <span class="block text-[9px] tracking-[0.15em] uppercase mt-1">A confirmar</span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            @if (! $heroUrl && ($event->featured || $event->accepts_registrations))
                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @if ($event->featured)
                                                        <span class="inline-flex items-center bg-coral text-sand px-2 py-0.5 rounded-full font-mono text-[9px] tracking-[0.2em] uppercase">Destacado</span>
                                                    @endif
                                                    @if ($event->accepts_registrations)
                                                        <span class="inline-flex items-center bg-sun text-ink px-2 py-0.5 rounded-full font-mono text-[9px] tracking-[0.2em] uppercase">Inscripciones</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <h4 class="font-display text-ink leading-tight mb-1.5 truncate"
                                                style="font-size: 22px; font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                                                {{ $event->title }}
                                            </h4>
                                            @if ($event->location)
                                                <div class="font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft truncate">
                                                    {{ $event->location }}
                                                </div>
                                            @endif
                                            @if ($event->description)
                                                <p class="mt-2 text-[13px] text-ink-soft leading-[1.5] line-clamp-2">
                                                    {{ Str::limit(strip_tags((string) $event->description), 110) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if ($events->hasPages())
                    <div class="mt-16 pt-8 border-t border-ink-line">
                        {{ $events->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
