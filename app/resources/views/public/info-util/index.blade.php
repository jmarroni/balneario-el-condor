<x-public.layouts.main title="Información útil">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Teléfonos del pueblo',
        'titleStart' => 'Info',
        'titleEnd'   => ' útil',
        'lede'       => 'Emergencias, instituciones, servicios públicos. Los números que conviene tener anotados antes de que haga falta.',
    ])

    {{-- =============== DIRECTORIO TELEFÓNICO =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-24">

            @if ($items->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin información cargada</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Próximamente
                    </h2>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($items as $info)
                        <article class="bg-foam border border-ink-line rounded-md p-7 flex flex-col gap-4
                                        transition-[transform,box-shadow] duration-300
                                        hover:-translate-y-0.5 hover:shadow-lift">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-coral text-sand flex items-center justify-center shadow-card">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.37 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.57 2.81.7A2 2 0 0122 16.92z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-display text-[22px] text-ink leading-[1.15] mb-1"
                                        style="font-variation-settings: 'opsz' 48, 'SOFT' 40; letter-spacing: -0.015em;">
                                        {{ $info->title }}
                                    </h3>
                                    @if ($info->phone)
                                        <a href="tel:{{ preg_replace('/\s+/', '', $info->phone) }}"
                                           class="font-mono text-[22px] text-ink hover:text-coral transition-colors block break-all">
                                            {{ $info->phone }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if ($info->address || $info->email || $info->website)
                                <div class="border-t border-ink-line pt-4 space-y-2 font-mono text-[12px] text-ink-soft">
                                    @if ($info->address)
                                        <div class="flex items-start gap-2">
                                            <svg class="shrink-0 mt-0.5 text-ink-line" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M8 1.5C5 1.5 3 3.5 3 6.3 3 9.5 8 14.5 8 14.5s5-5 5-8.2C13 3.5 11 1.5 8 1.5z"/>
                                                <circle cx="8" cy="6.2" r="1.6"/>
                                            </svg>
                                            <span>{{ $info->address }}</span>
                                        </div>
                                    @endif
                                    @if ($info->email)
                                        <a href="mailto:{{ $info->email }}"
                                           class="flex items-start gap-2 hover:text-coral transition-colors">
                                            <svg class="shrink-0 mt-0.5 text-ink-line" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="2" y="3" width="12" height="10" rx="1.5"/>
                                                <path d="M2.5 4l5.5 4.5L13.5 4"/>
                                            </svg>
                                            <span class="break-all">{{ $info->email }}</span>
                                        </a>
                                    @endif
                                    @if ($info->website)
                                        <a href="{{ $info->website }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="flex items-start gap-2 hover:text-coral transition-colors">
                                            <svg class="shrink-0 mt-0.5 text-ink-line" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <circle cx="8" cy="8" r="6"/>
                                                <path d="M2 8h12M8 2c2 2 2 10 0 12M8 2c-2 2-2 10 0 12"/>
                                            </svg>
                                            <span class="break-all">{{ $info->website }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

</x-public.layouts.main>
