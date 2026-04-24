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
    @if($image)
        <meta property="og:image" content="{{ $image }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT@0,9..144,300..900,0..100;1,9..144,300..900,0..100&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=JetBrains+Mono:wght@400;500&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body>
    <x-public.nav />

    <main>
        {{ $slot }}
    </main>

    <x-public.footer />

    @stack('scripts')
</body>
</html>
