@php
    $weather = cache()->get('weather:current');
    $links = [
        ['novedades.index', 'Novedades'],
        ['eventos.index', 'Eventos'],
        ['hospedajes.index', 'Hospedajes'],
        ['gastronomia.index', 'Gourmet'],
        ['mareas.index', 'Mareas'],
        ['galeria.index', 'Galería'],
    ];
@endphp
<nav x-data="{ open: false }" class="relative z-20 bg-sand border-b border-ink-line">
    <div class="max-w-[1400px] mx-auto px-5 lg:px-10 py-5 flex items-center gap-10">
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
            <img src="{{ asset('img/logo.png') }}" alt="Balneario El Cóndor"
                 class="w-[54px] h-[54px] object-contain drop-shadow-[0_1px_2px_rgba(15,45,92,0.2)]">
            <div class="flex flex-col leading-none">
                <span class="font-mono text-[10px] tracking-[0.22em] uppercase text-coral">Río Negro · Patagonia</span>
                <span class="font-display text-[22px] font-medium text-ink mt-0.5"
                      style="font-variation-settings: 'opsz' 144, 'SOFT' 30;">El Cóndor</span>
            </div>
        </a>

        <ul class="hidden lg:flex gap-7 ml-auto">
            @foreach($links as [$route, $label])
                <li><a href="{{ Route::has($route) ? route($route) : '#' }}" class="nav-link">{{ $label }}</a></li>
            @endforeach
        </ul>

        @if($weather)
            <div class="hidden md:flex items-center gap-2.5 px-3.5 py-2 bg-foam border border-ink-line rounded-full font-mono text-xs">
                <span class="w-1.5 h-1.5 bg-sun rounded-full ring-4 ring-sun/20"></span>
                <span>{{ $weather['temp'] }}°C · {{ $weather['wind_label'] }}</span>
            </div>
        @endif

        <button @click="open = !open" class="lg:hidden ml-auto p-2" aria-label="Menú">
            <svg x-show="!open" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/></svg>
            <svg x-show="open" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M6 18L18 6" stroke-linecap="round"/></svg>
        </button>
    </div>

    <div x-show="open" x-transition class="lg:hidden border-t border-ink-line bg-sand px-5 py-4">
        <ul class="flex flex-col gap-3">
            @foreach($links as [$route, $label])
                <li><a href="{{ Route::has($route) ? route($route) : '#' }}" class="nav-link text-lg">{{ $label }}</a></li>
            @endforeach
        </ul>
    </div>
</nav>
