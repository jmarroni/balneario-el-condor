#!/usr/bin/env bash
# Backup de Balneario El Cóndor — DB + storage
# Uso: ops/backup.sh
# Requiere: docker, gzip, tar
# Lee credenciales de app/.env.production

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
ENV_FILE="$APP_DIR/.env.production"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml -f $APP_DIR/docker-compose.prod.yml"

if [ ! -f "$ENV_FILE" ]; then
    echo "ERROR: $ENV_FILE no existe" >&2
    echo "Crear desde: cp $APP_DIR/.env.production.example $ENV_FILE" >&2
    exit 1
fi

# Cargar variables del .env.production
set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

BACKUP_DIR="${BACKUP_DIR:-/var/backups/balneario}"
RETENTION="${BACKUP_RETENTION_DAYS:-14}"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_PATH="$BACKUP_DIR/$TIMESTAMP"

mkdir -p "$BACKUP_PATH"

echo "→ Backup DB → $BACKUP_PATH/db.sql.gz"
$COMPOSE exec -T db sh -c \
    "mariadb-dump --single-transaction --routines --triggers --events \
     -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}" \
    | gzip > "$BACKUP_PATH/db.sql.gz"

echo "→ Backup storage → $BACKUP_PATH/storage.tar.gz"
$COMPOSE exec -T app sh -c "tar -czf - -C /var/www/html storage/app" \
    > "$BACKUP_PATH/storage.tar.gz"

echo "→ Tamaños:"
du -sh "$BACKUP_PATH"/*

# Off-site sync (opcional — solo si RCLONE_REMOTE está set)
if [ -n "${RCLONE_REMOTE:-}" ]; then
    echo "→ Off-site sync → $RCLONE_REMOTE/$TIMESTAMP"
    if command -v rclone >/dev/null 2>&1; then
        rclone copy "$BACKUP_PATH" "$RCLONE_REMOTE/$TIMESTAMP" \
            --transfers 4 --checkers 8 --quiet
        echo "✓ Off-site OK"
    else
        echo "⚠ rclone no instalado en el host — saltando off-site sync" >&2
    fi
fi

echo "→ Eliminando backups con más de $RETENTION días"
find "$BACKUP_DIR" -maxdepth 1 -type d -name "20*" -mtime +"$RETENTION" -exec rm -rf {} \; || true

echo "✓ Backup OK: $BACKUP_PATH"
