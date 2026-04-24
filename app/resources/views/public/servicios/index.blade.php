<x-public.layouts.main title="Servicios">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Oficios y soluciones',
        'titleStart' => 'Servi',
        'titleEnd'   => 'cios',
        'lede'       => 'Plomeros, electricistas, gasistas, jardineros, albañiles. Los oficios del pueblo, contactables en un par de toques.',
    ])

    {{-- =============== GRID =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-24">

            @if ($items->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin servicios cargados</div>
                    <h2 class="font-display text-3xl text-ink"
                        style="font-variation-settings: 'opsz' 144, 'SOFT' 40;">
                        Próximamente
                    </h2>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $item)
                        <x-public.directory-card
                            :item="$item"
                            route="servicios.show"
                            titleField="name"
                            :description="$item->description"
                            :meta="$item->phone" />
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
