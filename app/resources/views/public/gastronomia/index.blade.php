@php
    $catLabels = [
        'gourmet'   => 'Gourmet',
        'nightlife' => 'Nocturnos',
    ];
@endphp
<x-public.layouts.main title="Gastronomía">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Comer y salir',
        'titleStart' => 'Gastro',
        'titleEnd'   => 'nomía',
        'lede'       => 'De la mesa de un parador frente al mar a la barra de un bar después de la puesta de sol. Lo que se cocina y lo que late de noche en El Cóndor.',
    ])

    {{-- =============== CATEGORY TABS =============== --}}
    <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4">
            <div class="flex gap-2.5 items-center">
                @foreach (['gourmet', 'nightlife'] as $cat)
                    <a href="{{ route('gastronomia.index', ['categoria' => $cat]) }}"
                       @class([
                           'inline-flex items-center font-mono text-[11px] tracking-[0.18em] uppercase px-5 py-2.5 rounded-full border transition-colors',
                           'bg-coral text-sand border-coral'        => $current === $cat,
                           'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current !== $cat,
                       ])>
                        {{ $catLabels[$cat] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =============== GRID =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-12 pb-24">

            @if ($items->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin resultados</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Estamos completando esta guía
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[44ch] mx-auto">
                        Pronto vas a encontrar acá los mejores lugares para
                        @if ($current === 'gourmet') comer @else salir de noche @endif.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $item)
                        <x-public.directory-card
                            :item="$item"
                            route="gastronomia.show"
                            titleField="name"
                            :eyebrow="$catLabels[$item->category] ?? null"
                            :description="$item->description"
                            :meta="$item->address" />
                    @endforeach
                </div>

                @if ($items->hasPages())
                    <div class="mt-16 pt-8 border-t border-ink-line">
                        {{ $items->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

</x-public.layouts.main>
