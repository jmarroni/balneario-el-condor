# Contributing — Balneario El Cóndor

Gracias por contribuir. Este documento cubre el setup local, las convenciones del proyecto, y el flujo para mandar cambios.

## Setup local

### Requisitos

- Docker Engine 24+ y Docker Compose v2
- Git
- Node 20+ (solo si trabajás assets fuera del contenedor)

### Primera vez

```bash
git clone https://github.com/<owner>/balneario-el-condor.git
cd balneario-el-condor/app

cp .env.example .env
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run dev   # o build
```

El sitio queda en `http://localhost:18081` (o el puerto que pusiste en `APP_PORT`).

### Usuarios demo

El `DemoUsersSeeder` crea:
- `admin@balneario.local` (rol admin) — password `admin1234`
- `editor@balneario.local` (rol editor) — password `editor1234`
- `moderator@balneario.local` (rol moderator) — password `moder1234`

(Cambialos en seguida; son solo para dev local.)

## Convenciones

### Código

- **Pint:** formatter oficial. Correr antes de commitear:
  ```bash
  docker compose exec app ./vendor/bin/pint
  ```
  CI corre `pint --test` y bloquea si hay diffs.
- **Naming:** PSR-12 + convenciones Laravel. Models singular (`News`, `Event`), tablas plural (`news`, `events`). Controllers terminan en `Controller`. Form requests con prefijo `Store`/`Update`.
- **Blade components:** kebab-case en directorio (`components/public/article-card.blade.php`), invocados con `<x-public.article-card>`.
- **Imports:** `use` statements ordenados (Pint los acomoda).

### Tests

- **Suite:** PHPUnit. Correr con:
  ```bash
  docker compose exec app php artisan test
  ```
- **Test cases base:** `AdminTestCase` para admin (incluye seed roles + admin/editor/moderator users).
- **Coverage objetivo:** 80%+. Cada feature nuevo debe traer su test.
- **No commitear con tests rojos.** CI bloquea PRs con suite roja.

### Git

- **Branches:** `feature/<scope>-<description>` (ej. `feature/api-rate-limit`).
- **Commits:** convencionales. Tipos:
  - `feat(scope):` nueva feature
  - `fix(scope):` bug fix
  - `refactor(scope):` cambio interno sin cambio de comportamiento
  - `docs(scope):` solo docs
  - `test(scope):` solo tests
  - `chore(scope):` infra/build/deps
  - `ci(scope):` cambios al CI
- **Message body:** explicá *por qué*, no *qué* (el diff cuenta el qué).

### PR checklist

Antes de mandar PR:

- [ ] `php artisan test` verde
- [ ] `pint --test` verde
- [ ] `npm run build` sin errores
- [ ] No hay `dd()` / `dump()` / `console.log` olvidados
- [ ] No hay secrets hardcoded
- [ ] Si tocaste schema: migración hace `up()` y `down()` reversibles, ajustaste `MigrationRollbackTest::--step` y agregaste/actualizaste factory
- [ ] Si agregaste tabla con audit: aplicar `LogsActivity` trait
- [ ] Si tocaste UI pública: probaste en mobile (DevTools) y en theme claro/oscuro si aplica
- [ ] Si tocaste API: actualizar Scribe docstrings y correr `php artisan scribe:generate`
- [ ] Sumás docs en `docs/` si la feature requiere config nueva en producción

## Plans + tasks

Las features grandes se planifican antes de codear:

1. Crear `docs/superpowers/plans/YYYY-MM-DD-<nombre>.md` con estructura de tasks.
2. Cada task tiene archivos a tocar, código clave, tests, y criterio de aceptación.
3. Implementar tarea por tarea, commitear por tarea.
4. Mergear a master cuando todas las tareas verde + suite verde.

Ejemplos: ver `docs/superpowers/plans/`.

## Code review

Como reviewer, mirá:

- ¿La feature tiene tests?
- ¿Los tests cubren happy path + edge cases (validation, auth, 404)?
- ¿La policy está bien (admin/editor/moderator según el módulo)?
- ¿La UI hace sentido en mobile?
- ¿No hay N+1 queries (revisar Pulse local o `php artisan db:monitor`)?
- ¿Los strings están en español argentino?
- ¿Hay deuda obvia en TODOs sin issue?

## Decisiones de arquitectura

Para cambios grandes (cambiar stack, agregar dependencia heavy, cambiar diseño de auth, etc.), escribir un ADR en `docs/ADR/`. Ver `docs/ADR/README.md`.

## Reportar bugs

Issues en GitHub con:
- Versión (commit hash o tag)
- Steps to reproduce
- Output esperado vs actual
- Logs relevantes (`storage/logs/laravel.log`)

## Contacto

- Dev lead: TODO
- Stakeholder (Turismo): turismo@elcondor.gob.ar
