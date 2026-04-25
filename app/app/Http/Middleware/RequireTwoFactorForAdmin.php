<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si el usuario autenticado tiene rol "admin" y no confirmó 2FA,
 * redirige a la página de setup. Permite el flujo necesario para activarlo.
 *
 * Editor y moderator pueden usar 2FA opcionalmente, no se les exige.
 */
class RequireTwoFactorForAdmin
{
    /**
     * Nombres de rutas que se permiten siempre para que el admin pueda
     * activar 2FA, ver el dashboard mínimo y cerrar sesión.
     *
     * @var list<string>
     */
    private const ALLOWED_ROUTE_NAMES = [
        'admin.two-factor.show',
        'logout',
        'password.confirm',
        'password.confirm.store',
    ];

    /**
     * Prefijos de path (rutas Fortify) que también se permiten para el setup.
     *
     * @var list<string>
     */
    private const ALLOWED_PATH_PREFIXES = [
        'user/two-factor-authentication',
        'user/confirmed-two-factor-authentication',
        'user/two-factor-recovery-codes',
        'user/two-factor-qr-code',
        'user/two-factor-secret-key',
        'user/confirm-password',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('admin') || $user->two_factor_confirmed_at !== null) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if ($routeName !== null && in_array($routeName, self::ALLOWED_ROUTE_NAMES, true)) {
            return $next($request);
        }

        $path = $request->path();
        foreach (self::ALLOWED_PATH_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }

        return redirect()
            ->route('admin.two-factor.show')
            ->with('warning', 'Tu cuenta de admin requiere 2FA activado para continuar.');
    }
}
