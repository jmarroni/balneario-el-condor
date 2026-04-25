# Operaciones — Balneario El Cóndor

Scripts y procedimientos para producción.

## Scripts

| Script | Uso |
|---|---|
| `backup.sh` | Genera dump DB + tar de storage en `$BACKUP_DIR/<timestamp>/` |
| `restore.sh <path>` | Restaura DB y storage desde un backup. Pide confirmación |
| `deploy.sh [version]` | Pull, build, migrate, swap, smoke. Hace backup pre-deploy |
| `smoke.sh` | Verifica que las rutas críticas responden |
| `cron.example` | Ejemplo de cron del host para backups diarios |

## Backups

`./backup.sh` deja en `$BACKUP_DIR/<YYYYMMDD-HHMMSS>/`:

- `db.sql.gz` — `mariadb-dump --single-transaction --routines --triggers --events`, comprimido
- `storage.tar.gz` — `storage/app/` completo (uploads, legacy media, logs persistentes)

Retención automática: archivos más viejos que `$BACKUP_RETENTION_DAYS` se eliminan.

Cron sugerido: ver `cron.example`.

### Off-site (recomendado)

`backup.sh` deja todo en el host. Para resiliencia real, sumar después del backup un sync a S3/B2/rclone:

```bash
# Ejemplo con rclone (configurado previamente con `rclone config`)
rclone sync /var/backups/balneario remote:balneario-backups --max-age 30d
```

## Restore

```
./restore.sh /var/backups/balneario/20260425-120000
```

Pide confirmación explícita (`yes`). Sobrescribe DB y storage del entorno **prod actual**.

## Deploy de actualización

```
./deploy.sh v1.2.3
```

El script:
1. Hace backup pre-deploy (puede revertirse con `restore.sh`)
2. `git fetch --tags && git checkout <version>`
3. Build de la imagen prod
4. Migra (con app temporal, sin afectar workers en marcha)
5. Recrea containers `app`, `worker`, `scheduler`, `nginx`
6. Espera 10s + corre `smoke.sh`

Si el smoke falla, queda el backup pre-deploy para `restore.sh`.

## Variables

Todas se leen de `app/.env.production`:

- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `BACKUP_DIR` (default `/var/backups/balneario`)
- `BACKUP_RETENTION_DAYS` (default 14)
- `APP_URL` (consumido por `smoke.sh`)

## Permisos

Los scripts requieren ejecutables:

```
chmod +x ops/*.sh
```

Y el usuario que los ejecuta debe estar en el grupo `docker` (o ser root).
