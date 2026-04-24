<x-public.layouts.main title="Suscripción dada de baja"
                        description="Te diste de baja del newsletter del Balneario El Cóndor.">

    <section class="bg-sand">
        <div class="max-w-[680px] mx-auto px-5 lg:px-8 pt-24 pb-24 text-center">
            <div class="eyebrow mb-6">Baja confirmada</div>
            <h1 class="font-display font-normal leading-[0.95] text-ink mb-8"
                style="font-size: clamp(40px, 6vw, 80px); letter-spacing: -0.025em;
                       font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                Te diste de baja
            </h1>
            <p class="text-ink-soft mb-10" style="font-size: 19px; line-height: 1.7;">
                No vas a recibir más correos del newsletter en
                <strong class="text-ink">{{ $sub->email }}</strong>.
                Si fue un error, podés volver a suscribirte cuando quieras.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('newsletter.form') }}" class="btn-primary">
                    Volver a suscribirme
                </a>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                    Ir al inicio
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>

        <x-public.wave-divider />
    </section>

</x-public.layouts.main>
