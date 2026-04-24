@php
    use Illuminate\Support\Str;

    $hero    = $event->media->first();
    $heroUrl = $hero?->path
        ? (Str::startsWith($hero->path, ['http://', 'https://', '/'])
            ? $hero->path
            : asset('storage/'.ltrim($hero->path, '/')))
        : null;

    $gallery = $event->media->skip(1)->values();

    // Body parser: separa por dobles newlines (igual que en novedades).
    $paragraphs = collect(preg_split("/\r\n\r\n|\r\n|\n\n|\n/u", trim((string) $event->description)))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();

    $hasCustomForm = in_array($event->slug, ['fiesta-del-tejo', 'fiesta-de-la-primavera'], true);
@endphp

<x-public.layouts.main :title="$event->title" :description="$event->location ? $event->title.' — '.$event->location : $event->title" :image="$heroUrl">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li><a href="{{ route('eventos.index') }}" class="hover:text-coral transition-colors">Eventos</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink truncate max-w-[40ch]">{{ Str::limit($event->title, 30) }}</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HERO =============== --}}
    <section class="relative bg-ink text-sand overflow-hidden">
        @if ($heroUrl)
            <div class="absolute inset-0">
                <img src="{{ $heroUrl }}"
                     alt="{{ $hero->alt ?? $event->title }}"
                     class="w-full h-full object-cover opacity-50"
                     loading="eager"
                     fetchpriority="high">
                <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/70 to-ink/40"></div>
            </div>
        @endif
        <div class="relative max-w-[1360px] mx-auto px-5 lg:px-8 py-24 lg:py-32">
            <div class="grid lg:grid-cols-[auto_1fr] gap-10 items-end">
                @if ($event->starts_at)
                    {{-- Date stamp grande sol-on-navy --}}
                    <div class="bg-sun text-ink text-center px-3 py-4 rounded font-mono leading-none w-[140px] shrink-0 shadow-lift">
                        <span class="block text-[64px] font-medium leading-none">
                            {{ $event->starts_at->format('d') }}
                        </span>
                        <span class="block text-[14px] tracking-[0.18em] uppercase mt-2">
                            {{ $event->starts_at->locale('es')->isoFormat('MMM') }}
                        </span>
                        <span class="block text-[10px] tracking-[0.15em] uppercase mt-1 text-sun-deep">
                            {{ $event->starts_at->format('Y') }}
                        </span>
                    </div>
                @endif
                <div>
                    <span class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">
                        @if ($event->featured)
                            <span class="bg-coral text-sand px-2 py-1 rounded-full">Destacado</span>
                        @endif
                        Agenda · El Cóndor
                    </span>
                    <h1 class="font-display font-normal leading-[0.92] mb-5"
                        style="font-size: clamp(40px, 7vw, 96px); letter-spacing: -0.03em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        {{ $event->title }}
                    </h1>
                    @if ($event->starts_at || $event->location)
                        <div class="font-mono text-[13px] tracking-[0.12em] uppercase text-sand/80 leading-[1.8]">
                            @if ($event->starts_at)
                                <div>
                                    {{ $event->starts_at->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
                                    @if (! $event->all_day)
                                        · {{ $event->starts_at->format('H:i') }} hs
                                    @endif
                                </div>
                            @endif
                            @if ($event->location)
                                <div class="text-coral-soft">{{ $event->location }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- =============== FLASH SUCCESS =============== --}}
    @if (session('success'))
        <div class="bg-sun-deep text-sand">
            <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 flex items-center gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-mono text-[12px] tracking-[0.1em] uppercase">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- =============== BODY 2-COL =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-20 grid lg:grid-cols-[1fr_360px] gap-16">

            {{-- LEFT: descripción + galería --}}
            <article>
                @if ($paragraphs->isNotEmpty())
                    <div class="prose-lg text-ink max-w-[68ch]" style="font-size: 18px; line-height: 1.7;">
                        @foreach ($paragraphs as $p)
                            <p class="mb-5">{!! nl2br(e($p)) !!}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-ink-soft italic">Más detalles próximamente.</p>
                @endif

                @if ($gallery->isNotEmpty())
                    <div class="mt-14">
                        <span class="eyebrow block mb-5">Galería</span>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($gallery as $img)
                                @php
                                    $imgUrl = $img->path
                                        ? (Str::startsWith($img->path, ['http://', 'https://', '/'])
                                            ? $img->path
                                            : asset('storage/'.ltrim($img->path, '/')))
                                        : null;
                                @endphp
                                @if ($imgUrl)
                                    <figure class="aspect-[4/3] rounded overflow-hidden border border-ink-line bg-sand-2">
                                        <img src="{{ $imgUrl }}"
                                             alt="{{ $img->alt ?? $event->title }}"
                                             class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700"
                                             loading="lazy">
                                    </figure>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            {{-- RIGHT: ficha sticky --}}
            <aside class="lg:sticky lg:top-8 self-start">
                <div class="bg-foam border border-ink-line rounded-md p-7 shadow-card">
                    <span class="eyebrow block mb-5">Detalles</span>

                    <dl class="space-y-5">
                        @if ($event->starts_at)
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft mb-1">Comienza</dt>
                                <dd class="font-display text-[20px] text-ink leading-tight"
                                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                                    {{ $event->starts_at->locale('es')->isoFormat('D MMM YYYY') }}
                                    @if (! $event->all_day)
                                        <span class="font-mono text-[14px] text-coral block mt-1">{{ $event->starts_at->format('H:i') }} hs</span>
                                    @endif
                                </dd>
                            </div>
                        @endif

                        @if ($event->ends_at)
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft mb-1">Termina</dt>
                                <dd class="font-display text-[18px] text-ink leading-tight"
                                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                                    {{ $event->ends_at->locale('es')->isoFormat('D MMM YYYY') }}
                                    @if (! $event->all_day)
                                        <span class="font-mono text-[13px] text-ink-soft block mt-1">{{ $event->ends_at->format('H:i') }} hs</span>
                                    @endif
                                </dd>
                            </div>
                        @endif

                        @if ($event->location)
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft mb-1">Lugar</dt>
                                <dd class="font-mono text-[13px] text-ink leading-tight">{{ $event->location }}</dd>
                            </div>
                        @endif

                        @if ($event->external_url)
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft mb-1">Más info</dt>
                                <dd>
                                    <a href="{{ $event->external_url }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 font-mono text-[12px] text-coral hover:text-ink transition-colors break-all">
                                        {{ Str::limit($event->external_url, 40) }}
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M7 17L17 7M17 7H8M17 7v9" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-7 pt-6 border-t border-ink-line flex flex-col gap-3">
                        @if ($event->accepts_registrations)
                            <a href="#inscripcion" class="btn-primary justify-center">
                                Inscribirme
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                                </svg>
                            </a>
                        @endif
                        @if ($event->starts_at)
                            <a href="{{ route('eventos.show', $event) }}#detalles"
                               class="btn-ghost justify-center text-[14px]">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                                Agregar al calendario
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- =============== INSCRIPCIÓN =============== --}}
    @if ($event->accepts_registrations)
        <section id="inscripcion" class="bg-sand-2 border-t border-ink-line py-20">
            <div class="max-w-[760px] mx-auto px-5 lg:px-8">
                <div class="text-center mb-10">
                    <span class="eyebrow block mb-3">Inscripción</span>
                    <h2 class="font-display font-normal leading-[0.95] text-ink"
                        style="font-size: clamp(36px, 5vw, 64px); letter-spacing: -0.025em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Sumate al
                        <em class="not-italic font-display italic text-sun-deep"
                            style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">evento</em>
                    </h2>
                    <p class="mt-4 text-ink-soft max-w-[44ch] mx-auto">
                        Completá el formulario y nos comunicamos por mail con los detalles finales.
                    </p>
                </div>

                <form method="POST" action="{{ route('eventos.register', $event) }}"
                      class="bg-foam border border-ink-line rounded-md p-7 lg:p-10 shadow-card space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <label class="block md:col-span-2">
                            <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Nombre completo *</span>
                            <input type="text" name="name" maxlength="200" required
                                   value="{{ old('name') }}"
                                   class="w-full bg-sand border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
                            @error('name')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
                        </label>

                        <label class="block">
                            <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Email *</span>
                            <input type="email" name="email" maxlength="200" required
                                   value="{{ old('email') }}"
                                   class="w-full bg-sand border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
                            @error('email')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
                        </label>

                        <label class="block">
                            <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Teléfono</span>
                            <input type="tel" name="phone" maxlength="100"
                                   value="{{ old('phone') }}"
                                   class="w-full bg-sand border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
                            @error('phone')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
                        </label>
                    </div>

                    @if ($event->slug === 'fiesta-del-tejo')
                        <div class="pt-3 mt-3 border-t border-ink-line">
                            <span class="eyebrow block mb-4">Datos de la Fiesta del Tejo</span>
                            @include('public.eventos._forms.tejo')
                        </div>
                    @elseif ($event->slug === 'fiesta-de-la-primavera')
                        <div class="pt-3 mt-3 border-t border-ink-line">
                            <span class="eyebrow block mb-4">Datos de la Fiesta de la Primavera</span>
                            @include('public.eventos._forms.primavera')
                        </div>
                    @endif

                    <div class="pt-4 flex flex-wrap items-center justify-between gap-4">
                        <p class="font-mono text-[11px] text-ink-soft max-w-[40ch]">
                            Al enviar aceptás que el municipio se contacte por mail
                            con la confirmación de tu inscripción.
                        </p>
                        <button type="submit" class="btn-primary">
                            Enviar inscripción
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    @else
        <section class="bg-sand-2 border-t border-ink-line py-16">
            <div class="max-w-[680px] mx-auto px-5 lg:px-8 text-center">
                <span class="eyebrow block mb-3">Sin inscripción online</span>
                <p class="font-display text-[24px] text-ink"
                   style="font-variation-settings: 'opsz' 48, 'SOFT' 50;">
                    Evento sin inscripción online — consultar con turismo.
                </p>
                <a href="mailto:turismo@elcondor.gob.ar"
                   class="inline-flex items-center gap-2 mt-5 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                    Escribir a turismo
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </section>
    @endif

</x-public.layouts.main>
