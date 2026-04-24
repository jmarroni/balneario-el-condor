@php
    use Illuminate\Support\Str;
@endphp
<x-public.layouts.main title="Recetario" description="Cocina costera patagónica — recetas con producto del mar y la ría, recopiladas en El Cóndor.">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Cocina costera',
        'titleStart' => 'Recet',
        'titleEnd'   => 'ario',
        'lede'       => 'Mariscos, pesca del día y sabores de la Patagonia. Recetas que cruzaron generaciones de cocineros y abuelas.',
    ])

    {{-- =============== SEARCH =============== --}}
    <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 flex flex-wrap gap-4 items-center justify-between">
            <span class="font-mono text-[11px] tracking-[0.18em] uppercase text-ink-soft">
                {{ $totalCount }} {{ $totalCount === 1 ? 'receta' : 'recetas' }}
            </span>

            <form method="GET" action="{{ route('recetas.index') }}" class="flex gap-2 items-center">
                <label for="q" class="sr-only">Buscar receta</label>
                <input type="search"
                       id="q"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Buscar receta o ingrediente…"
                       class="font-mono text-[12px] uppercase tracking-[0.1em] bg-foam border border-ink-line rounded-full px-4 py-2 w-[260px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral">
                <button type="submit" class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                    Buscar
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="7" cy="7" r="5"/>
                        <path d="M11 11l3 3" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
        </div>
    </section>

    {{-- =============== GRID =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-12 pb-24">

            @if ($recipes->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin resultados</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        @if ($q)
                            No encontramos recetas para "<em class="text-coral italic">{{ $q }}</em>"
                        @else
                            Pronto más recetas
                        @endif
                    </h2>
                    @if ($q)
                        <a href="{{ route('recetas.index') }}"
                           class="inline-flex items-center gap-2 mt-6 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                            Ver todo el recetario
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($recipes as $recipe)
                        @php
                            $thumb = $recipe->media->first();
                            $thumbUrl = $thumb?->path
                                ? (Str::startsWith($thumb->path, ['http://', 'https://', '/'])
                                    ? $thumb->path
                                    : asset('storage/' . ltrim($thumb->path, '/')))
                                : null;
                            $totalMinutes = ($recipe->prep_minutes ?? 0) + ($recipe->cook_minutes ?? 0);
                        @endphp
                        <a href="{{ route('recetas.show', $recipe) }}"
                           class="group block bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                                  transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                                  hover:-translate-y-1 hover:shadow-lift">

                            <div class="aspect-[4/3] bg-sand-2 bg-cover bg-center relative overflow-hidden"
                                 @if ($thumbUrl) style="background-image: url('{{ $thumbUrl }}');" @endif>
                                @if (! $thumbUrl)
                                    <div class="absolute inset-0 flex items-center justify-center text-ink-line">
                                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                            <path d="M12 2c-3 0-5 2-5 5h10c0-3-2-5-5-5z"/>
                                            <path d="M5 9h14l-1 11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 9z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 flex flex-col flex-1">
                                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral mb-2">
                                    Receta
                                </div>

                                <h3 class="font-display text-[22px] font-medium leading-[1.2] text-ink mb-3 group-hover:text-coral transition-colors"
                                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40; letter-spacing: -0.015em;">
                                    {{ $recipe->title }}
                                </h3>

                                @if ($recipe->author)
                                    <p class="text-ink-soft text-[14px] italic mb-4 flex-1">
                                        por {{ $recipe->author }}
                                    </p>
                                @else
                                    <div class="flex-1"></div>
                                @endif

                                <div class="flex items-center gap-3 font-mono text-[11px] text-ink-soft pt-3 border-t border-ink-line mt-auto">
                                    @if ($totalMinutes > 0)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <circle cx="8" cy="8" r="6.5"/>
                                                <path d="M8 4v4l3 2" stroke-linecap="round"/>
                                            </svg>
                                            {{ $totalMinutes }} min
                                        </span>
                                    @endif
                                    @if ($recipe->servings)
                                        <span aria-hidden="true" class="text-ink-line">·</span>
                                        <span>{{ $recipe->servings }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($recipes->hasPages())
                    <div class="mt-16 pt-8 border-t border-ink-line">
                        {{ $recipes->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
