@php
    use Illuminate\Support\Str;

    $hero = $item->media->first();
    $heroUrl = $hero?->path
        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
            ? $hero->path
            : asset('storage/' . ltrim($hero->path, '/')))
        : null;

    $allMedia = $item->media->map(function ($m) {
        $url = Str::startsWith((string) $m->path, ['http://', 'https://', '/'])
            ? $m->path
            : asset('storage/' . ltrim((string) $m->path, '/'));
        return ['url' => $url, 'alt' => $m->alt ?? ''];
    })->values();

    $paragraphs = collect(preg_split("/\r\n\r\n|\r\n|\n\n|\n/u", trim((string) $item->description)))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();

    // YouTube embed simple desde URL.
    $youtubeId = null;
    if ($item->video_url) {
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', $item->video_url, $m)) {
            $youtubeId = $m[1];
        }
    }
@endphp

<x-public.layouts.main :title="$item->title" :description="Str::limit(strip_tags((string) $item->description), 160)" :image="$heroUrl">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li><a href="{{ route('clasificados.index') }}" class="hover:text-coral transition-colors">Clasificados</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink truncate max-w-[40ch]">{{ Str::limit($item->title, 30) }}</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HEADER =============== --}}
    <article class="bg-sand">
        <header class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-10">
            <div class="max-w-[68ch] mx-auto">
                @if ($item->category)
                    <div class="eyebrow mb-6">{{ $item->category->name }}</div>
                @endif

                <h1 class="font-display font-normal leading-[0.95] text-ink mb-6"
                    style="font-size: clamp(36px, 5.6vw, 78px); letter-spacing: -0.025em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    {{ $item->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-3 font-mono text-[12px] tracking-[0.14em] uppercase text-ink-soft">
                    @if ($item->published_at)
                        <span>{{ $item->published_at->locale('es')->isoFormat('D MMMM YYYY') }}</span>
                    @endif
                    @if ($item->address)
                        <span aria-hidden="true" class="text-ink-line">·</span>
                        <span>{{ $item->address }}</span>
                    @endif
                </div>
            </div>
        </header>

        {{-- =============== HERO + GALLERY CAROUSEL =============== --}}
        @if ($allMedia->isNotEmpty())
            <div x-data="{
                index: 0,
                images: @js($allMedia),
                next() { this.index = (this.index + 1) % this.images.length; },
                prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; },
            }" class="max-w-[1240px] mx-auto px-5 lg:px-8 mb-16">
                <figure class="relative aspect-[16/9] rounded-md overflow-hidden border border-ink-line shadow-card bg-sand-2">
                    <template x-for="(img, i) in images" :key="i">
                        <img :src="img.url"
                             :alt="img.alt"
                             x-show="index === i"
                             x-transition:enter="transition-opacity duration-500"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0 w-full h-full object-cover"
                             loading="eager">
                    </template>

                    @if ($allMedia->count() > 1)
                        <button type="button"
                                @click="prev()"
                                aria-label="Anterior"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-foam/90 border border-ink-line text-ink hover:bg-coral hover:text-sand hover:border-coral transition-colors flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 4l-4 4 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button"
                                @click="next()"
                                aria-label="Siguiente"
                                class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-foam/90 border border-ink-line text-ink hover:bg-coral hover:text-sand hover:border-coral transition-colors flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 bg-foam/90 backdrop-blur-sm border border-ink-line rounded-full px-3 py-2">
                            <template x-for="(img, i) in images" :key="i">
                                <button type="button"
                                        @click="index = i"
                                        :class="index === i ? 'bg-coral' : 'bg-ink-line hover:bg-coral'"
                                        class="w-2 h-2 rounded-full transition-colors"
                                        aria-label="Ir a imagen"></button>
                            </template>
                        </div>
                    @endif
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

        {{-- =============== BODY + CONTACT FORM =============== --}}
        <div class="max-w-[1240px] mx-auto px-5 lg:px-8 pb-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                {{-- Left: descripción + video --}}
                <div class="lg:col-span-2">
                    @if ($paragraphs->isNotEmpty())
                        <div class="text-ink" style="font-size: 19px; line-height: 1.7;">
                            @foreach ($paragraphs as $p)
                                <p class="mb-6">{{ $p }}</p>
                            @endforeach
                        </div>
                    @else
                        <p class="text-ink-soft text-[17px] italic">
                            El anunciante no agregó descripción.
                        </p>
                    @endif

                    @if ($youtubeId)
                        <div class="mt-10">
                            <span class="eyebrow block mb-5">Video</span>
                            <div class="aspect-video rounded-md overflow-hidden border border-ink-line shadow-card bg-ink">
                                <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                        title="Video del clasificado"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        class="w-full h-full"></iframe>
                            </div>
                        </div>
                    @elseif ($item->video_url)
                        <div class="mt-10">
                            <span class="eyebrow block mb-5">Video</span>
                            <a href="{{ $item->video_url }}"
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 text-coral hover:text-ink transition-colors break-all">
                                {{ $item->video_url }}
                            </a>
                        </div>
                    @endif

                    @if ($item->latitude && $item->longitude)
                        <div id="mapa" class="mt-12 scroll-mt-24">
                            <span class="eyebrow block mb-5">Ubicación</span>
                            <x-public.map :lat="$item->latitude" :lng="$item->longitude" :label="$item->title" />
                        </div>
                    @endif
                </div>

                {{-- Right: contact form (sticky) --}}
                <aside class="lg:sticky lg:top-24 self-start">
                    <div class="bg-foam border border-ink-line rounded-md p-7 shadow-card">
                        <span class="eyebrow block mb-5">Contactar al anunciante</span>

                        @if ($item->contact_name)
                            <div class="mb-5">
                                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">A cargo de</div>
                                <div class="text-ink text-[16px] font-medium">{{ $item->contact_name }}</div>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-5 bg-sand-2 border border-sun-deep rounded p-4">
                                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun-deep mb-1">Listo</div>
                                <p class="text-ink text-[15px]">{{ session('success') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('clasificados.contact', $item) }}" class="flex flex-col gap-4">
                            @csrf

                            <div>
                                <label for="name" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Nombre</label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('name') border-coral @enderror">
                                @error('name')
                                    <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Email</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('email') border-coral @enderror">
                                @error('email')
                                    <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Teléfono <span class="opacity-50 normal-case tracking-normal">(opcional)</span></label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('phone') border-coral @enderror">
                                @error('phone')
                                    <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Mensaje</label>
                                <textarea id="message"
                                          name="message"
                                          rows="5"
                                          required
                                          class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('message') border-coral @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn-primary justify-center mt-2">
                                Enviar mensaje
                                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>

                        @if ($item->latitude && $item->longitude)
                            <a href="#mapa"
                               class="inline-flex items-center gap-2 mt-5 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
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
    </article>

    {{-- =============== RELATED =============== --}}
    @if ($related->isNotEmpty())
        <section class="bg-sand-2 border-t border-ink-line py-20">
            <div class="max-w-[1360px] mx-auto px-5 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-6 mb-12">
                    <h2 class="font-display font-normal leading-[0.95] text-ink"
                        style="font-size: clamp(36px, 4vw, 56px); letter-spacing: -0.025em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Más clasificados
                    </h2>
                    <a href="{{ route('clasificados.index') }}"
                       class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                        Ver todos
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
                        @endphp
                        <a href="{{ route('clasificados.show', $r) }}"
                           class="group block bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                                  transition-[transform,box-shadow] duration-300 hover:-translate-y-1 hover:shadow-lift">
                            <div class="aspect-[4/3] bg-sand-2 bg-cover bg-center"
                                 @if ($rThumbUrl) style="background-image: url('{{ $rThumbUrl }}');" @endif></div>
                            <div class="p-5">
                                @if ($r->category)
                                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral mb-2">
                                        {{ $r->category->name }}
                                    </div>
                                @endif
                                <h3 class="font-display text-[18px] font-medium leading-[1.2] text-ink group-hover:text-coral transition-colors"
                                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                                    {{ $r->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-public.layouts.main>
