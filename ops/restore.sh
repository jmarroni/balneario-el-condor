#!/usr/bin/env bash
# Restore desde backup. Uso: ops/restore.sh /var/backups/balneario/20260425-120000
# CUIDADO: sobrescribe DB y storage. Pide confirmación.

set -euo pipefail

if [ -z "${1:-}" ]; then
    echo "Uso: $0 <backup-path>" >&2
    echo "Ej:  $0 /var/backups/balneario/20260425-120000" >&2
    exit 1
fi

BACKUP_PATH="$1"
[ -d "$BACKUP_PATH" ] || { echo "ERROR: $BACKUP_PATH no existe"; exit 1; }
[ -f "$BACKUP_PATH/db.sql.gz" ] || { echo "ERROR: falta db.sql.gz"; exit 1; }
[ -f "$BACKUP_PATH/storage.tar.gz" ] || { echo "ERROR: falta storage.tar.gz"; exit 1; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
ENV_FILE="$APP_DIR/.env.production"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml -f $APP_DIR/docker-compose.prod.yml"

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

echo "⚠  Esto va a SOBRESCRIBIR la DB y storage actuales."
echo "   Backup origen: $BACKUP_PATH"
echo "   DB destino:    $DB_DATABASE @ db"
echo
read -r -p "Escribí 'yes' para confirmar: " CONFIRM
[ "$CONFIRM" = "yes" ] || { echo "Abortado"; exit 1; }

echo "→ Restaurando DB"
gunzip -c "$BACKUP_PATH/db.sql.gz" | $COMPOSE exec -T db sh -c \
    "mariadb -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}"

echo "→ Restaurando storage"
$COMPOSE exec -T app sh -c "rm -rf /var/www/html/storage/app && tar -xzf - -C /var/www/html" \
    < "$BACKUP_PATH/storage.tar.gz"

echo "→ Limpiando caches Laravel"
$COMPOSE exec -T app php artisan config:clear
$COMPOSE exec -T app php artisan cache:clear

echo "✓ Restore OK desde $BACKUP_PATH"
