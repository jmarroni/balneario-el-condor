@props([
    'title' => 'Balneario El Cóndor',
    'description' => 'Pueblo costero a 30 km de Viedma, en la desembocadura del río Negro sobre el Atlántico patagónico.',
    'image' => null,
])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} · Balneario El Cóndor</title>
    <meta name="description" content="{{ $description }}">

    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Balneario El Cóndor">
    <meta property="og:locale" content="es_AR">
    @if($image)
        <meta property="og:image" content="{{ $image }}">
    @endif

    <meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if($image)
        <meta name="twitter:image" content="{{ $image }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT@0,9..144,300..900,0..100;1,9..144,300..900,0..100&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=JetBrains+Mono:wght@400;500&display=swap">

    {{-- Leaflet (mapas en directorio público) --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
            defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Plausible Analytics (privacy-first, sin cookies). Solo si está configurado. --}}
    @if(config('app.plausible_domain'))
        <script defer
                data-domain="{{ config('app.plausible_domain') }}"
                src="{{ rtrim(config('app.plausible_host', 'https://plausible.io'), '/') }}/js/script.js"></script>
        <script>
            window.plausible = window.plausible || function () {
                (window.plausible.q = window.plausible.q || []).push(arguments);
            };
        </script>
    @endif

    {{ $head ?? '' }}
</head>
<body>
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:bg-coral focus:text-sand focus:px-4 focus:py-2 focus:rounded focus:z-50">
        Saltar al contenido
    </a>

    <x-public.nav />

    <main id="main">
        {{ $slot }}
    </main>

    <x-public.footer />

    @stack('scripts')
</body>
</html>
