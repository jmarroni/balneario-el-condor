# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Tourism website for Balneario El Cóndor (Argentina). PHP legacy stack: **Zend Framework 1** app on **PHP** (running via php-fpm) with **MariaDB**, fronted by **nginx**. Content is in Spanish.

## Running the stack

Docker Compose orchestrates three services (`php`, `nginx`, `db` aka `thor`):

```bash
docker-compose up -d        # start
docker-compose logs -f nginx
docker-compose down
```

Site is served on `http://localhost:${BALNEARIO_PORT}` (default `8000`, see `.env`). DB is exposed on `${DB_PORT}` (3306). Credentials and ports live in `.env` at the repo root — do not hardcode them.

The DB dump `balneario-el-condor.sql` is a full schema+data snapshot; it is **not** auto-loaded by compose (the `./docker/db/init` mount path in `docker-compose.yml` does not exist in the repo). To seed a fresh DB, import it manually:

```bash
docker exec -i thor mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < balneario-el-condor.sql
```

There is no build step, no package manager lockfile, and no test suite. Frontend assets under `htdocs/vendor/` and `htdocs/min/` are vendored directly.

## Architecture — the important parts

### Two separate Zend 1 applications, same pattern

1. **Public site** — `htdocs/` (webroot) + `00_private/application/` (MVC code, outside webroot).
2. **Members area** — `socios/htdocs/` + `socios/00_private/application/`.

Each has its own `index.php` front controller that bootstraps a `Zend_Application` from `configs/application.ini`. When working on one, confirm which app — both have `controllers/`, `models/`, `views/`, `Bootstrap.php`.

### Request lifecycle (public site)

`htdocs/index.php` → defines `APPLICATION_PATH` as `../00_private/application` → `Zend_Application` bootstraps from `application.ini` → dispatches to a controller in `00_private/application/controllers/`. All URLs that aren't real files/dirs are routed through `index.php` via `htdocs/.htaccess` (Apache) and `nginx/sites/balneario-el-condor.conf` `try_files` (nginx).

### Custom Spanish-slug routes live in Bootstrap

`00_private/application/Bootstrap.php::_initRouters()` registers routes for pretty URLs (`/horario-colectivo`, `/club-de-amigos/:cl_id`, `/recetas/:keyword`, etc.). New pretty URLs go here, not in a routes config file. `_initRutaimg()` defines site-wide constants (`RUTA_SITIO`, `RUTA_IMAGENES`, `CLAVE_JSON`, and numeric section IDs like `MAREAS=1`, `AGENDA=3`) — controllers rely on these.

### Model layer is two-tier

- `models/DbTable/*.php` — `Zend_Db_Table` classes, one per table (e.g. `novedades.php`, `agenda.php`, `mareas.php`).
- `models/*Mapper.php` — data mapper classes on top (e.g. `usuariosMapper.php`, `ubicacionMapper.php`). Controllers generally use the mappers.

### Config gotchas

- `00_private/application/configs/application.ini` hardcodes `resources.db.params.host = "localhost"` and `username = "root"`. Inside Docker the DB host is `db` (service name) or `thor` (container name), not `localhost`. Before changing, verify whether the running deployment overrides this via environment section or a committed patch.
- The same INI still references `C:/wamp64/www/library/` (Windows) in `includePaths.library` — a legacy WAMP artifact. The actual Zend library is loaded from elsewhere in the include_path; don't trust this line as authoritative.
- Two nginx configs exist: `nginx/conf.d/default.conf` (the one actually mounted via `NGINX_PATH_CONFIG`, uses `fastcgi_pass php:9000`) and `nginx/sites/balneario-el-condor.conf` (has `fastcgi_pass people_backend:80`, not mounted by default). If you edit nginx, edit the one matching `NGINX_PATH_CONFIG` in `.env`.
- `php/Dockerfile` builds a PHP 5.6-cli image with supercronic and a `cron` file, but `docker-compose.yml` uses the stock `php:fpm-alpine` image for the `php` service — the custom image is **not** currently wired in. If you need the custom build, switch the `php` service to `build:` and set `USER_ID`.

### Cron jobs

`crons/cronclima.php` is a standalone weather-fetch script (uses legacy `mysql_*` ext, hardcoded creds). It is **not** executed by the compose stack by default — schedule it externally or wire it into the `php` container via supercronic.

### Legacy artifacts to be aware of

- `.svn/` directories are present alongside `.git` — the repo was migrated from SVN. Do not modify `.svn/` contents.
- `hdtocs/` (typo of `htdocs/`) contains only a stub `index.html` — ignore unless investigating a deploy misconfiguration.
- `htdocs/error_log` (~800KB) is a checked-in PHP error log; don't treat it as source.
- `htdocs/camara.php` embeds an RTSP stream via the deprecated VLC ActiveX plugin — effectively dead in modern browsers.
