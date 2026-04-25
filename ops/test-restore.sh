#!/usr/bin/env bash
# Test de restore semanal: verifica que el último backup off-site se
# restaura correctamente y tiene data esperada.
# Uso: ops/test-restore.sh
# Requiere: rclone configurado + docker

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
ENV_FILE="$APP_DIR/.env.production"

[ -f "$ENV_FILE" ] || { echo "ERROR: $ENV_FILE no existe"; exit 1; }

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

if [ -z "${RCLONE_REMOTE:-}" ]; then
    echo "ERROR: RCLONE_REMOTE no está set en .env.production" >&2
    exit 1
fi

if ! command -v rclone >/dev/null 2>&1; then
    echo "ERROR: rclone no instalado" >&2
    exit 1
fi

echo "→ Buscando último backup en $RCLONE_REMOTE"
LATEST=$(rclone lsf "$RCLONE_REMOTE" --dirs-only | grep -E '^20' | sort | tail -1 | tr -d '/')

if [ -z "$LATEST" ]; then
    echo "ERROR: no hay backups en $RCLONE_REMOTE" >&2
    exit 1
fi

echo "→ Último backup: $LATEST"

TMPDIR=$(mktemp -d -t balneario-restore-test.XXXXXX)
trap "rm -rf $TMPDIR; docker stop backup-test 2>/dev/null || true" EXIT

echo "→ Descargando $LATEST/db.sql.gz"
rclone copy "$RCLONE_REMOTE/$LATEST/db.sql.gz" "$TMPDIR/" --quiet

[ -f "$TMPDIR/db.sql.gz" ] || { echo "ERROR: db.sql.gz no descargó" >&2; exit 1; }

echo "→ Iniciando MariaDB temporal"
docker run -d --name backup-test --rm \
    -e MYSQL_ROOT_PASSWORD=test \
    -e MYSQL_DATABASE=balneario_restore_test \
    mariadb:11 > /dev/null

# Esperar healthy
for i in $(seq 1 30); do
    if docker exec backup-test mariadb -uroot -ptest -e "SELECT 1" >/dev/null 2>&1; then
        break
    fi
    sleep 2
done

echo "→ Restaurando dump"
gunzip -c "$TMPDIR/db.sql.gz" | docker exec -i backup-test \
    mariadb -u root -ptest balneario_restore_test 2>/dev/null

# Verificar que tiene data esperada
NEWS_COUNT=$(docker exec backup-test mariadb -u root -ptest -B -N \
    -e "SELECT COUNT(*) FROM balneario_restore_test.news" 2>/dev/null || echo 0)
USERS_COUNT=$(docker exec backup-test mariadb -u root -ptest -B -N \
    -e "SELECT COUNT(*) FROM balneario_restore_test.users" 2>/dev/null || echo 0)
TIDES_COUNT=$(docker exec backup-test mariadb -u root -ptest -B -N \
    -e "SELECT COUNT(*) FROM balneario_restore_test.tides" 2>/dev/null || echo 0)

echo "→ Verificación de integridad:"
echo "   news:  $NEWS_COUNT"
echo "   users: $USERS_COUNT"
echo "   tides: $TIDES_COUNT"

FAILS=0
[ "$NEWS_COUNT" -ge 10 ] || { echo "✗ FAIL: news count <10"; FAILS=$((FAILS+1)); }
[ "$USERS_COUNT" -ge 1 ] || { echo "✗ FAIL: users count <1"; FAILS=$((FAILS+1)); }
[ "$TIDES_COUNT" -ge 100 ] || { echo "✗ FAIL: tides count <100"; FAILS=$((FAILS+1)); }

if [ "$FAILS" -gt 0 ]; then
    echo "✗ Backup test FAILED ($FAILS checks)" >&2
    if [ -n "${DEPLOY_WEBHOOK_URL:-}" ]; then
        curl -sX POST -H "Content-Type: application/json" \
            -d "{\"text\":\"⚠️ Test de restore semanal FAILED para $LATEST: $FAILS checks fallaron\"}" \
            "$DEPLOY_WEBHOOK_URL" > /dev/null
    fi
    exit 1
fi

echo "✓ Backup verificado: news=$NEWS_COUNT users=$USERS_COUNT tides=$TIDES_COUNT"
