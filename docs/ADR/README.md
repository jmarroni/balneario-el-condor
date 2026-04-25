# Architecture Decision Records (ADRs)

Cada ADR documenta una decisión de arquitectura significativa: contexto que la motivó, opciones consideradas, decisión tomada, y consecuencias.

## Cuándo escribir un ADR

- Cambio de stack (lenguaje, framework, base de datos)
- Decisión sobre dependencias importantes (auth, queue, mail, search)
- Cambios de diseño con impacto en múltiples módulos
- Trade-offs significativos donde futuros devs van a preguntar "por qué así?"

No hace falta para:

- Bug fixes
- Features dentro del patrón establecido
- Refactors menores

## Formato

Un archivo Markdown por ADR, numerado secuencialmente:

```
docs/ADR/0001-titulo-corto.md
docs/ADR/0002-otra-decision.md
```

Cada ADR tiene secciones:
- **Estado:** propuesto / aceptado / superseded
- **Contexto:** qué problema motivó la decisión
- **Decisión:** qué hacemos
- **Alternativas consideradas:** otras opciones y por qué se descartaron
- **Consecuencias:** qué cambia, qué deuda asumimos

## ADRs activos

| # | Título | Estado |
|---|---|---|
| [0001](0001-laravel-11-stack.md) | Stack Laravel 11 + Blade + Tailwind | Aceptado |
| [0002](0002-editorial-design-direction.md) | Diseño editorial costero (Fraunces + paleta sand/ink) | Aceptado |
| [0003](0003-sanctum-private-api.md) | API privada con Sanctum (no pública) | Aceptado |
