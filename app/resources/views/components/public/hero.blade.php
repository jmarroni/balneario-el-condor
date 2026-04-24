@props(['tide' => null])

<section class="hero relative pt-20 pb-32 overflow-hidden">
    {{-- Sun glow background --}}
    <div aria-hidden="true"
         class="absolute -top-32 -right-48 w-[640px] h-[640px] pointer-events-none
                bg-[radial-gradient(circle,_rgba(216,155,42,0.22)_0%,_transparent_55%)]"></div>

    <div class="max-w-[1360px] mx-auto px-5 lg:px-8">
        <div class="grid lg:grid-cols-[1fr_1.15fr] gap-20 items-center min-h-[680px]">

            {{-- Hero text --}}
            <div class="hero-text">
                <div class="flex items-center gap-3.5 mb-7 animate-rise" style="animation-delay: 0.1s;">
                    <span class="w-12 h-0.5 bg-coral"></span>
                    <span class="eyebrow">Balneario · Desde 1921</span>
                </div>

                <h1 class="display-xl text-[clamp(52px,7.5vw,108px)] animate-rise"
                    style="animation-delay: 0.2s; line-height: 0.9;">
                    El faro,<br>
                    el cóndor<br>
                    <em class="display-italic inline-block" style="transform: translateX(-8px);">y el mar.</em>
                </h1>

                <p class="mt-9 text-[19px] leading-relaxed text-ink-soft max-w-[48ch] animate-rise"
                   style="animation-delay: 0.4s;">
                    Donde la costa atlántica se vuelve patagónica. A 30 km de Viedma, entre la desembocadura del río Negro y el océano, el pueblo más austral de la provincia.
                </p>

                <div class="mt-11 flex gap-4 flex-wrap animate-rise" style="animation-delay: 0.55s;">
                    <a href="{{ route('hospedajes.index') }}" class="btn-primary">
                        Planificar la visita
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="{{ route('galeria.index') }}" class="btn-ghost">Ver la galería</a>
                </div>
            </div>

            {{-- Hero photo collage --}}
            <div class="relative h-[620px] hidden lg:block">
                {{-- Main photo (rotated -1.2deg, polaroid style) --}}
                <div class="absolute inset-0 rounded-md shadow-lift bg-cover bg-[center_30%] animate-rise overflow-hidden"
                     style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1400&q=85');
                            transform: rotate(-1.2deg);
                            animation-delay: 0.3s;"
                     aria-hidden="true">
                    <div class="absolute inset-0 rounded-md"
                         style="background: linear-gradient(180deg, rgba(15,45,92,0) 40%, rgba(15,45,92,0.3) 100%);"></div>
                </div>

                {{-- Side photo (small, rotated +4deg, white border) --}}
                <div class="absolute top-10 -right-10 w-[200px] h-[260px] bg-cover bg-center rounded
                            shadow-card border-[6px] border-foam animate-rise"
                     style="background-image: url('https://images.unsplash.com/photo-1507652955-f3dcef5a3be5?w=600&q=85');
                            transform: rotate(4deg);
                            animation-delay: 0.85s;"
                     aria-hidden="true"></div>

                {{-- Tide widget overlay --}}
                <x-public.tide-card
                    :tide="$tide"
                    class="!absolute -left-16 -bottom-8 w-[340px] animate-rise"
                    style="animation-delay: 0.7s;" />
            </div>
        </div>
    </div>
</section>
