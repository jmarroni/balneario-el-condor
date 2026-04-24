@php
    use Illuminate\Support\Str;

    $imagesPayload = $images->getCollection()->map(function ($img) {
        $url = Str::startsWith((string) $img->path, ['http://', 'https://', '/'])
            ? $img->path
            : asset('storage/' . ltrim((string) $img->path, '/'));
        return [
            'url'   => $url,
            'title' => $img->title ?? '',
            'desc'  => $img->description ?? '',
            'taken' => $img->taken_on?->locale('es')->isoFormat('D MMMM YYYY') ?? '',
        ];
    })->values();
@endphp
<x-public.layouts.main title="Galería" description="Atardeceres, faros, tormentas y veranos. La galería visual de Balneario El Cóndor.">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'El pueblo en imágenes',
        'titleStart' => 'Gale',
        'titleEnd'   => 'ría',
        'lede'       => 'Una ventana hacia el mar y la ría. Fotos de vecinos, fotógrafos y visitantes que dejaron su mirada en El Cóndor.',
    ])

    {{-- =============== YEAR FILTER =============== --}}
    @if (! empty($years))
        <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
            <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 overflow-x-auto">
                <div class="flex gap-2.5 items-center min-w-max">
                    <a href="{{ route('galeria.index') }}"
                       @class([
                           'inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                           'bg-coral text-sand border-coral'        => ! $current,
                           'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current,
                       ])>
                        Todos
                        <span class="opacity-70">{{ $totalCount }}</span>
                    </a>
                    @foreach ($years as $yr => $count)
                        <a href="{{ route('galeria.index', ['anio' => $yr]) }}"
                           @class([
                               'inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                               'bg-coral text-sand border-coral'        => $current === (int) $yr,
                               'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current !== (int) $yr,
                           ])>
                            {{ $yr }}
                            <span class="opacity-70">{{ $count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- =============== MASONRY + LIGHTBOX =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-12 pb-24">

            @if ($images->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin imágenes</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        @if ($current)
                            No hay imágenes de {{ $current }}
                        @else
                            La galería está vacía
                        @endif
                    </h2>
                </div>
            @else
                <div x-data="{
                    open: false,
                    index: 0,
                    images: @js($imagesPayload),
                    show(i) {
                        this.index = i;
                        this.open = true;
                        document.body.style.overflow = 'hidden';
                    },
                    close() {
                        this.open = false;
                        document.body.style.overflow = '';
                    },
                    next() { this.index = (this.index + 1) % this.images.length; },
                    prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; },
                }"
                @keydown.window.escape="open && close()"
                @keydown.window.arrow-right="open && next()"
                @keydown.window.arrow-left="open && prev()">

                    {{-- Masonry grid --}}
                    <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4">
                        @foreach ($images as $i => $img)
                            @php
                                $imgUrl = Str::startsWith((string) $img->path, ['http://', 'https://', '/'])
                                    ? $img->path
                                    : asset('storage/' . ltrim((string) $img->path, '/'));
                            @endphp
                            <button type="button"
                                    @click="show({{ $i }})"
                                    class="group block w-full mb-4 break-inside-avoid relative overflow-hidden rounded border border-ink-line bg-sand-2 cursor-zoom-in focus:outline-none focus:ring-2 focus:ring-coral focus:ring-offset-2 focus:ring-offset-sand">
                                <img src="{{ $imgUrl }}"
                                     alt="{{ $img->title ?? '' }}"
                                     loading="lazy"
                                     class="w-full h-auto block transition-transform duration-700 group-hover:scale-[1.04]">
                                @if ($img->title)
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/85 via-ink/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-4 pt-12 pb-4">
                                        <h3 class="font-display text-sand text-[15px] leading-tight"
                                            style="font-variation-settings: 'opsz' 48, 'SOFT' 30;">
                                            {{ $img->title }}
                                        </h3>
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    {{-- Lightbox modal --}}
                    <div x-show="open"
                         x-transition:enter="transition-opacity duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-ink/95 backdrop-blur-sm z-[200] flex flex-col items-center justify-center"
                         style="display: none;"
                         role="dialog"
                         aria-modal="true"
                         aria-label="Visor de imagen">

                        {{-- Close --}}
                        <button type="button"
                                @click="close()"
                                aria-label="Cerrar"
                                class="absolute top-5 right-5 w-12 h-12 rounded-full bg-foam/10 hover:bg-coral text-sand border border-sand/20 hover:border-coral transition-colors flex items-center justify-center z-10">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 6l12 12M6 18L18 6" stroke-linecap="round"/>
                            </svg>
                        </button>

                        {{-- Counter --}}
                        <div class="absolute top-5 left-5 font-mono text-[12px] tracking-[0.18em] uppercase text-sand/80">
                            <span x-text="index + 1"></span> / <span x-text="images.length"></span>
                        </div>

                        {{-- Prev --}}
                        <button type="button"
                                @click="prev()"
                                aria-label="Anterior"
                                class="absolute left-5 top-1/2 -translate-y-1/2 w-14 h-14 rounded-full bg-foam/10 hover:bg-coral text-sand border border-sand/20 hover:border-coral transition-colors flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 4l-4 4 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        {{-- Image --}}
                        <figure class="px-20 max-w-[95vw] flex flex-col items-center gap-5">
                            <img :src="images[index].url"
                                 :alt="images[index].title"
                                 class="max-w-[88vw] max-h-[78vh] rounded shadow-lift bg-ink object-contain">
                            <figcaption class="text-center max-w-[64ch]">
                                <h2 class="font-display text-sand text-[24px] leading-tight mb-2"
                                    style="font-variation-settings: 'opsz' 144, 'SOFT' 40;"
                                    x-text="images[index].title"></h2>
                                <p class="text-sand/75 text-[15px] mb-2" x-show="images[index].desc" x-text="images[index].desc"></p>
                                <p class="font-mono text-[11px] tracking-[0.18em] uppercase text-sun"
                                   x-show="images[index].taken"
                                   x-text="images[index].taken"></p>
                            </figcaption>
                        </figure>

                        {{-- Next --}}
                        <button type="button"
                                @click="next()"
                                aria-label="Siguiente"
                                class="absolute right-5 top-1/2 -translate-y-1/2 w-14 h-14 rounded-full bg-foam/10 hover:bg-coral text-sand border border-sand/20 hover:border-coral transition-colors flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @if ($images->hasPages())
                    <div class="mt-16 pt-8 border-t border-ink-line">
                        {{ $images->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
