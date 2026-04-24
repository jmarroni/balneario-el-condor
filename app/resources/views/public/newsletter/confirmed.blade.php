<x-public.layouts.main title="Suscripción confirmada"
                        description="Tu suscripción al newsletter del Balneario El Cóndor está confirmada.">

    <section class="bg-sand">
        <div class="max-w-[680px] mx-auto px-5 lg:px-8 pt-24 pb-24 text-center">
            <div class="eyebrow mb-6">Listo</div>
            <h1 class="font-display font-normal leading-[0.95] text-ink mb-8"
                style="font-size: clamp(40px, 6vw, 80px); letter-spacing: -0.025em;
                       font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                Suscripción confirmada
            </h1>
            <p class="text-ink-soft mb-10" style="font-size: 19px; line-height: 1.7;">
                Tu email <strong class="text-ink">{{ $sub->email }}</strong> quedó registrado.
                Vas a recibir las novedades del balneario en tu casilla. ¡Gracias!
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}" class="btn-primary">
                    Volver al inicio
                </a>
                <a href="{{ route('eventos.index') }}"
                   class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                    Ver agenda
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>

        <x-public.wave-divider />
    </section>

</x-public.layouts.main>
