<x-public.layouts.main title="Alquileres">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Casas y departamentos',
        'titleStart' => 'Alqui',
        'titleEnd'   => 'leres',
        'lede'       => 'Casas para grupos, departamentos para parejas, espacios para una semana o un mes entero. Contactá directo a quien lo alquila.',
    ])

    {{-- =============== GRID =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-24">

            @if ($items->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin alquileres por ahora</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Pronto vamos a sumar publicaciones
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[44ch] mx-auto">
                        Si tenés un alquiler para sumar al directorio, escribinos.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $item)
                        <x-public.directory-card
                            :item="$item"
                            route="alquileres.show"
                            titleField="title"
                            :eyebrow="'Capacidad · ' . $item->places . ' ' . ($item->places == 1 ? 'persona' : 'personas')"
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
