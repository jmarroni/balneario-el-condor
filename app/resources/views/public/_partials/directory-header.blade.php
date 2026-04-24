@props([
    'eyebrow',
    'titleStart',     // primer trozo del título
    'titleEnd' => null, // segundo trozo (en italic, color sun-deep)
    'lede',
])
<section class="bg-sand">
    <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-20 pb-14">
        <div class="flex flex-wrap items-end justify-between gap-10">
            <div class="max-w-[20ch]">
                <span class="eyebrow block mb-4">{{ $eyebrow }}</span>
                <h1 class="font-display font-normal leading-[0.92] text-ink"
                    style="font-size: clamp(56px, 8.4vw, 128px); letter-spacing: -0.035em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    {{ $titleStart }}@if ($titleEnd)<em class="not-italic font-display italic text-sun-deep"
                                style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">{{ $titleEnd }}</em>@endif
                </h1>
            </div>
            <div class="max-w-[44ch] text-ink-soft text-[19px] leading-[1.55]">
                {{ $lede }}
            </div>
        </div>
    </div>
</section>
