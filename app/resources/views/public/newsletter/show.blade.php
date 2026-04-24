<x-public.layouts.main title="Newsletter"
                        description="Suscribite al newsletter del Balneario El Cóndor para recibir agenda, mareas y novedades.">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink">Newsletter</li>
            </ol>
        </div>
    </nav>

    <section class="bg-sand">
        <div class="max-w-[760px] mx-auto px-5 lg:px-8 pt-20 pb-24 text-center">
            <div class="eyebrow mb-6">Suscribite</div>
            <h1 class="font-display font-normal leading-[0.95] text-ink mb-8"
                style="font-size: clamp(48px, 7vw, 96px); letter-spacing: -0.025em;
                       font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                Newsletter del balneario
            </h1>
            <p class="text-ink-soft mb-10" style="font-size: 19px; line-height: 1.6;">
                Recibí en tu mail la agenda de eventos, las tablas de mareas
                de la semana y las novedades del balneario. Sin spam, baja en
                un click cuando quieras.
            </p>

            @if (session('success'))
                <div class="mb-8 mx-auto max-w-md bg-foam border border-sun-deep rounded p-4 text-left">
                    <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun-deep mb-1">Casi listo</div>
                    <p class="text-ink text-[15px]">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('newsletter.subscribe') }}"
                  class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                @csrf

                <input type="text"
                       name="captcha_honeypot"
                       class="hidden"
                       tabindex="-1"
                       autocomplete="off"
                       aria-hidden="true">

                <div class="flex-1">
                    <label for="email" class="sr-only">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="tu@email.com"
                           required
                           class="w-full bg-foam border border-ink-line rounded px-4 py-3 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('email') border-coral @enderror">
                    @error('email')
                        <p class="mt-2 font-mono text-[11px] text-coral text-left">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary justify-center whitespace-nowrap">
                    Suscribirme
                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <p class="mt-8 font-mono text-[11px] tracking-[0.14em] uppercase text-ink-soft">
                Doble confirmación · Te llega un mail para validar
            </p>
        </div>

        <x-public.wave-divider />
    </section>

</x-public.layouts.main>
