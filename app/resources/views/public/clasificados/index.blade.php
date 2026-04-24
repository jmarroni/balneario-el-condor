@php
    use Illuminate\Support\Str;
@endphp
<x-public.layouts.main title="Clasificados" description="Vendo, alquilo, busco, ofrezco — clasificados de la comunidad de El Cóndor.">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Mercado de la comunidad',
        'titleStart' => 'Clasi',
        'titleEnd'   => 'ficados',
        'lede'       => 'Vecinos publicando lo que ofrecen, lo que buscan, lo que prestan. El cartel del almacén, ahora online.',
    ])

    {{-- =============== CATEGORÍAS + SEARCH =============== --}}
    <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex gap-2.5 items-center overflow-x-auto min-w-0">
                <a href="{{ route('clasificados.index', array_filter(['q' => $q])) }}"
                   @class([
                       'inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors whitespace-nowrap',
                       'bg-coral text-sand border-coral'        => ! $current,
                       'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current,
                   ])>
                    Todos
                    <span class="opacity-70">{{ $totalCount }}</span>
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('clasificados.index', array_filter(['categoria' => $cat->slug, 'q' => $q])) }}"
                       @class([
                           'inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors whitespace-nowrap',
                           'bg-coral text-sand border-coral'        => $current === $cat->slug,
                           'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current !== $cat->slug,
                       ])>
                        {{ $cat->name }}
                        <span class="opacity-70">{{ $counts[$cat->id] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('clasificados.index') }}" class="flex gap-2 items-center shrink-0">
                @if ($current)
                    <input type="hidden" name="categoria" value="{{ $current }}">
                @endif
                <label for="q" class="sr-only">Buscar</label>
                <input type="search"
                       id="q"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Buscar…"
                       class="font-mono text-[12px] uppercase tracking-[0.1em] bg-foam border border-ink-line rounded-full px-4 py-2 w-[200px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral">
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

            @if ($items->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin resultados</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        @if ($q)
                            No encontramos clasificados para "<em class="text-coral italic">{{ $q }}</em>"
                        @else
                            Pronto más avisos
                        @endif
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[44ch] mx-auto">
                        Probá con otra categoría o sin filtros.
                    </p>
                    @if ($q || $current)
                        <a href="{{ route('clasificados.index') }}"
                           class="inline-flex items-center gap-2 mt-6 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                            Ver todos los clasificados
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $item)
                        @php
                            $thumb = $item->media->first();
                            $thumbUrl = $thumb?->path
                                ? (Str::startsWith($thumb->path, ['http://', 'https://', '/'])
                                    ? $thumb->path
                                    : asset('storage/' . ltrim($thumb->path, '/')))
                                : null;
                        @endphp
                        <a href="{{ route('clasificados.show', $item) }}"
                           class="group block bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                                  transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                                  hover:-translate-y-1 hover:shadow-lift">

                            <div class="aspect-[4/3] bg-sand-2 bg-cover bg-center relative overflow-hidden"
                                 @if ($thumbUrl) style="background-image: url('{{ $thumbUrl }}');" @endif>
                                @if (! $thumbUrl)
                                    <div class="absolute inset-0 flex items-center justify-center text-ink-line">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                                            <circle cx="9" cy="11" r="1.5"/>
                                            <path d="M3 17l5-5 4 4 3-3 6 6"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 flex flex-col flex-1">
                                @if ($item->category)
                                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral mb-2">
                                        {{ $item->category->name }}
                                    </div>
                                @endif

                                <h3 class="font-display text-[20px] font-medium leading-[1.2] text-ink mb-3 group-hover:text-coral transition-colors"
                                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40; letter-spacing: -0.015em;">
                                    {{ $item->title }}
                                </h3>

                                <p class="text-ink-soft text-[14px] leading-snug line-clamp-2 mb-4 flex-1">
                                    {{ Str::limit(strip_tags((string) $item->description), 130) }}
                                </p>

                                <div class="flex items-center justify-between font-mono text-[11px] text-ink-soft pt-3 border-t border-ink-line mt-auto">
                                    <span>
                                        @if ($item->published_at)
                                            {{ $item->published_at->locale('es')->isoFormat('D MMM YYYY') }}
                                        @else
                                            {{ $item->created_at->locale('es')->isoFormat('D MMM YYYY') }}
                                        @endif
                                    </span>
                                    @if ($item->address)
                                        <span class="line-clamp-1 max-w-[18ch] text-right">{{ $item->address }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($items->hasPages())
                    <div class="mt-16 pt-8 border-t border-ink-line">
                        {{ $items->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
