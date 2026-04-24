@props([
    'news',
    'variant' => 'default', // default | featured | compact
])

@php
    use Illuminate\Support\Str;

    $thumb     = $news->media->first();
    $thumbUrl  = $thumb?->path
        ? (Str::startsWith($thumb->path, ['http://', 'https://', '/'])
            ? $thumb->path
            : asset('storage/' . ltrim($thumb->path, '/')))
        : null;

    $url      = route('novedades.show', $news);
    $category = $news->category?->name;
    $date     = $news->published_at?->locale('es')->isoFormat('D MMMM YYYY');
    $reading  = $news->reading_minutes;
@endphp

@if ($variant === 'featured')
    {{-- =================== FEATURED (hero card with polaroid tilt) =================== --}}
    <a href="{{ $url }}"
       class="group relative block bg-foam border border-ink-line rounded-md overflow-hidden
              transition-[transform,box-shadow] duration-500 ease-[cubic-bezier(0.65,0,0.35,1)]
              -rotate-[1deg] hover:rotate-0 hover:-translate-y-1 hover:shadow-lift">
        <div class="relative aspect-[16/9] bg-sand-2 bg-cover bg-center overflow-hidden"
             @if ($thumbUrl) style="background-image: url('{{ $thumbUrl }}');" @endif>
            <div class="absolute inset-0"
                 style="background: linear-gradient(180deg, transparent 35%, rgba(15,45,92,0.65));"></div>

            {{-- Coral ribbon --}}
            <span class="absolute top-5 left-5 inline-flex items-center gap-1.5 bg-coral text-sand
                         font-mono text-[10px] tracking-[0.2em] uppercase px-3 py-1.5 rounded-sm shadow-card">
                <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 2l1.8 4 4.2.5-3.2 2.9.9 4.4L8 11.6l-3.7 2.2.9-4.4L2 6.5l4.2-.5L8 2z" stroke-linejoin="round"/>
                </svg>
                Destacada
            </span>
        </div>

        <div class="absolute left-0 right-0 bottom-0 px-9 py-8 z-10 text-foam">
            @if ($category)
                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-sun mb-3">
                    Novedades · {{ $category }}
                </div>
            @endif
            <h3 class="font-display font-medium leading-[0.95] text-foam"
                style="font-size: clamp(32px, 4.4vw, 64px); letter-spacing: -0.025em;
                       font-variation-settings: 'opsz' 144, 'SOFT' 50;">
                {{ $news->title }}
            </h3>
            <p class="mt-4 text-[15px] max-w-[58ch] text-[rgba(250,243,227,0.86)] leading-snug">
                {{ $news->excerpt }}
            </p>
            <div class="mt-5 flex justify-between font-mono text-[11px] text-[rgba(250,243,227,0.7)]">
                <span>{{ $date }}</span>
                <span>Lectura · {{ $reading }} min</span>
            </div>
        </div>
    </a>

@elseif ($variant === 'compact')
    {{-- =================== COMPACT (related, sidebar) =================== --}}
    <a href="{{ $url }}"
       class="group flex gap-4 items-start py-4 border-b border-ink-line last:border-0
              transition-colors duration-200 hover:text-coral">
        <div class="shrink-0 w-[88px] h-[72px] rounded bg-sand-2 bg-cover bg-center overflow-hidden border border-ink-line"
             @if ($thumbUrl) style="background-image: url('{{ $thumbUrl }}');" @endif></div>
        <div class="flex-1 min-w-0">
            @if ($category)
                <div class="font-mono text-[9px] tracking-[0.2em] uppercase text-coral mb-1">{{ $category }}</div>
            @endif
            <h3 class="font-display text-[16px] font-medium leading-snug text-ink line-clamp-2 group-hover:text-coral"
                style="font-variation-settings: 'opsz' 24, 'SOFT' 30;">
                {{ $news->title }}
            </h3>
            <div class="mt-1 font-mono text-[10px] text-ink-soft">{{ $date }}</div>
        </div>
    </a>

@else
    {{-- =================== DEFAULT (grid card) =================== --}}
    <a href="{{ $url }}"
       class="group block bg-foam border border-ink-line rounded-md overflow-hidden flex flex-col
              transition-[transform,box-shadow] duration-300 ease-[cubic-bezier(0.65,0,0.35,1)]
              hover:-translate-y-1 hover:shadow-lift">

        <div class="aspect-[4/3] bg-sand-2 bg-cover bg-center relative overflow-hidden"
             @if ($thumbUrl) style="background-image: url('{{ $thumbUrl }}');" @endif>
            @if (! $thumbUrl)
                <div class="absolute inset-0 flex items-center justify-center text-ink-line">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <circle cx="9" cy="11" r="1.5"/>
                        <path d="M3 17l5-5 4 4 3-3 6 6"/>
                    </svg>
                </div>
            @endif
            <div class="absolute inset-x-0 bottom-0 h-1/3"
                 style="background: linear-gradient(180deg, transparent, rgba(15,45,92,0.18));"></div>
        </div>

        <div class="p-6 flex flex-col flex-1">
            @if ($category)
                <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral mb-2">
                    {{ $category }}
                </div>
            @endif
            <h3 class="font-display text-[24px] font-medium leading-[1.1] text-ink mb-3"
                style="font-variation-settings: 'opsz' 48, 'SOFT' 40; letter-spacing: -0.015em;">
                {{ $news->title }}
            </h3>
            <p class="text-ink-soft text-[15px] leading-snug line-clamp-2 mb-4 flex-1">
                {{ $news->excerpt }}
            </p>
            <div class="flex justify-between items-center font-mono text-[11px] text-ink-soft pt-3 border-t border-ink-line">
                <span>{{ $date }}</span>
                <span>Lectura · {{ $reading }} min</span>
            </div>
        </div>
    </a>
@endif
