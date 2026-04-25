<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Volvemos en un rato — Balneario El Cóndor</title>
<meta name="robots" content="noindex">
@if(isset($exception) && method_exists($exception, 'getHeaders'))
    @php $headers = $exception->getHeaders(); @endphp
    @if(isset($headers['Retry-After']))
        <meta http-equiv="refresh" content="{{ $headers['Retry-After'] }}">
    @endif
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT@0,9..144,300..900,0..100;1,9..144,300..900,0..100&family=Instrument+Sans:wght@400;500&display=swap" rel="stylesheet">

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Instrument Sans', system-ui, sans-serif;
        background: #faf3e3;
        color: #0f2d5c;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
    }
    body::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -180px;
        width: 640px;
        height: 640px;
        background: radial-gradient(circle, rgba(216,155,42,0.22) 0%, transparent 55%);
        pointer-events: none;
    }
    .container {
        max-width: 520px;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    img.logo {
        width: 110px;
        height: auto;
        margin-bottom: 32px;
    }
    .eyebrow {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 11px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #c85a3c;
        margin-bottom: 18px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    .rule { width: 32px; height: 1.5px; background: #c85a3c; }
    h1 {
        font-family: 'Fraunces', serif;
        font-weight: 400;
        font-size: clamp(48px, 8vw, 72px);
        line-height: 0.95;
        letter-spacing: -0.025em;
        margin-bottom: 24px;
        font-variation-settings: 'opsz' 144, 'SOFT' 50;
    }
    h1 em {
        font-style: italic;
        color: #c85a3c;
        font-variation-settings: 'opsz' 144, 'SOFT' 100;
    }
    p {
        font-size: 17px;
        color: #3c5a84;
        line-height: 1.65;
        margin-bottom: 16px;
        max-width: 44ch;
        margin-left: auto;
        margin-right: auto;
    }
    .phone {
        margin-top: 36px;
        padding: 20px;
        background: #ffffff;
        border: 1px solid rgba(15,45,92,0.15);
        border-radius: 6px;
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 14px;
    }
    .phone strong {
        display: block;
        font-size: 22px;
        color: #0f2d5c;
        margin-top: 6px;
    }
</style>
</head>
<body>
    <div class="container">
        <img src="/img/logo.png" alt="Balneario El Cóndor" class="logo">

        <div class="eyebrow">
            <span class="rule"></span>
            Mantenimiento
        </div>

        <h1>Volvemos<br>en un <em>rato.</em></h1>

        <p>Estamos haciendo mejoras al sitio. Reintentá en unos minutos — la página se va a refrescar sola.</p>

        <div class="phone">
            Si necesitás algo urgente:
            <strong>Turismo Municipal +54 9 2920 15 3300</strong>
        </div>
    </div>
</body>
</html>
