<x-public.layouts.main title="Publicitá en el sitio"
                        description="Publicitá tu emprendimiento, comercio o evento en el sitio del Balneario El Cóndor.">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink">Publicite</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HERO =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-12">
            <div class="max-w-[68ch]">
                <div class="eyebrow mb-6">Publicidad</div>
                <h1 class="font-display font-normal leading-[0.95] text-ink mb-6"
                    style="font-size: clamp(48px, 7vw, 104px); letter-spacing: -0.025em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    Publicitate en el sitio del balneario
                </h1>
                <p class="text-ink-soft" style="font-size: 19px; line-height: 1.6;">
                    Llegá a miles de visitantes que planifican sus vacaciones
                    en El Cóndor. Espacios destacados en home, sidebar de
                    eventos y footer del sitio.
                </p>
            </div>
        </div>

        {{-- =============== GRID FORM + INFO =============== --}}
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

                {{-- ===== FORM ===== --}}
                <div class="lg:col-span-3">
                    <div class="bg-foam border border-ink-line rounded-md p-7 lg:p-10 shadow-card">
                        <span class="eyebrow block mb-6">Solicitá información</span>

                        @if (session('success'))
                            <div class="mb-6 bg-sand-2 border border-sun-deep rounded p-4">
                                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun-deep mb-1">Consulta recibida</div>
                                <p class="text-ink text-[15px]">{{ session('success') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('publicite.store') }}" class="flex flex-col gap-5">
                            @csrf

                            <input type="text"
                                   name="captcha_honeypot"
                                   class="hidden"
                                   tabindex="-1"
                                   autocomplete="off"
                                   aria-hidden="true">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Nombre</label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('name') border-coral @enderror">
                                    @error('name')
                                        <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">
                                        Apellido <span class="opacity-50 normal-case tracking-normal">(opcional)</span>
                                    </label>
                                    <input type="text"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ old('last_name') }}"
                                           class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('last_name') border-coral @enderror">
                                    @error('last_name')
                                        <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="email" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Email</label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('email') border-coral @enderror">
                                    @error('email')
                                        <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="zone" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">
                                        Zona de interés <span class="opacity-50 normal-case tracking-normal">(opcional)</span>
                                    </label>
                                    <select id="zone"
                                            name="zone"
                                            class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('zone') border-coral @enderror">
                                        <option value="">Sin preferencia</option>
                                        <option value="home-top" @selected(old('zone') === 'home-top')>Home · destacado superior</option>
                                        <option value="sidebar" @selected(old('zone') === 'sidebar')>Sidebar · columna lateral</option>
                                        <option value="events-page" @selected(old('zone') === 'events-page')>Página de eventos</option>
                                        <option value="footer" @selected(old('zone') === 'footer')>Footer · pie del sitio</option>
                                        <option value="other" @selected(old('zone') === 'other')>Otro / a definir</option>
                                    </select>
                                    @error('zone')
                                        <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">Mensaje</label>
                                <textarea id="message"
                                          name="message"
                                          rows="6"
                                          required
                                          placeholder="Contanos sobre tu negocio o emprendimiento, qué querés publicitar y por cuánto tiempo."
                                          class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('message') border-coral @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn-primary justify-center mt-3 self-start">
                                Enviar consulta
                                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ===== INFO LATERAL ===== --}}
                <aside class="lg:col-span-2 flex flex-col gap-8">
                    <div class="bg-sand-2 border border-ink-line rounded-md p-7">
                        <span class="eyebrow block mb-5">¿Por qué publicitar?</span>
                        <ul class="flex flex-col gap-4 text-[15px] text-ink leading-[1.7]">
                            <li class="flex gap-3">
                                <span class="text-coral font-mono text-[12px] mt-1">01</span>
                                <span>Llegada directa a turistas que planifican su visita.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-coral font-mono text-[12px] mt-1">02</span>
                                <span>Audiencia local activa durante toda la temporada.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-coral font-mono text-[12px] mt-1">03</span>
                                <span>Espacios curados, no saturados de banners.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-foam border border-ink-line rounded-md p-7">
                        <span class="eyebrow block mb-5">Contacto directo</span>
                        <p class="text-ink-soft text-[15px] leading-[1.7] mb-3">
                            Si preferís hablar antes, escribinos por mail.
                        </p>
                        <a href="mailto:publicidad@elcondor.gob.ar"
                           class="text-coral hover:text-ink transition-colors text-[15px]">
                            publicidad@elcondor.gob.ar
                        </a>
                    </div>
                </aside>

            </div>
        </div>
    </section>

</x-public.layouts.main>
