@php
    use Illuminate\Support\Str;

    $hero = $recipe->media->first();
    $heroUrl = $hero?->path
        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
            ? $hero->path
            : asset('storage/' . ltrim($hero->path, '/')))
        : null;

    $ingredients = collect(preg_split("/\r\n|\n/u", trim((string) $recipe->ingredients)))
        ->map(fn ($i) => trim(ltrim($i, "-•· \t")))
        ->filter(fn ($i) => $i !== '')
        ->values();

    $steps = collect(preg_split("/\r\n\r\n|\r\n|\n\n|\n/u", trim((string) $recipe->instructions)))
        ->map(fn ($s) => trim($s))
        ->filter(fn ($s) => $s !== '')
        ->values();

    $totalMinutes = ($recipe->prep_minutes ?? 0) + ($recipe->cook_minutes ?? 0);
@endphp

<x-public.layouts.main :title="$recipe->title" :description="Str::limit(strip_tags((string) $recipe->instructions), 160)" :image="$heroUrl">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li><a href="{{ route('recetas.index') }}" class="hover:text-coral transition-colors">Recetario</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink truncate max-w-[40ch]">{{ Str::limit($recipe->title, 30) }}</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HERO =============== --}}
    <article class="bg-sand">
        <header class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-10">
            <div class="max-w-[68ch] mx-auto text-center">
                <div class="eyebrow mb-6">Receta</div>

                <h1 class="font-display font-normal leading-[0.92] text-ink mb-6"
                    style="font-size: clamp(48px, 7.4vw, 110px); letter-spacing: -0.035em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 50;">
                    {{ $recipe->title }}
                </h1>

                @if ($recipe->author)
                    <p class="font-display italic text-ink-soft text-[20px] mb-6"
                       style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">
                        una receta de {{ $recipe->author }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 font-mono text-[12px] tracking-[0.14em] uppercase text-ink-soft">
                    @if ($recipe->prep_minutes)
                        <span class="inline-flex items-center gap-2">
                            <span class="text-coral">Prep</span>
                            <span class="text-ink">{{ $recipe->prep_minutes }} min</span>
                        </span>
                    @endif
                    @if ($recipe->cook_minutes)
                        <span class="inline-flex items-center gap-2">
                            <span class="text-coral">Cocción</span>
                            <span class="text-ink">{{ $recipe->cook_minutes }} min</span>
                        </span>
                    @endif
                    @if ($recipe->servings)
                        <span class="inline-flex items-center gap-2">
                            <span class="text-coral">Porciones</span>
                            <span class="text-ink">{{ $recipe->servings }}</span>
                        </span>
                    @endif
                    @if ($recipe->cost)
                        <span class="inline-flex items-center gap-2">
                            <span class="text-coral">Costo</span>
                            <span class="text-ink">{{ $recipe->cost }}</span>
                        </span>
                    @endif
                </div>
            </div>
        </header>

        @if ($heroUrl)
            <div class="max-w-[1240px] mx-auto px-5 lg:px-8 mb-16">
                <figure class="relative aspect-[16/9] rounded-md overflow-hidden border border-ink-line shadow-card">
                    <img src="{{ $heroUrl }}"
                         alt="{{ $hero->alt ?? $recipe->title }}"
                         class="w-full h-full object-cover"
                         loading="eager"
                         fetchpriority="high">
                </figure>
            </div>
        @else
            <div class="max-w-[1240px] mx-auto px-5 lg:px-8 mb-16">
                <div class="aspect-[16/9] rounded-md border border-ink-line bg-sand-2 flex items-center justify-center text-ink-line shadow-card">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                        <path d="M12 2c-3 0-5 2-5 5h10c0-3-2-5-5-5z"/>
                        <path d="M5 9h14l-1 11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 9z"/>
                    </svg>
                </div>
            </div>
        @endif

        {{-- =============== SPLIT: INGREDIENTES + PREPARACIÓN =============== --}}
        <div class="max-w-[1240px] mx-auto px-5 lg:px-8 pb-16">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.8fr] gap-12 lg:gap-16">

                {{-- Ingredientes (sticky) --}}
                <aside class="lg:sticky lg:top-8 self-start">
                    <div class="bg-foam border border-ink-line rounded-md p-7 shadow-card">
                        <span class="eyebrow block mb-5">Ingredientes</span>

                        @if ($ingredients->isEmpty())
                            <p class="text-ink-soft italic text-[15px]">No se cargaron ingredientes.</p>
                        @else
                            <ul class="flex flex-col gap-3">
                                @foreach ($ingredients as $ing)
                                    <li class="flex items-start gap-3 pb-3 border-b border-ink-line last:border-b-0 last:pb-0">
                                        <span class="font-mono text-coral text-[10px] mt-2 shrink-0">●</span>
                                        <span class="font-display text-ink text-[17px] leading-[1.45]"
                                              style="font-variation-settings: 'opsz' 24, 'SOFT' 30;">
                                            {{ $ing }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($recipe->servings || $totalMinutes > 0)
                            <div class="mt-7 pt-5 border-t border-ink-line grid grid-cols-2 gap-3">
                                @if ($recipe->servings)
                                    <div>
                                        <div class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft mb-1">Rinde</div>
                                        <div class="font-display text-ink text-[15px] leading-tight"
                                             style="font-variation-settings: 'opsz' 24, 'SOFT' 30;">
                                            {{ $recipe->servings }}
                                        </div>
                                    </div>
                                @endif
                                @if ($totalMinutes > 0)
                                    <div>
                                        <div class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft mb-1">Tiempo total</div>
                                        <div class="font-mono text-ink text-[18px] leading-tight">
                                            {{ $totalMinutes }} min
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </aside>

                {{-- Preparación --}}
                <div>
                    <span class="eyebrow block mb-6">Preparación</span>

                    @if ($steps->isEmpty())
                        <p class="text-ink-soft italic text-[17px]">No se cargaron las instrucciones.</p>
                    @else
                        <ol class="flex flex-col gap-8">
                            @foreach ($steps as $i => $step)
                                <li class="flex gap-5">
                                    <span class="font-mono text-coral text-[15px] shrink-0 mt-1.5 select-none w-10">
                                        {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <p class="text-ink leading-relaxed flex-1"
                                       style="font-size: 18px; line-height: 1.7;">
                                        {{ $step }}
                                    </p>
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    @if ($recipe->published_on)
                        <div class="mt-12 pt-6 border-t border-ink-line">
                            <p class="font-mono text-[11px] tracking-[0.18em] uppercase text-ink-soft">
                                Publicada el {{ $recipe->published_on->locale('es')->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>
                    @endif
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
                        Más recetas
                    </h2>
                    <a href="{{ route('recetas.index') }}"
                       class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                        Ver todo el recetario
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($related as $r)
                        @php
                            $rThumb = $r->media->first();
                            $rThumbUrl = $rThumb?->path
                                ? (Str::startsWith($rThumb->path, ['http://', 'https://', '/'])
                                    ? $rThumb->path
                                    : asset('storage/' . ltrim($rThumb->path, '/')))
                                : null;
                            $rTotal = ($r->prep_minutes ?? 0) + ($r->cook_minutes ?? 0);
                        @endphp
                        <a href="{{ route('recetas.show', $r) }}"
                           class="group block bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                                  transition-[transform,box-shadow] duration-300 hover:-translate-y-1 hover:shadow-lift">
                            <div class="aspect-[4/3] bg-sand-2 bg-cover bg-center"
                                 @if ($rThumbUrl) style="background-image: url('{{ $rThumbUrl }}');" @endif></div>
                            <div class="p-5">
                                <h3 class="font-display text-[18px] font-medium leading-[1.2] text-ink group-hover:text-coral transition-colors mb-2"
                                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                                    {{ $r->title }}
                                </h3>
                                <div class="font-mono text-[11px] text-ink-soft">
                                    @if ($rTotal > 0)
                                        {{ $rTotal }} min
                                    @endif
                                    @if ($r->servings)
                                        @if ($rTotal > 0) · @endif
                                        {{ $r->servings }}
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-public.layouts.main>
