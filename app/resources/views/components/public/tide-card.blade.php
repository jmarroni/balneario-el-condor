@props(['tide' => null])

@php
    $hasTide = ! is_null($tide);
    $rows = [
        ['1.ª Pleamar', $hasTide ? $tide->first_high  : null, $hasTide ? $tide->first_high_height  : null],
        ['1.ª Bajamar', $hasTide ? $tide->first_low   : null, $hasTide ? $tide->first_low_height   : null],
        ['2.ª Pleamar', $hasTide ? $tide->second_high : null, $hasTide ? $tide->second_high_height : null],
        ['2.ª Bajamar', $hasTide ? $tide->second_low  : null, $hasTide ? $tide->second_low_height  : null],
    ];
    $dateLabel = $hasTide && $tide->date
        ? \Carbon\Carbon::parse($tide->date)->locale('es')->isoFormat('dddd D MMMM')
        : \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D MMMM');
@endphp

<div {{ $attributes->merge(['class' => 'bg-foam rounded-md p-6 shadow-lift border border-ink-line']) }}>
    <div class="flex items-baseline justify-between gap-3 pb-3.5 border-b border-ink-line">
        <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft">Mareas · Hoy</span>
        <span class="font-mono text-[11px] text-coral capitalize">{{ $dateLabel }}</span>
    </div>

    <h3 class="font-display text-2xl font-medium mt-2.5 text-ink"
        style="font-variation-settings: 'opsz' 48, 'SOFT' 60;">
        Pleamar al atardecer
    </h3>

    <div class="mt-3.5 grid grid-cols-2 gap-3.5">
        @foreach($rows as [$kind, $time, $height])
            <div class="flex flex-col">
                <span class="font-mono text-[10px] tracking-[0.15em] uppercase text-ink-soft">{{ $kind }}</span>
                <span class="font-display text-[28px] font-medium text-ink mt-1"
                      style="font-variation-settings: 'opsz' 48;">
                    {{ $time ? substr((string) $time, 0, 5) : '—' }}
                </span>
                <span class="font-mono text-xs text-sun-deep mt-0.5">
                    {{ $height ? '+ ' . $height : '' }}
                </span>
            </div>
        @endforeach
    </div>

    {{-- SVG wave sparkline --}}
    <div class="mt-4 h-[60px]">
        <svg viewBox="0 0 300 60" preserveAspectRatio="none" class="w-full h-full">
            <path d="M0,30 Q37,0 75,30 T150,30 T225,30 T300,30"
                  fill="none" stroke="#1e40af" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <circle cx="75" cy="8" r="3" fill="#d89b2a"/>
            <circle cx="225" cy="8" r="3" fill="#d89b2a"/>
            <circle cx="150" cy="52" r="3" fill="#c85a3c"/>
        </svg>
    </div>
</div>
