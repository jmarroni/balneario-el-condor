<x-public.layouts.main title="Contacto"
                        description="Contactanos por consultas sobre el balneario, alojamiento, eventos o turismo en El Cóndor.">

    {{-- =============== BREADCRUMB =============== --}}
    <nav aria-label="Breadcrumb" class="bg-sand border-b border-ink-line">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[11px] tracking-[0.12em] uppercase text-ink-soft">
                <li><a href="{{ route('home') }}" class="hover:text-coral transition-colors">Inicio</a></li>
                <li aria-hidden="true" class="text-ink-line">·</li>
                <li class="text-ink">Contacto</li>
            </ol>
        </div>
    </nav>

    {{-- =============== HEADER EDITORIAL =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-12">
            <div class="max-w-[68ch]">
                <div class="eyebrow mb-6">Escribinos</div>
                <h1 class="font-display font-normal leading-[0.95] text-ink mb-6"
                    style="font-size: clamp(48px, 7vw, 104px); letter-spacing: -0.025em;
                           font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                    Contactanos
                </h1>
                <p class="text-ink-soft" style="font-size: 19px; line-height: 1.6;">
                    Estamos para responder consultas sobre el balneario, hospedajes,
                    eventos o cualquier cosa que necesites saber antes de tu visita.
                </p>
            </div>
        </div>

        {{-- =============== GRID 2 COLS: FORM + INFO =============== --}}
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

                {{-- ===== FORM ===== --}}
                <div class="lg:col-span-3">
                    <div class="bg-foam border border-ink-line rounded-md p-7 lg:p-10 shadow-card">
                        <span class="eyebrow block mb-6">Formulario de contacto</span>

                        @if (session('success'))
                            <div class="mb-6 bg-sand-2 border border-sun-deep rounded p-4">
                                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun-deep mb-1">Mensaje enviado</div>
                                <p class="text-ink text-[15px]">{{ session('success') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contacto.store') }}" class="flex flex-col gap-5">
                            @csrf

                            {{-- Honeypot anti-bot: oculto pero presente en el DOM. --}}
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
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="phone" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">
                                        Teléfono <span class="opacity-50 normal-case tracking-normal">(opcional)</span>
                                    </label>
                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('phone') border-coral @enderror">
                                    @error('phone')
                                        <p class="mt-1 font-mono text-[11px] text-coral">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="subject" class="block font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1.5">
                                        Asunto <span class="opacity-50 normal-case tracking-normal">(opcional)</span>
                                    </label>
                                    <input type="text"
                                           id="subject"
                                           name="subject"
                                           value="{{ old('subject') }}"
                                           class="w-full bg-sand border border-ink-line rounded px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-coral focus:border-coral @error('subject') border-coral @enderror">
                                    @error('subject')
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
                        <span class="eyebrow block mb-5">Turismo Municipal</span>
                        <dl class="flex flex-col gap-4 text-[15px]">
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Dirección</dt>
                                <dd class="text-ink">Av. Río Negro · Balneario El Cóndor</dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Teléfono</dt>
                                <dd class="text-ink">+54 2920 49-7497</dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Email</dt>
                                <dd>
                                    <a href="mailto:turismo@elcondor.gob.ar" class="text-coral hover:text-ink transition-colors">
                                        turismo@elcondor.gob.ar
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-1">Horario</dt>
                                <dd class="text-ink">Lun a Vie · 8 a 14h</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-foam border border-ink-line rounded-md p-7">
                        <span class="eyebrow block mb-5">Cómo llegar</span>
                        <p class="text-ink-soft text-[15px] leading-[1.7]">
                            A 30 km de Viedma por Ruta Provincial Nº 1.
                            Conexiones diarias en colectivo desde la terminal de Viedma.
                        </p>
                        <a href="{{ route('home') }}#mapa"
                           class="inline-flex items-center gap-2 mt-5 font-mono text-[11px] tracking-[0.18em] uppercase text-coral hover:text-ink transition-colors">
                            Ver mapa
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </aside>

            </div>
        </div>
    </section>

</x-public.layouts.main>
