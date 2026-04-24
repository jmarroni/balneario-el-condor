@props(['items' => []])
<nav class="text-xs text-slate-500 mb-1">
    @foreach($items as $label => $url)
        @if(!$loop->last)
            <a href="{{ $url }}" class="hover:underline">{{ $label }}</a> /
        @else
            <span class="text-slate-700">{{ is_int($label) ? $url : $label }}</span>
        @endif
    @endforeach
</nav>
