@php
    use Illuminate\Support\Carbon;

    $dateLabel = $date->locale('es')->isoFormat('dddd D [de] MMMM, YYYY');
    $isToday   = $date->isSameDay($today);

    // Si no hay tide para el día solicitado, usamos el más cercano para el chart.
    $displayTide = $tide ?? $nearestTide;

    // Días de la semana actual (ordenados lunes→domingo).
    $weekDays = [];
    $cursor = $date->copy()->startOfWeek();
    for ($i = 0; $i < 7; $i++) {
        $key = $cursor->toDateString();
        $weekDays[] = [
            'date'  => $cursor->copy(),
            'tide'  => $week->firstWhere(fn ($t) => Carbon::parse($t->date)->toDateString() === $key),
        ];
        $cursor->addDay();
    }
@endphp

<x-public.layouts.main :title="'Mareas · '.$date->locale('es')->isoFormat('D MMM YYYY')">

    {{-- =============== HEADER EDITORIAL =============== --}}
    @include('public._partials.directory-header', [
        'eyebrow'    => 'Instrumentos náuticos',
        'titleStart' => 'Mareas',
        'titleEnd'   => null,
        'lede'       => 'Dos pleamares y dos bajamares cada 24 horas. La amplitud entre una y otra puede superar los tres metros: por eso el pueblo sabe leer el horario del mar antes de salir a pescar, remar o caminar la restinga.',
    ])

    {{-- =============== NAVEGACIÓN POR FECHA =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pb-8">
            <form method="GET" action="{{ route('mareas.index') }}"
                  class="flex flex-wrap items-center justify-center gap-3 md:gap-5 bg-foam border border-ink-line rounded-md px-5 py-4">

                <a href="{{ route('mareas.index', ['fecha' => $prevDay->toDateString()]) }}"
                   class="group inline-flex items-center gap-2 px-4 py-2 rounded border border-ink-line bg-sand
                          hover:border-coral hover:text-coral transition-colors"
                   aria-label="Día anterior">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="font-mono text-[11px] tracking-[0.12em] uppercase">Anterior</span>
                </a>

                <div class="flex flex-col items-center min-w-[280px]">
                    <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft">
                        {{ $isToday ? 'Hoy' : 'Día seleccionado' }}
                    </span>
                    <span class="font-display text-[22px] capitalize text-ink mt-1"
                          style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                        {{ $dateLabel }}
                    </span>
                </div>

                <a href="{{ route('mareas.index', ['fecha' => $nextDay->toDateString()]) }}"
                   class="group inline-flex items-center gap-2 px-4 py-2 rounded border border-ink-line bg-sand
                          hover:border-coral hover:text-coral transition-colors"
                   aria-label="Día siguiente">
                    <span class="font-mono text-[11px] tracking-[0.12em] uppercase">Siguiente</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <div class="flex items-center gap-2 ml-0 md:ml-3 pl-0 md:pl-4 md:border-l md:border-ink-line">
                    <label for="fecha" class="font-mono text-[10px] tracking-[0.15em] uppercase text-ink-soft">
                        Ir a fecha
                    </label>
                    <input type="date"
                           id="fecha"
                           name="fecha"
                           value="{{ $date->toDateString() }}"
                           onchange="this.form.submit()"
                           class="font-mono text-sm bg-sand border border-ink-line rounded px-2 py-1.5
                                  focus:outline-none focus:border-coral">
                </div>

                @if(! $isToday)
                    <a href="{{ route('mareas.index') }}"
                       class="font-mono text-[11px] tracking-[0.1em] uppercase text-coral hover:underline">
                        Volver a hoy
                    </a>
                @endif
            </form>
        </div>
    </section>

    {{-- =============== CHART DEL DÍA =============== --}}
    <section class="bg-ink text-sand py-20 relative overflow-hidden">
        <div aria-hidden="true"
             class="absolute -left-[200px] -bottom-[200px] w-[500px] h-[500px] pointer-events-none"
             style="background: radial-gradient(circle, rgba(216,155,42,0.18), transparent 60%);"></div>

        <div class="max-w-[1100px] mx-auto px-5 lg:px-8 relative">
            @if($displayTide)
                @if(! $tide && $nearestTide)
                    <div class="mb-6 px-5 py-3 bg-[rgba(250,243,227,0.06)] border border-[rgba(250,243,227,0.18)] rounded-md">
                        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-sun">Aviso</span>
                        <p class="text-sand/85 text-sm mt-1">
                            No tenemos predicción para el {{ $date->locale('es')->isoFormat('D [de] MMMM') }}.
                            Mostramos la más cercana:
                            <strong class="text-sand">{{ Carbon::parse($nearestTide->date)->locale('es')->isoFormat('dddd D [de] MMMM') }}</strong>.
                        </p>
                    </div>
                @endif

                <x-public.tide-chart :tide="$displayTide" />
            @else
                <div class="bg-[rgba(250,243,227,0.04)] border border-[rgba(250,243,227,0.15)] rounded-md p-12 text-center">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral">Sin datos</div>
                    <h3 class="font-display text-3xl text-sand mt-3"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        No hay predicción cargada para esta fecha
                    </h3>
                    <p class="mt-3 text-sand/70 max-w-[48ch] mx-auto">
                        Probá con
                        <a href="{{ route('mareas.index', ['fecha' => $prevDay->toDateString()]) }}"
                           class="text-sun underline">el día anterior</a>
                        o
                        <a href="{{ route('mareas.index', ['fecha' => $nextDay->toDateString()]) }}"
                           class="text-sun underline">el siguiente</a>.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- =============== TABLA SEMANA ACTUAL =============== --}}
    <section class="bg-sand-2 py-20">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8">

            <div class="flex flex-wrap items-end justify-between gap-6 pb-10">
                <div>
                    <span class="eyebrow block mb-3">Semana del</span>
                    <h2 class="font-display font-normal leading-[0.95] text-ink"
                        style="font-size: clamp(36px, 4vw, 56px); letter-spacing: -0.025em;
                               font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        {{ $date->copy()->startOfWeek()->locale('es')->isoFormat('D MMM') }}
                        <em class="not-italic font-display italic text-sun-deep"
                            style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">
                            al {{ $date->copy()->endOfWeek()->locale('es')->isoFormat('D MMM') }}
                        </em>
                    </h2>
                </div>
                <div class="font-mono text-[11px] tracking-[0.1em] uppercase text-ink-soft">
                    Lunes a domingo
                </div>
            </div>

            <div class="overflow-x-auto bg-sand border border-ink-line rounded-md">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-ink-line">
                            <th class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft px-5 py-4">Fecha</th>
                            <th class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft px-3 py-4">1.ª Pleamar</th>
                            <th class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft px-3 py-4">1.ª Bajamar</th>
                            <th class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft px-3 py-4">2.ª Pleamar</th>
                            <th class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft px-3 py-4">2.ª Bajamar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekDays as $day)
                            @php
                                $rowDate = $day['date'];
                                $rowTide = $day['tide'];
                                $isCurrentRow = $rowDate->isSameDay($date);
                            @endphp
                            <tr class="border-b border-ink-line/60 last:border-b-0
                                       {{ $isCurrentRow ? 'bg-foam' : 'hover:bg-foam/50' }}
                                       transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('mareas.index', ['fecha' => $rowDate->toDateString()]) }}"
                                       class="block group">
                                        <span class="font-mono text-[10px] tracking-[0.15em] uppercase
                                                   {{ $isCurrentRow ? 'text-coral' : 'text-ink-soft' }}">
                                            {{ $rowDate->locale('es')->isoFormat('ddd') }}
                                        </span>
                                        <span class="block font-display text-[20px] font-medium text-ink mt-0.5
                                                     group-hover:text-coral transition-colors"
                                              style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                                            {{ $rowDate->locale('es')->isoFormat('D MMM') }}
                                        </span>
                                    </a>
                                </td>
                                @if($rowTide)
                                    @foreach([
                                        ['first_high', 'first_high_height'],
                                        ['first_low',  'first_low_height'],
                                        ['second_high','second_high_height'],
                                        ['second_low', 'second_low_height'],
                                    ] as [$timeKey, $heightKey])
                                        <td class="px-3 py-4">
                                            <span class="block font-display text-[18px] text-ink"
                                                  style="font-variation-settings: 'opsz' 48;">
                                                {{ $rowTide->{$timeKey} ? substr((string) $rowTide->{$timeKey}, 0, 5) : '—' }}
                                            </span>
                                            <span class="block font-mono text-[11px] text-sun-deep mt-0.5">
                                                {{ $rowTide->{$heightKey} ? '+ ' . $rowTide->{$heightKey} : '' }}
                                            </span>
                                        </td>
                                    @endforeach
                                @else
                                    <td colspan="4" class="px-3 py-4 text-center font-mono text-[11px] text-ink-soft italic">
                                        sin predicción cargada
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="mt-8 font-mono text-[11px] tracking-[0.08em] text-ink-soft text-center max-w-[60ch] mx-auto">
                Predicciones basadas en el patrón histórico migrado del archivo legacy.
                Para consultas oficiales: <a href="https://www.hidro.gob.ar/" target="_blank" rel="noopener"
                   class="text-coral underline hover:no-underline">SHN Argentina</a>.
            </p>
        </div>
    </section>

</x-public.layouts.main>
