#!/usr/bin/env bash
# Deploy a prod. Uso: ops/deploy.sh [version]
# Ej: ops/deploy.sh v1.2.3
# El script hace backup pre-deploy, maintenance mode, git checkout,
# build, migrate, swap containers, smoke + maintenance off.

set -euo pipefail

VERSION="${1:-latest}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
APP_DIR="$REPO_ROOT/app"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml -f $APP_DIR/docker-compose.prod.yml"
SECRET=""

# Cargar .env.production para webhook + url
if [ -f "$APP_DIR/.env.production" ]; then
    set -a
    # shellcheck disable=SC1091
    source "$APP_DIR/.env.production"
    set +a
fi

# Webhook helper (Slack/Discord compatible payload)
notify() {
    if [ -n "${DEPLOY_WEBHOOK_URL:-}" ]; then
        curl -sX POST -H "Content-Type: application/json" \
            -d "{\"text\":$(printf '%s' "$1" | jq -Rs .)}" \
            "$DEPLOY_WEBHOOK_URL" > /dev/null 2>&1 || true
    fi
}

# En cualquier error, intentar levantar el sitio + notificar
on_error() {
    local exit_code=$?
    notify "❌ Deploy *$VERSION* FAILED (paso: $1, exit $exit_code). Sitio en mantenimiento, revisar logs."
    exit "$exit_code"
}
trap 'on_error unknown' ERR

cd "$REPO_ROOT"

echo "==================================================================="
echo "  Deploy Balneario El Cóndor → $VERSION"
echo "==================================================================="

notify "🚀 Deploy iniciado: \`$VERSION\` (rev \`$(git rev-parse --short HEAD)\`)"

echo
echo "→ [1/7] Backup pre-deploy"
trap 'on_error backup' ERR
"$SCRIPT_DIR/backup.sh"

echo
echo "→ [2/7] Activando maintenance mode con bypass secret"
trap 'on_error maintenance-on' ERR
SECRET=$(openssl rand -hex 16)
$COMPOSE exec -T app php artisan down \
    --render="errors::503" \
    --secret="$SECRET" \
    --refresh=15 || true
echo "   Bypass URL: ${APP_URL:-https://elcondor.gob.ar}/${SECRET}"

echo
echo "→ [3/7] Pulling code"
trap 'on_error git-checkout' ERR
git fetch --tags --quiet
if [ "$VERSION" != "latest" ]; then
    git checkout "$VERSION"
else
    git pull --ff-only
fi
echo "   en: $(git rev-parse --short HEAD) ($(git describe --tags 2>/dev/null || echo 'sin tag'))"

echo
echo "→ [4/7] Build app image (target=prod)"
trap 'on_error build' ERR
APP_VERSION="$VERSION" $COMPOSE build app

echo
echo "→ [5/7] Migrate (con app temporal)"
trap 'on_error migrate' ERR
APP_VERSION="$VERSION" $COMPOSE run --rm app php artisan migrate --force --no-interaction

echo
echo "→ [6/7] Swap containers"
trap 'on_error swap' ERR
APP_VERSION="$VERSION" $COMPOSE up -d --no-deps --force-recreate app worker scheduler nginx

echo
echo "→ [7/7] Smoke (10s grace) + maintenance off"
trap 'on_error smoke' ERR
sleep 10
"$SCRIPT_DIR/smoke.sh"

# Si llegamos hasta acá: smoke OK, levantar mantenimiento
$COMPOSE exec -T app php artisan up || true

# Cache purge Cloudflare opcional
if [ -n "${CLOUDFLARE_ZONE_ID:-}" ] && [ -n "${CLOUDFLARE_API_TOKEN:-}" ]; then
    echo "→ Purgando cache Cloudflare"
    curl -sX POST "https://api.cloudflare.com/client/v4/zones/${CLOUDFLARE_ZONE_ID}/purge_cache" \
         -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
         -H "Content-Type: application/json" \
         -d '{"purge_everything":true}' > /dev/null || true
fi

trap - ERR
notify "✅ Deploy *$VERSION* OK ($(date +%H:%M))"

echo
echo "==================================================================="
echo "  ✓ Deploy $VERSION OK"
echo "==================================================================="
echo
echo "Rollback rápido si hace falta:"
echo "  ops/restore.sh /var/backups/balneario/<timestamp-pre-deploy>"
echo "  o: ops/deploy.sh <version-anterior>"
