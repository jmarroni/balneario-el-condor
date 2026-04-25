#!/usr/bin/env bash
# Smoke test prod: verifica que las rutas críticas responden con el código esperado.
# Lee APP_URL de app/.env.production.
# Uso: ops/smoke.sh [base-url]

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
ENV_FILE="$APP_DIR/.env.production"

if [ -n "${1:-}" ]; then
    URL="$1"
elif [ -f "$ENV_FILE" ]; then
    URL=$(grep -E '^APP_URL=' "$ENV_FILE" | head -1 | cut -d'=' -f2- | tr -d '"' | tr -d "'")
fi
URL="${URL:-https://elcondor.gob.ar}"

echo "→ Smoke contra $URL"
FAILS=0

check() {
    local path="$1"
    local expected="$2"
    local code
    code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 10 "${URL}${path}" || echo 000)
    if [ "$code" = "$expected" ]; then
        printf "  ✓ %-30s → %s\n" "$path" "$code"
    else
        printf "  ✗ %-30s → %s (esperaba %s)\n" "$path" "$code" "$expected" >&2
        FAILS=$((FAILS + 1))
    fi
}

# Healthcheck
check "/up" 200

# Sitio público
check "/" 200
check "/novedades" 200
check "/eventos" 200
check "/hospedajes" 200
check "/gastronomia" 200
check "/alquileres" 200
check "/clasificados" 200
check "/galeria" 200
check "/recetas" 200
check "/mareas" 200
check "/clima" 200
check "/contacto" 200
check "/newsletter" 200

# SEO
check "/sitemap.xml" 200
check "/robots.txt" 200

# Admin (sin auth → redirect a login)
check "/admin" 302
check "/login" 200

# API privada (sin auth → 401 con Accept JSON; sin Accept → 302/401)
api_code=$(curl -sk -o /dev/null -w "%{http_code}" -H "Accept: application/json" --max-time 10 "${URL}/api/v1/me" || echo 000)
if [ "$api_code" = "401" ]; then
    printf "  ✓ %-30s → %s\n" "/api/v1/me (no-auth)" "$api_code"
else
    printf "  ✗ %-30s → %s (esperaba 401)\n" "/api/v1/me (no-auth)" "$api_code" >&2
    FAILS=$((FAILS + 1))
fi

# Public contact (POST con throttle)
contact_code=$(curl -sk -o /dev/null -w "%{http_code}" -X GET --max-time 10 "${URL}/api/v1/contact" || echo 000)
if [ "$contact_code" = "405" ] || [ "$contact_code" = "404" ]; then
    printf "  ✓ %-30s → %s\n" "/api/v1/contact (GET)" "$contact_code"
else
    printf "  ✗ %-30s → %s (esperaba 405 o 404)\n" "/api/v1/contact (GET)" "$contact_code" >&2
    FAILS=$((FAILS + 1))
fi

# Docs (en prod sin SCRIBE_DOCS_ENABLED → 404)
docs_code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 10 "${URL}/docs" || echo 000)
echo "  ℹ  /docs → $docs_code (200 si SCRIBE_DOCS_ENABLED=true, 404 si no)"

echo
if [ "$FAILS" -gt 0 ]; then
    echo "✗ $FAILS smoke check(s) fallaron" >&2
    exit 1
fi
echo "✓ Todo verde"
