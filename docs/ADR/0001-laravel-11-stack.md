# ADR-0001 — Stack Laravel 11 + Blade + Tailwind

**Fecha:** 2026-04-21
**Estado:** Aceptado

## Contexto

El sitio anterior corre en Zend Framework 1.11 (PHP), modificado a lo largo de los años con poca estructura. El sitio estaba caído al inicio del proyecto: dependencias muertas, MercadoPago integration broken, base de datos con encoding mixto latin1/utf8. Mantenibilidad ~0.

Necesitamos:
- Stack moderno con comunidad activa
- Ergonomía de developer high (tests, migrations, ORM)
- Que un dev nuevo pueda contribuir en menos de un día
- Costo bajo (open source, sin licencias)
- Continuar siendo PHP (skill de quien sigue manteniendo)

## Decisión

**Laravel 11** como framework, con stack:

- **Backend:** Laravel 11.51 + PHP 8.4 FPM
- **Templates:** Blade (server-rendered)
- **Frontend:** Tailwind 3 + Alpine.js (sin SPA)
- **Build:** Vite
- **DB:** MariaDB 11
- **Cache + queue + sessions:** Redis 7
- **Auth:** Breeze (login + reset) + Spatie Permission (RBAC)
- **API:** Sanctum (ver [ADR-0003](0003-sanctum-private-api.md))
- **Testing:** PHPUnit + RefreshDatabase + factories Faker es_AR

Containerizado con Docker Compose. Multi-stage Dockerfile (dev / prod).

## Alternativas consideradas

### Symfony

- Pros: más estructurado, comunidad enterprise, mejor para proyectos grandes con varios devs senior.
- Contras: curva de aprendizaje más alta, menos azúcar sintáctica, ecosistema Eloquent vs Doctrine es trade-off.
- **Descartado:** Laravel tiene mejor onboarding para devs PHP medianos/juniors, que es el target de quien sigue manteniendo.

### Django (Python)

- Pros: admin auto-generado gratis, ORM excelente, gran comunidad.
- Contras: cambio de lenguaje (skill PHP perdido), VPS más complicado de configurar.
- **Descartado:** romper continuidad de skill PHP no compensa.

### Rails

- Pros: convenciones excelentes, productividad alta.
- Contras: cambio de lenguaje, menos hosting baratos en Argentina, comunidad PHP local más fuerte.
- **Descartado:** mismo motivo que Django.

### Astro / Next.js

- Pros: frontend moderno, mejor SEO con SSG, Lighthouse alto.
- Contras: requiere Node serverless o ISR; deploy más caro; cambio de paradigma para mantenedores PHP.
- **Descartado:** Blade + Tailwind nos da SSR rápido, simpler hosting, y la performance es suficiente.

### Filament / Nova

- Pros: panel admin generado gratis.
- Contras: rigidez en el diseño, menos control fino, dependencia adicional.
- **Descartado:** preferimos admin propio con Blade — más control, mismo stack que el público, sin dependencia adicional.

## Consecuencias

### Positivas

- Onboarding rápido (CONTRIBUTING.md + docker compose up)
- Tests robustos con factories realistas
- Migrations + audit log + RBAC nativos del ecosistema
- Deploy estándar Docker Compose en cualquier VPS
- 384+ tests verdes con CI completo

### Negativas

- Sin SPA: cada navegación recarga la página. OK para sitio editorial; si en el futuro se quiere UX más reactiva, evaluar Livewire o Inertia.
- Vite + Tailwind requieren Node en el build (extra step en Dockerfile prod resuelto con stage `node-builder`).
- Blade no es React-friendly: contributors con background frontend pueden necesitar onboarding al patrón.

### Deuda asumida

- Reescritura full vs migración incremental — descartamos lo legacy y lo reescribimos. Riesgo: bugs en feature parity, mitigado con suite de tests E2E.
- Acoplamiento al ecosistema Spatie (permission + activitylog + media). Si Spatie cambia drásticamente, hay rewrite — riesgo bajo dado el track record.
