#!/usr/bin/env bash
# Deploy a prod. Uso: ops/deploy.sh [version]
# Ej: ops/deploy.sh v1.2.3
# El script hace backup pre-deploy, git checkout, build, migrate, swap, smoke.

set -euo pipefail

VERSION="${1:-latest}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
APP_DIR="$REPO_ROOT/app"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml -f $APP_DIR/docker-compose.prod.yml"

cd "$REPO_ROOT"

echo "==================================================================="
echo "  Deploy Balneario El Cóndor → $VERSION"
echo "==================================================================="

echo
echo "→ [1/6] Backup pre-deploy"
"$SCRIPT_DIR/backup.sh"

echo
echo "→ [2/6] Pulling code"
git fetch --tags --quiet
if [ "$VERSION" != "latest" ]; then
    git checkout "$VERSION"
else
    git pull --ff-only
fi
echo "   en: $(git rev-parse --short HEAD) ($(git describe --tags 2>/dev/null || echo 'sin tag'))"

echo
echo "→ [3/6] Build app image (target=prod)"
APP_VERSION="$VERSION" $COMPOSE build app

echo
echo "→ [4/6] Migrate (con app temporal)"
APP_VERSION="$VERSION" $COMPOSE run --rm app php artisan migrate --force --no-interaction

echo
echo "→ [5/6] Swap containers"
APP_VERSION="$VERSION" $COMPOSE up -d --no-deps --force-recreate app worker scheduler nginx

echo
echo "→ [6/6] Smoke (10s grace)"
sleep 10
"$SCRIPT_DIR/smoke.sh"

echo
echo "==================================================================="
echo "  ✓ Deploy $VERSION OK"
echo "==================================================================="
echo
echo "Rollback rápido si hace falta:"
echo "  ops/restore.sh /var/backups/balneario/<timestamp-pre-deploy>"
echo "  o: ops/deploy.sh <version-anterior>"
