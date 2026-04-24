<x-public.layouts.main title="Lugares cercanos">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Excursiones a un paso',
        'titleStart' => 'Cer',
        'titleEnd'   => 'canos',
        'lede'       => 'La Lobería, Bahía Creek, Viedma, el Faro Segunda Barranca. Lo que merece un día completo a pocos kilómetros del pueblo.',
    ])

    {{-- =============== GRID =============== --}}
    <section class="bg-sand">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 pt-16 pb-24">

            @if ($items->isEmpty())
                <div class="bg-foam border border-ink-line rounded-md py-24 px-8 text-center">
                    <div class="eyebrow mb-3">Sin lugares cargados</div>
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
                            route="cercanos.show"
                            titleField="title"
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
