# ADR-0002 — Diseño editorial costero patagónico

**Fecha:** 2026-04-24
**Estado:** Aceptado

## Contexto

El sitio antiguo usaba un template Bootstrap genérico, sin personalidad propia. Para un sitio turístico de un pueblo costero patagónico con identidad fuerte (faro, cóndor, Atlántico, comunidad chica), el diseño tiene que comunicar lugar — no parecer un dashboard SaaS.

Stakeholder pidió "diseño lindo con los logos y demás", explícitamente "no template Tailwind genérico".

## Decisión

**Dirección:** editorial costera patagónica — magazine moderno con alma de afiche de viajes antiguo. Cálido, familiar, no luxury.

**Tipografía:**
- **Fraunces** (variable, ejes `opsz` 9-144 y `SOFT` 0-100) — display serif con personalidad artesanal
- **Instrument Sans** (variable) — body humanista 400-700
- **JetBrains Mono** — datos: horarios de mareas, coordenadas, grados de viento

**Paleta:**

| Token | HEX | Uso |
|---|---|---|
| `ink` | `#0f2d5c` | Texto principal, fondos oscuros destacados |
| `sun` | `#d89b2a` | Ocre cálido (amarillo del logo, envejecido por sal) |
| `coral` | `#c85a3c` | Acento de calor, CTAs, italics |
| `sand` | `#faf3e3` | Fondo dominante (crema cálida) |
| `foam` | `#ffffff` | Cards, surfaces |

**Principios visuales:**
1. Asimetría editorial — bento con cards de distinto peso
2. Datos como decoración (mareas como instrumento náutico)
3. Grano sutil sobre toda la página (SVG fractal noise)
4. Fotos con rotación polaroid (-1.2°, +4°)
5. Divisores SVG de olas hechos a mano
6. Stamps de fechas estilo sello de correo
7. Hover states intencionales con underline animada
8. Animaciones de entrada staggered en hero

## Alternativas consideradas

### Bootstrap default / Tailwind UI templates

- **Descartado:** stakeholder explícitamente pidió no template genérico. Para un sitio de pueblo con identidad fuerte, los templates leen como SaaS B2B — wrong vibe.

### Dark mode default

- **Descartado:** no aplica al lugar. Crema cálida es identidad. Sand `#faf3e3` evoca arena + papel viejo — el balneario.

### Brutalismo / neo-brutalism

- Pros: muy distintivo, en moda.
- **Descartado:** demasiado edgy para audiencia familiar (turistas, vecinos del pueblo, adultos mayores). El balneario no es una agencia creativa.

### Glassmorphism / luxury

- **Descartado:** el pueblo no es luxury. La estética es "verano familiar en la costa" — no boutique hotel.

### Google Fonts populares (Inter, Roboto, Manrope)

- **Descartado:** son tipografías de SaaS modernos, no de magazine costera. Fraunces con eje `SOFT` da exactamente la calidez artesanal que el lugar tiene.

## Consecuencias

### Positivas

- Identidad visual fuerte y reconocible
- Diferenciación clara vs sitios de turismo genéricos
- Tipografía variable con un solo download (Fraunces tiene `opsz` y `SOFT` axes)
- Tokens CSS bien definidos en Tailwind config — fácil de extender
- Preview HTML estandalone (`design-preview/fase-5-home.html`) sirve como spec viva del diseño

### Negativas

- Más trabajo upfront en Blade (cada vista respeta el patrón en lugar de copiar de UI library)
- Fraunces + Instrument Sans + JetBrains Mono = ~180kb de fonts. Mitigado con `font-display: swap` y `preconnect` a fonts.gstatic.com. En prod considerar self-host.
- Animaciones de entrada agregan ~50ms de bloating en mobile low-end. Aceptable.

### Deuda asumida

- El layout asimétrico es más sensible a contenido inesperado (títulos largos, fotos en aspect ratios raros). Hay que validar cada nuevo módulo en mobile.
- Si en el futuro se quiere unificar el admin con el público, hay que decidir si el admin adopta esta paleta o sigue con tokens más sobrios.

## Referencia

Preview canónico: `design-preview/fase-5-home.html` (estándalone, abre en browser).
