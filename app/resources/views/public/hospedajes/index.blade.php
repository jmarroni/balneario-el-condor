@php
    $typeLabels = [
        'hotel'   => 'Hoteles',
        'casa'    => 'Casas',
        'camping' => 'Camping',
        'hostel'  => 'Hostels',
    ];
@endphp
<x-public.layouts.main title="Hospedajes">

    @include('public._partials.directory-header', [
        'eyebrow'    => 'Dónde dormir',
        'titleStart' => 'Hospe',
        'titleEnd'   => 'dajes',
        'lede'       => 'Cabañas frente al mar, hoteles familiares, hostels y campings al borde de la ría. Elegí cómo querés despertar el primer día.',
    ])

    {{-- =============== TYPE TABS =============== --}}
    <section class="bg-sand border-y border-ink-line sticky top-0 z-10 backdrop-blur-sm">
        <div class="max-w-[1360px] mx-auto px-5 lg:px-8 py-4 overflow-x-auto">
            <div class="flex gap-2.5 items-center min-w-max">
                <a href="{{ route('hospedajes.index') }}"
                   @class([
                       'inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                       'bg-coral text-sand border-coral'        => ! $current,
                       'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current,
                   ])>
                    Todos
                    <span class="opacity-70">{{ array_sum($counts) }}</span>
                </a>
                @foreach ($types as $t)
                    <a href="{{ route('hospedajes.index', ['tipo' => $t]) }}"
                       @class([
                           'inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.18em] uppercase px-4 py-2 rounded-full border transition-colors',
                           'bg-coral text-sand border-coral'        => $current === $t,
                           'bg-foam text-ink border-ink-line hover:border-coral hover:text-coral' => $current !== $t,
                       ])>
                        {{ $typeLabels[$t] ?? ucfirst($t) }}
                        <span class="opacity-70">{{ $counts[$t] ?? 0 }}</span>
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
                        Pronto más opciones
                    </h2>
                    <p class="mt-3 text-ink-soft max-w-[44ch] mx-auto">
                        Estamos completando el directorio de hospedajes.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $item)
                        <x-public.directory-card
                            :item="$item"
                            route="hospedajes.show"
                            titleField="name"
                            :eyebrow="$typeLabels[$item->type] ?? ucfirst($item->type)"
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
