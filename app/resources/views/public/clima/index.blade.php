@php
    use Illuminate\Support\Carbon;

    // Iconos SVG simplificados por código WMO de Open-Meteo.
    $iconFor = function (int $code): string {
        // Sol
        $sun  = '<circle cx="12" cy="12" r="4.5" fill="currentColor"/><g stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/><line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/><line x1="4.5" y1="4.5" x2="5.9" y2="5.9"/><line x1="18.1" y1="18.1" x2="19.5" y2="19.5"/><line x1="4.5" y1="19.5" x2="5.9" y2="18.1"/><line x1="18.1" y1="5.9" x2="19.5" y2="4.5"/></g>';
        // Nube
        $cloud = '<path d="M7 18h10a4 4 0 100-8 5 5 0 00-9.6-1.5A3.5 3.5 0 007 18z" fill="currentColor" fill-opacity="0.85"/>';
        // Lluvia
        $rain  = '<path d="M7 14h10a4 4 0 100-8 5 5 0 00-9.6-1.5A3.5 3.5 0 007 14z" fill="currentColor" fill-opacity="0.85"/><g stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><line x1="9"  y1="17" x2="8" y2="20"/><line x1="13" y1="17" x2="12" y2="20"/><line x1="17" y1="17" x2="16" y2="20"/></g>';
        // Niebla
        $fog   = '<path d="M7 14h10a4 4 0 100-8 5 5 0 00-9.6-1.5A3.5 3.5 0 007 14z" fill="currentColor" fill-opacity="0.7"/><g stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><line x1="4" y1="18" x2="20" y2="18"/><line x1="6" y1="21" x2="18" y2="21"/></g>';
        // Nieve
        $snow  = '<path d="M7 14h10a4 4 0 100-8 5 5 0 00-9.6-1.5A3.5 3.5 0 007 14z" fill="currentColor" fill-opacity="0.85"/><g stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><line x1="9"  y1="17" x2="9"  y2="20"/><line x1="13" y1="17" x2="13" y2="20"/><line x1="17" y1="17" x2="17" y2="20"/></g>';

        return match (true) {
            $code === 0       => $sun,
            $code <= 3        => $cloud,
            $code <= 48       => $fog,
            $code <= 67       => $rain,
            $code <= 77       => $snow,
            $code <= 82       => $rain,
            $code <= 86       => $snow,
            default           => $rain,
        };
    };

    $updatedAt = $weather && ! empty($weather['updated_at'])
        ? Carbon::parse($weather['updated_at'])->locale('es')->isoFormat('D MMM, HH:mm')
        : null;
@endphp

<x-public.layouts.main title="Clima en El Cóndor">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Pronóstico costero',
        'titleStart' => 'Clima',
        'titleEnd'   => null,
        'lede'       => 'La temperatura, el viento y la humedad de El Cóndor en tiempo casi real. Pronóstico a cuatro días de Open-Meteo, alimentado automáticamente cada media hora.',
    ])

    <section class="bg-sand">
        <div class="max-w-[1100px] mx-auto px-5 lg:px-8 pb-24">

            @if(! $weather)
                {{-- Empty / fallback --}}
                <div class="bg-foam border border-ink-line rounded-md py-20 px-8 text-center">
                    <div class="eyebrow mb-3 text-coral">Sin datos</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Servicio de clima temporalmente no disponible
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[48ch] mx-auto">
                        Estamos teniendo problemas para conectar con Open-Meteo. Probá más tarde.
                    </p>
                </div>
            @else
                {{-- =============== CARD CURRENT =============== --}}
                <article class="bg-foam border border-ink-line rounded-md p-8 lg:p-12 grid grid-cols-1 lg:grid-cols-[1fr_1fr] gap-10 items-center shadow-card">
                    <div>
                        <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral">Ahora · El Cóndor</div>
                        <div class="flex items-baseline gap-3 mt-3">
                            <span class="font-display text-[clamp(80px,12vw,140px)] leading-[0.85] text-sun-deep"
                                  style="font-variation-settings: 'opsz' 144, 'SOFT' 30;">
                                {{ $weather['temp'] }}°
                            </span>
                            <span class="font-mono text-sm text-ink-soft">C</span>
                        </div>
                        <p class="font-display italic text-2xl text-ink mt-2"
                           style="font-variation-settings: 'opsz' 48, 'SOFT' 100;">
                            {{ $weather['description'] }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-5 lg:gap-7">
                        <div class="bg-sand border border-ink-line rounded p-5">
                            <span class="font-mono text-[10px] tracking-[0.15em] uppercase text-ink-soft">Humedad</span>
                            <div class="font-display text-[36px] text-ink mt-2"
                                 style="font-variation-settings: 'opsz' 48;">
                                {{ $weather['humidity'] }}%
                            </div>
                        </div>
                        <div class="bg-sand border border-ink-line rounded p-5">
                            <span class="font-mono text-[10px] tracking-[0.15em] uppercase text-ink-soft">Viento</span>
                            <div class="font-display text-[36px] text-ink mt-2"
                                 style="font-variation-settings: 'opsz' 48;">
                                {{ $weather['wind'] }}
                                <span class="font-mono text-sm text-ink-soft">km/h</span>
                            </div>
                        </div>
                        <div class="bg-sand border border-ink-line rounded p-5 col-span-2">
                            <span class="font-mono text-[10px] tracking-[0.15em] uppercase text-ink-soft">Dirección</span>
                            <div class="font-display text-[28px] text-ink mt-2"
                                 style="font-variation-settings: 'opsz' 48;">
                                {{ $weather['wind_label'] }}
                            </div>
                        </div>
                    </div>
                </article>

                {{-- =============== FORECAST 4 DÍAS =============== --}}
                <h3 class="mt-16 mb-6 font-display text-3xl text-ink"
                    style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    Próximos días
                </h3>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($weather['forecast'] ?? [] as $i => $day)
                        @php
                            $dayDate  = Carbon::parse($day['date']);
                            $dayLabel = $i === 0
                                ? 'Hoy'
                                : ($i === 1 ? 'Mañana' : $dayDate->locale('es')->isoFormat('ddd D'));
                        @endphp
                        <div class="bg-foam border border-ink-line rounded-md p-6 transition-transform duration-200 hover:-translate-y-0.5 hover:shadow-lift">
                            <div class="font-mono text-[10px] tracking-[0.18em] uppercase text-coral">
                                {{ $dayLabel }}
                            </div>
                            <div class="mt-3 text-sun-deep w-12 h-12">
                                <svg viewBox="0 0 24 24" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="1.6">
                                    {!! $iconFor((int) $day['code']) !!}
                                </svg>
                            </div>
                            <div class="mt-4 flex items-baseline gap-2">
                                <span class="font-display text-[36px] text-ink leading-none"
                                      style="font-variation-settings: 'opsz' 48, 'SOFT' 30;">
                                    {{ $day['max'] }}°
                                </span>
                                <span class="font-mono text-sm text-ink-soft">/ {{ $day['min'] }}°</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($updatedAt)
                    <p class="mt-12 font-mono text-[11px] tracking-[0.08em] text-ink-soft text-center">
                        Actualizado {{ $updatedAt }} · fuente
                        <a href="https://open-meteo.com" target="_blank" rel="noopener"
                           class="text-coral underline hover:no-underline">Open-Meteo</a>
                    </p>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
