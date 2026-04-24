@php
    use Illuminate\Support\Str;

    // Galería tile fallbacks (mismas imágenes Unsplash del preview)
    $galleryFallbacks = [
        'https://images.unsplash.com/photo-1464039397811-45ae9b6c3bf8?w=600&q=80',
        'https://images.unsplash.com/photo-1500964757637-c85e8a162699?w=600&q=80',
        'https://images.unsplash.com/photo-1502126324834-38f8e02d776b?w=600&q=80',
        'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?w=600&q=80',
    ];
@endphp

<x-public.layouts.main title="Balneario El Cóndor">

    {{-- =============== HERO =============== --}}
    <x-public.hero :tide="$todayTide" />

    {{-- =============== WAVE DIVIDER =============== --}}
    <x-public.wave-divider />

    {{-- =============== BENTO MODULES =============== --}}
    <section id="novedades" class="max-w-[1360px] mx-auto px-5 lg:px-8">

        <div class="flex flex-wrap justify-between items-end gap-10 pt-20 pb-12">
            <h2 class="font-display font-normal leading-[0.95] text-ink max-w-[11ch]"
                style="font-size: clamp(44px, 5vw, 72px); letter-spacing: -0.025em; font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                Lo que pasa en el
                <em class="not-italic font-display italic text-sun-deep"
                    style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">balneario</em>
            </h2>
            <div class="max-w-[44ch] text-ink-soft text-[17px]">
                <span class="eyebrow block mb-2.5">Crónica costera</span>
                Del río al mar, la agenda de verano, los hospedajes familiares, las noches de tejo y la mesa de quienes cocinan con lo que trae la red del día.
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-5 pb-20 auto-rows-[minmax(180px,auto)]">

            {{-- ========== Card 1 — Featured news (span 4 cols × 2 rows) ========== --}}
            @if($featuredNews)
                <a href="#"
                   class="group relative bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                          transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                          hover:-translate-y-1 hover:shadow-lift
                          col-span-1 md:col-span-4 md:row-span-2 lg:col-span-4 lg:row-span-2">
                    <div class="flex-1 bg-cover bg-center relative min-h-[360px]"
                         style="background-image: url('https://images.unsplash.com/photo-1506710507565-203b9f24669b?w=1400&q=85');">
                        <div class="absolute inset-0"
                             style="background: linear-gradient(180deg, transparent 40%, rgba(15,45,92,0.55));"></div>
                    </div>
                    <div class="absolute left-0 right-0 bottom-0 px-9 py-8 z-[2] text-foam">
                        <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun">Novedades · Destacada</div>
                        <h3 class="font-display font-medium mt-2 leading-none text-foam"
                            style="font-size: clamp(32px, 3.2vw, 46px); letter-spacing: -0.02em;">
                            {{ $featuredNews->title }}
                        </h3>
                        <p class="mt-3 text-[15px] max-w-[52ch] text-[rgba(250,243,227,0.85)]">
                            {{ Str::limit(strip_tags($featuredNews->body ?? ''), 180) }}
                        </p>
                        <div class="mt-3.5 flex justify-between font-mono text-[11px] text-[rgba(250,243,227,0.65)]">
                            <span>{{ $featuredNews->published_at?->locale('es')->isoFormat('D MMMM') }}</span>
                            <span>Lectura · 4 min</span>
                        </div>
                    </div>
                </a>
            @else
                {{-- Empty state: Featured news placeholder --}}
                <div class="bg-sand-2 border border-ink-line rounded-md flex flex-col items-center justify-center min-h-[360px] px-8 text-center
                            col-span-1 md:col-span-4 md:row-span-2 lg:col-span-4 lg:row-span-2">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral mb-3">Novedades</div>
                    <h3 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Pronto más novedades
                    </h3>
                    <p class="mt-2 text-ink-soft max-w-[40ch]">
                        Estamos preparando la próxima crónica del balneario.
                    </p>
                </div>
            @endif

            {{-- ========== Card 2 — Próximos eventos (span 2 cols × 2 rows) ========== --}}
            <a href="{{ route('eventos.index') }}"
               class="group bg-ink text-sand p-7 flex flex-col rounded-md border border-ink-line overflow-hidden
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-2 md:row-span-2 lg:col-span-2 lg:row-span-2">
                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun">Próximos eventos</div>
                <h3 class="font-display font-medium mt-2 leading-none text-sand text-[22px]"
                    style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                    Agenda
                </h3>

                @if($upcomingEvents->isEmpty())
                    <div class="mt-6 text-[rgba(250,243,227,0.65)] text-sm">Agenda en construcción</div>
                @else
                    <div class="mt-6 flex flex-col gap-[18px] flex-1">
                        @foreach($upcomingEvents as $event)
                            <div class="grid grid-cols-[64px_1fr] gap-4 pb-[18px]
                                        @if(! $loop->last) border-b border-[rgba(250,243,227,0.14)] @endif">
                                <x-public.event-stamp :date="$event->starts_at" />
                                <div>
                                    <div class="font-display text-[17px] font-medium leading-tight text-sand"
                                         style="font-variation-settings: 'opsz' 24, 'SOFT' 30;">
                                        {{ $event->title }}
                                    </div>
                                    <div class="font-mono text-[10px] tracking-[0.1em] text-[rgba(250,243,227,0.6)] mt-1">
                                        {{ $event->location }}
                                        @if($event->starts_at && ! $event->all_day)
                                            · {{ $event->starts_at->format('H:i') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </a>

            {{-- ========== Card 3 — Galería (span 2 cols × 2 rows) ========== --}}
            <a href="{{ route('galeria.index') }}"
               class="group relative bg-sand-2 rounded-md overflow-hidden border border-ink-line
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-2 md:row-span-2 lg:col-span-2 lg:row-span-2">
                @if($latestImages->isEmpty())
                    <div class="grid grid-cols-2 grid-rows-2 h-full gap-[3px] min-h-[300px]">
                        <div class="bg-sand-3"></div>
                        <div class="bg-sand-3"></div>
                        <div class="bg-sand-3"></div>
                        <div class="bg-sand-3"></div>
                    </div>
                @else
                    <div class="grid grid-cols-2 grid-rows-2 h-full gap-[3px] min-h-[300px]">
                        @foreach($latestImages->take(4) as $i => $img)
                            @php
                                $url = $img->path
                                    ? (Str::startsWith($img->path, ['http://','https://','/']) ? $img->path : asset('storage/' . ltrim($img->path, '/')))
                                    : ($galleryFallbacks[$i] ?? $galleryFallbacks[0]);
                            @endphp
                            <div class="bg-cover bg-center" style="background-image: url('{{ $url }}');" aria-hidden="true"></div>
                        @endforeach
                    </div>
                @endif

                <div class="absolute left-5 right-5 bottom-5 bg-sand py-3.5 px-[18px] rounded flex justify-between items-center">
                    <h3 class="font-display text-xl font-medium m-0 text-ink"
                        style="font-variation-settings: 'opsz' 48, 'SOFT' 50;">
                        Galería
                    </h3>
                    <span class="font-mono text-[11px] text-coral">{{ $stats['gallery'] }} imágenes</span>
                </div>
            </a>

            {{-- ========== Card 4 — Hospedajes (span 2 cols × 2 rows) ========== --}}
            <a href="{{ route('hospedajes.index') }}" id="donde-quedarse"
               class="group bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-2 md:row-span-2 lg:col-span-2 lg:row-span-2">
                <div class="flex-1 bg-cover bg-center min-h-[180px] relative"
                     style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=85');">
                    <div class="absolute inset-0"
                         style="background: linear-gradient(180deg, transparent 40%, rgba(15,45,92,0.55));"></div>
                </div>
                <div class="px-[22px] py-5 bg-foam">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral">Dónde quedarse</div>
                    <h3 class="font-display font-medium mt-2 leading-tight text-ink text-[22px]"
                        style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                        {{ $stats['lodgings'] }} hospedajes familiares
                    </h3>
                    <p class="mt-2.5 text-ink-soft text-sm leading-snug">
                        Hoteles, cabañas y campings frente al mar. Curados por turismo municipal.
                    </p>
                    <div class="mt-3.5 flex justify-between font-mono text-[11px] text-ink-soft">
                        <span>Desde $28.000 / noche</span>
                        <span class="text-sun-deep">Abierto todo el año</span>
                    </div>
                </div>
            </a>

            {{-- ========== Card 5 — Gourmet (span 2 cols × 2 rows) ========== --}}
            <a href="{{ route('gastronomia.index') }}" id="gastronomia"
               class="group bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-2 md:row-span-2 lg:col-span-2 lg:row-span-2">
                <div class="flex-1 bg-cover bg-center min-h-[240px] relative"
                     style="background-image: url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=85');">
                    <div class="absolute inset-0"
                         style="background: linear-gradient(180deg, transparent 40%, rgba(15,45,92,0.55));"></div>
                </div>
                <div class="px-[22px] py-5 bg-foam">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral">Gourmet · Nocturnos</div>
                    <h3 class="font-display font-medium mt-2 leading-tight text-ink text-[22px]"
                        style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                        {{ $stats['venues'] }} lugares para comer y salir
                    </h3>
                    <p class="mt-2.5 text-ink-soft text-sm leading-snug">
                        Parrillas frente al océano, pescadería de la semana, barcitos del muelle.
                    </p>
                </div>
            </a>

            {{-- ========== Card 6 — Recetas (span 2 cols × 2 rows, coral background) ========== --}}
            <a href="{{ route('recetas.index') }}"
               class="group relative bg-coral text-sand p-8 px-7 flex flex-col justify-between rounded-md overflow-hidden border border-ink-line
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-2 md:row-span-2 lg:col-span-2 lg:row-span-2">
                {{-- Decorative sun --}}
                <div aria-hidden="true"
                     class="absolute -top-10 -right-10 w-[180px] h-[180px] pointer-events-none"
                     style="background: radial-gradient(circle, rgba(216,155,42,0.35), transparent 70%);"></div>

                <div class="relative">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sand opacity-70">Recetario</div>
                    <h3 class="font-display italic mt-2 leading-[1.05] text-sand text-[34px]"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">
                        Mejillones a la parrilla <em class="not-italic">como en casa.</em>
                    </h3>
                    <p class="mt-3 max-w-[30ch] text-sm text-[rgba(250,243,227,0.82)]">
                        {{ $stats['recipes'] }} recetas con pescado fresco y mariscos de la costa. Guías de cocineras del pueblo.
                    </p>
                </div>
                <span class="relative inline-flex items-center gap-1.5 font-mono text-[11px] text-sand opacity-75">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 4h10M3 8h10M3 12h6" stroke-linecap="round"/>
                    </svg>
                    {{ $stats['recipes'] }} recetas · temporada de otoño
                </span>
            </a>

            {{-- ========== Card 7 — Alquileres (span 3 cols × 1 row) ========== --}}
            <a href="{{ route('alquileres.index') }}"
               class="group bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-4 lg:col-span-3">
                <div class="flex-1 bg-cover bg-center min-h-[180px] relative"
                     style="background-image: url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200&q=85');">
                    <div class="absolute inset-0"
                         style="background: linear-gradient(180deg, transparent 40%, rgba(15,45,92,0.55));"></div>
                </div>
                <div class="px-[22px] py-5 bg-foam">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral">Alquileres</div>
                    <h3 class="font-display font-medium mt-2 leading-tight text-ink text-[22px]"
                        style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                        {{ $stats['rentals'] }} propiedades para tu escapada
                    </h3>
                    <p class="mt-2.5 text-ink-soft text-sm leading-snug">
                        Casas, departamentos y dormis desde 2 hasta 12 plazas. Contacto directo con el dueño.
                    </p>
                    <div class="mt-3.5 flex justify-between font-mono text-[11px] text-ink-soft">
                        <span>Temporada baja abierta</span>
                        <span>Sin intermediarios</span>
                    </div>
                </div>
            </a>

            {{-- ========== Card 8 — Clasificados (span 3 cols × 1 row, sand-2 bg, two-column inner) ========== --}}
            <a href="{{ route('clasificados.index') }}"
               class="group bg-sand-2 rounded-md p-6 grid grid-cols-1 md:grid-cols-2 gap-5 border border-ink-line
                      transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
                      hover:-translate-y-1 hover:shadow-lift
                      col-span-1 md:col-span-4 lg:col-span-3">
                <div>
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-2">Clasificados</div>
                    <h3 class="font-display font-medium mt-2 leading-tight text-ink text-[22px] mb-3"
                        style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                        El pizarrón del pueblo
                    </h3>
                    <p class="text-ink-soft text-sm leading-snug">
                        Ventas, servicios, compras, paseos. Todo lo que pasa de mano en mano.
                    </p>
                </div>

                @if($latestClassifieds->isNotEmpty())
                    <ul class="flex flex-col gap-2.5">
                        @foreach($latestClassifieds->take(4) as $cl)
                            <li class="font-display text-[15px] flex justify-between gap-3 pb-2
                                       @if(! $loop->last) border-b border-dotted border-ink-line @endif"
                                style="font-variation-settings: 'opsz' 14;">
                                <span class="truncate text-ink">{{ Str::limit($cl->title, 38) }}</span>
                                <span class="font-mono text-[10px] text-coral whitespace-nowrap">
                                    {{ $cl->published_at?->format('d/m') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </a>

        </div>
    </section>

    {{-- =============== MAREAS FEATURE =============== --}}
    <section id="mareas" class="bg-ink text-sand py-32 relative overflow-hidden">
        {{-- Decorative sun --}}
        <div aria-hidden="true"
             class="absolute -left-[200px] -bottom-[200px] w-[500px] h-[500px] pointer-events-none"
             style="background: radial-gradient(circle, rgba(216,155,42,0.2), transparent 60%);"></div>

        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.2fr] gap-12 lg:gap-24 items-center">

                <div>
                    <span class="eyebrow !text-sun">Instrumentos náuticos</span>
                    <h2 class="font-display font-normal text-sand leading-[0.95] mt-4 mb-6"
                        style="font-size: clamp(42px, 5vw, 68px); letter-spacing: -0.025em; font-variation-settings: 'opsz' 144, 'SOFT' 50;">
                        La marea que <em class="not-italic font-display italic text-sun"
                                         style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">empuja</em><br>
                        el día.
                    </h2>
                    <p class="text-[rgba(250,243,227,0.72)] max-w-[48ch] text-[17px] leading-[1.65]">
                        Dos pleamares y dos bajamares cada 24 horas. La amplitud entre una y otra puede superar los tres metros: por eso el pueblo sabe leer el horario del mar antes de salir a pescar, remar o caminar la restinga.
                    </p>
                    <p class="mt-7 font-mono text-xs text-[rgba(250,243,227,0.55)] tracking-[0.08em]">
                        Publicamos predicciones a 14 días · datos actualizados desde el SHN
                    </p>
                </div>

                <x-public.tide-chart :tide="$todayTide" />
            </div>
        </div>
    </section>

    {{-- =============== INFO STRIP =============== --}}
    <section class="bg-sand-2 py-20">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8">

            <div class="flex flex-wrap justify-between items-end gap-10 pb-12">
                <h2 class="font-display font-normal leading-[0.95] text-ink"
                    style="font-size: clamp(44px, 5vw, 72px); letter-spacing: -0.025em; font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    Teléfonos<br>
                    <em class="not-italic font-display italic text-sun-deep"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">a mano.</em>
                </h2>
                <div class="max-w-[44ch] text-ink-soft text-[17px]">
                    <span class="eyebrow block mb-2.5">Información útil</span>
                    Lo importante, siempre visible. Servicios del pueblo, emergencias, turismo y traslados.
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    // Iconos default por nombre de servicio (matching del preview).
                    $defaultIcons = [
                        'shield' => '<path d="M12 2L4 7v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V7l-8-5z" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>',
                        'bolt'   => '<path d="M13 3L4 14h6l-1 7 9-11h-6l1-7z" stroke-linejoin="round"/>',
                        'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2" stroke-linecap="round"/>',
                        'pin'    => '<path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1118 0z" stroke-linejoin="round"/><circle cx="12" cy="10" r="3"/>',
                    ];

                    // Mapeo de items por defecto (mirror del preview) si no hay UsefulInfo data.
                    $fallbackInfo = collect([
                        (object)['title' => 'Policía',           'sub' => 'Destacamento local, 24 hs.',     'phone' => '911',                'icon' => 'shield'],
                        (object)['title' => 'Bomberos',          'sub' => 'Voluntarios de El Cóndor',       'phone' => '100',                'icon' => 'bolt'],
                        (object)['title' => 'Hospital',          'sub' => 'Centro de salud Viedma',         'phone' => '107',                'icon' => 'clock'],
                        (object)['title' => 'Turismo municipal', 'sub' => 'Av. Costanera s/n',              'phone' => '+54 9 2920 15 3300', 'icon' => 'pin'],
                    ]);

                    $infoItems = $infoTop->isNotEmpty()
                        ? $infoTop->map(function ($item, $i) use ($defaultIcons) {
                            $icons = array_keys($defaultIcons);
                            return (object)[
                                'title' => $item->title,
                                'sub'   => $item->address,
                                'phone' => $item->phone,
                                'icon'  => $icons[$i % count($icons)],
                            ];
                          })
                        : $fallbackInfo;
                @endphp

                @foreach($infoItems as $info)
                    <div class="bg-sand p-7 px-[26px] rounded-md border border-ink-line transition-transform duration-200
                                hover:-translate-y-[3px] hover:border-coral">
                        <div class="w-[46px] h-[46px] rounded-full bg-coral text-sand flex items-center justify-center mb-4">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-[22px] h-[22px]">
                                {!! $defaultIcons[$info->icon] ?? $defaultIcons['pin'] !!}
                            </svg>
                        </div>
                        <h4 class="font-display text-xl font-medium text-ink"
                            style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                            {{ $info->title }}
                        </h4>
                        <p class="text-ink-soft mt-1.5 text-sm">{{ $info->sub }}</p>
                        <span class="block font-mono text-[18px] text-ink mt-3 font-medium">
                            {{ $info->phone }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-public.layouts.main>
