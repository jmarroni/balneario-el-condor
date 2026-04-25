# ADR-0003 — API privada con Sanctum (no pública)

**Fecha:** 2026-04-25
**Estado:** Aceptado

## Contexto

El spec inicial mencionaba "API pública v1" pensada para apps externas leyendo data del balneario. Al revisar con stakeholder, se aclaró:

- No hay apps externas planeadas que justifiquen API pública abierta.
- La data sensible (mensajes de contacto, suscriptores newsletter, registrations a eventos) NO debe ser accesible sin auth.
- Sí hay valor en una API para futuros clientes internos (app móvil del municipio, integración con sitio del gobierno provincial, dashboard offline para turismo).

Necesitamos:
- Auth simple para clientes internos (no Auth0 ni OAuth flows complejos)
- Granularidad de permisos (no todos los tokens pueden todo)
- Rate limiting para evitar abuso accidental
- Excepción documentada para forms públicos (formulario de contacto desde una app externa que no tiene login)

## Decisión

**Laravel Sanctum** con tokens Bearer personales, restringidos por permisos Spatie del usuario emisor.

**Características:**

- Endpoints `/api/v1/*` requieren `auth:sanctum` middleware.
- Tokens emitidos por usuarios admin desde `/admin/tokens` UI.
- Cada token captura un *snapshot* de los permisos del usuario al momento de creación (abilities).
- Rate limit por rol: admin 300, editor 180, moderator 120, otro auth 60, guest 30 rpm.
- **Excepción:** `POST /api/v1/contact` es público (sin auth) con `throttle:10,1` por IP. Para apps externas que solo mandan formulario.

**Endpoints implementados:**
- 23 read (news, events, lodgings, venues, rentals, classifieds, recipes, gallery, tides, weather, etc.)
- 11 write (CRUD news + events, moderation classified/messages/subscribers)
- 1 público (contacto)

## Alternativas consideradas

### Laravel Passport (OAuth 2.0)

- Pros: estándar OAuth, scopes granulares, refresh tokens.
- Contras: complejidad alta, flows que no necesitamos (authorization code, password grant, client credentials), tablas adicionales, UI complicada para emitir tokens.
- **Descartado:** matar mosquito a cañonazos. Sanctum cubre nuestro caso 100%.

### API keys estáticas (sin user)

- Pros: más simple, key en config.
- Contras: no auditable (¿quién hizo qué?), rotación manual, no scopes por usuario.
- **Descartado:** queremos audit log + permisos por usuario. Sanctum los da gratis.

### API pública sin auth con rate limiting agresivo

- Pros: máximo acceso, ergonómico.
- Contras: imposible de proteger contra scraping/abuso. Data sensible (clasificados con email) expuesta.
- **Descartado:** el costo de un leak de mensajes de contacto > el valor de "API pública por si acaso".

### JWT custom

- Pros: stateless, escalable.
- Contras: revocación complicada, lock-in con la implementación, Sanctum hace lo mismo más simple.
- **Descartado:** no escalamos a millones de requests; Sanctum + Redis es suficiente.

## Consecuencias

### Positivas

- 1 token = 1 usuario = 1 set de permisos auditable.
- Revocación instantánea desde UI admin.
- Permisos heredados de Spatie (mismo sistema que admin web).
- Rate limit por rol previene abuso accidental.
- `/api/v1/contact` público con throttle cubre casos legítimos sin auth.

### Negativas

- **Snapshot de abilities:** si un usuario pierde un permiso, sus tokens existentes siguen con el permiso viejo. Documentado en UI: "revocá y recreá si cambian permisos".
- Sin OAuth flow: si en el futuro queremos integrar con Google/Apple SSO, hay que sumar Socialite — no Sanctum.
- Tabla `personal_access_tokens` crece linealmente con tokens. Cron de cleanup mensual sobre `last_used_at < 90 días` sería sano.

### Deuda asumida

- No hay versión `v2` planeada. Cuando rompamos contrato API, hay que crear `/api/v2/` y mantener `/v1/` durante transición. Diseñar v1 con versioning en mente desde el principio.
- Documentación con Scribe (`/docs`) protegida por basic auth en prod. Si quisiéramos docs públicas, decidir si exponer el OpenAPI spec separadamente.
