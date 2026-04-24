@props(['tide' => null])

@php
    $hasTide = ! is_null($tide);
    $now      = \Carbon\Carbon::now();
    $nowX     = ($now->hour * 60 + $now->minute) / 1440 * 600;
    $nowLabel = $now->locale('es')->isoFormat('dddd D [de] MMMM');

    $readings = [
        ['1.ª Pleamar', $hasTide ? $tide->first_high  : null, $hasTide ? $tide->first_high_height  : null],
        ['1.ª Bajamar', $hasTide ? $tide->first_low   : null, $hasTide ? $tide->first_low_height   : null],
        ['2.ª Pleamar', $hasTide ? $tide->second_high : null, $hasTide ? $tide->second_high_height : null],
        ['2.ª Bajamar', $hasTide ? $tide->second_low  : null, $hasTide ? $tide->second_low_height  : null],
    ];
@endphp

<div class="bg-[rgba(250,243,227,0.04)] border border-[rgba(250,243,227,0.15)] rounded-md p-9">
    <div class="flex justify-between items-baseline pb-[18px] border-b border-[rgba(250,243,227,0.15)] mb-6">
        <div>
            <div class="font-display text-[28px] capitalize"
                 style="font-variation-settings: 'opsz' 48, 'SOFT' 40;">
                {{ $nowLabel }}
            </div>
            <div class="font-mono text-[11px] text-[rgba(250,243,227,0.55)] mt-1">—41.05 S, —62.82 O</div>
        </div>
        <div class="font-mono text-[11px] text-[rgba(250,243,227,0.55)]">SHN.gob.ar</div>
    </div>

    <div class="h-[180px] relative">
        <svg viewBox="0 0 600 180" preserveAspectRatio="none" class="w-full h-full">
            {{-- Grid line --}}
            <line x1="0" y1="90" x2="600" y2="90" stroke="#faf3e3" stroke-opacity="0.12" stroke-dasharray="3 5"/>

            {{-- Axis labels --}}
            <g font-family="JetBrains Mono" font-size="9" fill="#faf3e3" fill-opacity="0.4">
                <text x="8" y="25">+4m</text>
                <text x="8" y="175">0m</text>
                <text x="100" y="172" text-anchor="middle">06:00</text>
                <text x="225" y="172" text-anchor="middle">12:00</text>
                <text x="375" y="172" text-anchor="middle">18:00</text>
                <text x="525" y="172" text-anchor="middle">00:00</text>
            </g>

            {{-- Area under curve --}}
            <path d="M0,90 C50,20 100,20 150,90 C200,160 250,160 300,90 C350,20 400,20 450,90 C500,160 550,160 600,90 L600,160 L0,160 Z"
                  fill="#d89b2a" fill-opacity="0.12"/>

            {{-- Curve --}}
            <path d="M0,90 C50,20 100,20 150,90 C200,160 250,160 300,90 C350,20 400,20 450,90 C500,160 550,160 600,90"
                  fill="none" stroke="#d89b2a" stroke-width="2" stroke-linecap="round"/>

            {{-- Peaks (sun) and troughs (coral) --}}
            <circle cx="75"  cy="28"  r="4" fill="#d89b2a"/>
            <circle cx="375" cy="28"  r="4" fill="#d89b2a"/>
            <circle cx="225" cy="152" r="4" fill="#c85a3c"/>
            <circle cx="525" cy="152" r="4" fill="#c85a3c"/>

            {{-- "AHORA" indicator (calculated server-side) --}}
            <line x1="{{ $nowX }}" y1="10" x2="{{ $nowX }}" y2="170"
                  stroke="#faf3e3" stroke-opacity="0.4" stroke-dasharray="2 4"/>
            <text x="{{ $nowX + 2 }}" y="20"
                  font-family="JetBrains Mono" font-size="9"
                  fill="#faf3e3" fill-opacity="0.7">AHORA</text>
        </svg>
    </div>

    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($readings as [$kind, $time, $height])
            <div>
                <div class="font-mono text-[10px] tracking-[0.15em] uppercase text-[rgba(250,243,227,0.55)]">{{ $kind }}</div>
                <div class="font-display text-2xl mt-1" style="font-variation-settings: 'opsz' 48;">
                    {{ $time ? substr((string) $time, 0, 5) : '—' }}
                </div>
                <div class="font-mono text-[11px] text-sun mt-0.5">
                    {{ $height ? '+' . $height : '' }}
                </div>
            </div>
        @endforeach
    </div>
</div>
