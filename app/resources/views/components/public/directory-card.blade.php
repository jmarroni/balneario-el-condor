@props([
    'item',
    'route',           // route name for show, e.g. 'hospedajes.show'
    'titleField' => 'name', // 'name' or 'title'
    'eyebrow' => null, // optional uppercase tag (type, category, etc.)
    'description' => null, // optional truncated text below title
    'meta' => null,    // optional small line (address, phone, etc.)
])
@php
    use Illuminate\Support\Str;

    $title = $item->{$titleField} ?? '—';

    $thumb = method_exists($item, 'media') && $item->media ? $item->media->first() : null;
    $thumbUrl = $thumb?->path
        ? (Str::startsWith($thumb->path, ['http://', 'https://', '/'])
            ? $thumb->path
            : asset('storage/' . ltrim($thumb->path, '/')))
        : null;

    $href = route($route, $item);
@endphp

<a href="{{ $href }}"
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
        @if ($eyebrow)
            <div class="font-mono text-[10px] tracking-[0.2em] uppercase text-coral mb-2">
                {{ $eyebrow }}
            </div>
        @endif

        <h3 class="font-display text-[22px] font-medium leading-[1.15] text-ink mb-3 group-hover:text-coral transition-colors"
            style="font-variation-settings: 'opsz' 48, 'SOFT' 40; letter-spacing: -0.015em;">
            {{ $title }}
        </h3>

        @if ($description)
            <p class="text-ink-soft text-[15px] leading-snug line-clamp-2 mb-4 flex-1">
                {{ Str::limit($description, 140) }}
            </p>
        @endif

        @if ($meta)
            <div class="flex items-start gap-2 font-mono text-[11px] text-ink-soft pt-3 border-t border-ink-line mt-auto">
                <svg class="shrink-0 mt-0.5" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M8 1.5C5 1.5 3 3.5 3 6.3 3 9.5 8 14.5 8 14.5s5-5 5-8.2C13 3.5 11 1.5 8 1.5z"/>
                    <circle cx="8" cy="6.2" r="1.6"/>
                </svg>
                <span class="line-clamp-1">{{ $meta }}</span>
            </div>
        @endif
    </div>
</a>
