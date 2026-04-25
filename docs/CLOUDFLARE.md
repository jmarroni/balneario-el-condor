# Cloudflare setup

CDN gratis + DDoS mitigation + cache para assets. 5 minutos de setup.

## 1. Cuenta y dominio

1. Crear cuenta en https://dash.cloudflare.com (free).
2. "Add a site" → poner `elcondor.gob.ar`.
3. Cloudflare escanea los DNS records actuales — copiá los que detectó.
4. Plan free → continuar.
5. Cambiar nameservers en el registrador del dominio a los que Cloudflare indica
   (ej. `noah.ns.cloudflare.com`, `cassidy.ns.cloudflare.com`).
6. Esperar propagación (5 min - 24 hs).

## 2. SSL/TLS

Cloudflare → SSL/TLS → Overview → modo **Full (strict)**.

Esto requiere que el VPS tenga un cert válido (Let's Encrypt via Traefik o Caddy).
Si todavía no lo tenés, dejar en "Full" (acepta self-signed) y subir a "strict"
después de configurar el cert.

Activar:
- ✅ Always Use HTTPS
- ✅ HTTP Strict Transport Security (HSTS) — `max-age=31536000`
- ✅ Minimum TLS Version: TLS 1.2

## 3. Caching

Cloudflare → Caching → Configuration:
- Browser Cache TTL: **4 hours** (defecto razonable)
- Caching Level: **Standard**
- Always Online: **On** (sirve copia cuando el origin cae)

## 4. Page Rules (gratuito: 3 reglas)

Cloudflare → Rules → Page Rules:

| URL pattern | Settings |
|---|---|
| `*elcondor.gob.ar/build/*` | Cache Level = Cache Everything, Edge Cache TTL = a month, Browser Cache TTL = a year |
| `*elcondor.gob.ar/admin/*` | Cache Level = Bypass, Disable Performance |
| `*elcondor.gob.ar/api/*` | Cache Level = Bypass, Disable Performance |

(El sitio público se cachea con los headers Cache-Control que ya manda nginx,
no requiere page rule.)

## 5. Security

Cloudflare → Security → Settings:
- Security Level: **Medium**
- Bot Fight Mode: **On** (free)
- Challenge Passage: 30 minutes
- Browser Integrity Check: **On**

Cloudflare → Security → WAF (Managed Rules en plan Pro+, no incluido en free).

## 6. Speed

Cloudflare → Speed → Optimization:
- Auto Minify: **JS / CSS / HTML** ON
- Brotli: **On**
- Early Hints: **On**

## 7. nginx + Laravel ya configurados

El stack ya está listo para Cloudflare:

- `app/docker/nginx/snippets/cloudflare-real-ip.conf` propaga `CF-Connecting-IP` a `$remote_addr`. Activado por default en `prod.conf`.
- `app/bootstrap/app.php` usa `TRUSTED_PROXIES=*` para que Laravel respete los headers `X-Forwarded-*`.
- Cache headers en nginx:
  - `/build/*` → `public, max-age=31536000, immutable` (1 año, immutable por hash de Vite)
  - `/storage/*` → `public, max-age=2592000` (30 días)
  - HTML → `no-cache` por default (Cloudflare lo cachea via page rule)

## 8. Verificación

Después del cambio de nameservers:

```bash
# Verificar que CF responde
curl -sI https://elcondor.gob.ar | grep -i 'server\|cf-ray'
# Esperado: Server: cloudflare + CF-Ray: <id>

# Verificar real IP en Laravel
ssh prod 'docker compose -f /opt/balneario/app/docker-compose.yml -f /opt/balneario/app/docker-compose.prod.yml logs nginx --tail 1'
# Debe mostrar la IP del cliente real, no la de Cloudflare
```

## 9. Cache purge en deploy

Después de un deploy con cambios visuales, purgar el cache CF:

### Manual (web)
Cloudflare → Caching → Configuration → Purge Cache → Purge Everything

### Automático (API)

Agregar al final de `ops/deploy.sh`:

```bash
if [ -n "${CLOUDFLARE_ZONE_ID:-}" ] && [ -n "${CLOUDFLARE_API_TOKEN:-}" ]; then
    echo "→ Purgando cache Cloudflare"
    curl -sX POST "https://api.cloudflare.com/client/v4/zones/${CLOUDFLARE_ZONE_ID}/purge_cache" \
         -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
         -H "Content-Type: application/json" \
         -d '{"purge_everything":true}' > /dev/null
fi
```

Y configurar las env vars en `.env.production`. El token se crea en
Cloudflare → My Profile → API Tokens con permiso `Zone.Cache Purge`.

## 10. Costos

- Plan free: ilimitado tráfico, CDN, DDoS protection básico, 3 page rules.
- Plan Pro ($20/mes): WAF managed rules, 20 page rules, image optimization.

Para un sitio municipal, **plan free alcanza** holgadamente.

## Troubleshooting

### El sitio no carga después del cambio DNS

Verificar:
```bash
dig elcondor.gob.ar NS
# Debe apuntar a los NS de Cloudflare
```

Si todavía apunta al viejo, esperar propagación (hasta 24 hs en peor caso).

### "Too many redirects"

SSL mode "Flexible" en Cloudflare + Laravel forzando HTTPS = loop. Cambiar a
SSL mode "Full" o "Full (strict)".

### Las imágenes no cargan

Verificar que el path `/build/*` o `/storage/*` no esté siendo bloqueado por
Cloudflare. Caching → Configuration → Development Mode (3 hs) para temporariamente
bypassear cache.

### Real IP no llega a Laravel

```bash
docker compose exec app php artisan tinker --execute="dump(request()->ip());"
```

Si devuelve la IP de Cloudflare, verificar:
1. `cloudflare-real-ip.conf` está incluído en `prod.conf` ✓ (default)
2. `TRUSTED_PROXIES=*` en `.env.production` ✓ (default)
3. nginx restartó después del cambio: `docker compose restart nginx`
