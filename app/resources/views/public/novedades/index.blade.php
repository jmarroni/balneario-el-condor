<x-public.layouts.main title="Novedades">

    {{-- =============== EDITORIAL HEADER =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-20 pb-14">
            <div class="flex flex-wrap items-end justify-between gap-10">
                <div class="max-w-[20ch]">
                    <span class="eyebrow block mb-4">Crónica costera</span>
                    <h1 class="font-display font-normal leading-[0.92] text-ink"
                        style="font-size: clamp(56px, 8.4vw, 128px); letter-spacing: -0.035em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Nove<em class="not-italic font-display italic text-sun-deep"
                                style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">dades</em>
                    </h1>
                </div>
                <div class="max-w-[44ch] text-ink-soft text-[19px] leading-[1.55]">
                    Lo que pasa en el pueblo, contado por quienes lo viven. Avisos del municipio,
                    historias de la temporada, voces que cruzan la ría y vuelven con el viento.
                </div>
            </div>
        </div>
    </section>

    {{-- =============== CATEGORY TABS =============== --}}
    @if ($categories->isNotEmpty())
        <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
            <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 overflow-x-auto">
                <div class="flex gap-2.5 items-center min-w-max">
                    <a href="{{ route('novedades.index') }}"
                       @class([
                           'inline-flex items-center font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                           'bg-coral text-sand border-coral'        => ! $current,
                           'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current,
                       ])>
                        Todas
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('novedades.index', ['categoria' => $category->slug]) }}"
                           @class([
                               'inline-flex items-center font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                               'bg-coral text-sand border-coral'        => $current === $category->slug,
                               'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current !== $category->slug,
                           ])>
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- =============== FEATURED + GRID =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-12 pb-24">

            @if ($featured)
                <div class="mb-20">
                    <x-public.article-card :news="$featured" variant="featured" />
                </div>
            @endif

            @if ($news->isEmpty() && ! $featured)
                {{-- Empty state --}}
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin novedades</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        @if ($current)
                            No hay notas en esta categoría todavía
                        @else
                            Pronto más novedades
                        @endif
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[44ch] mx-auto">
                        Estamos preparando la próxima crónica del balneario.
                    </p>
                    @if ($current)
                        <a href="{{ route('novedades.index') }}"
                           class="inline-flex items-center gap-2 mt-7 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 4l-4 4 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Ver todas
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    @foreach ($news as $item)
                        <x-public.article-card :news="$item" />
                    @endforeach
                </div>

                {{-- =============== PAGINATION =============== --}}
                @if ($news->hasPages())
                    <div class="mt-16 pt-8 border-t border-ink-line">
                        {{ $news->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
