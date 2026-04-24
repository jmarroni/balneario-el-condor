@props(['date'])

@php
    $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
    $day   = $carbon->format('d');
    $month = $carbon->locale('es')->isoFormat('MMM');
@endphp

<div {{ $attributes->merge(['class' => 'bg-sun text-ink text-center px-1 py-1.5 rounded-[3px] font-mono leading-none h-fit']) }}>
    <span class="block text-[24px] font-medium">{{ $day }}</span>
    <span class="block text-[9px] tracking-[0.15em] uppercase mt-1">{{ $month }}</span>
</div>
