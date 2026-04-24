<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordReset
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_reset_password) {
            // Permitir acceso a las rutas de reset + logout
            $allowed = ['password.edit', 'password.update', 'logout'];
            if (! in_array($request->route()?->getName(), $allowed, true)) {
                return redirect()
                    ->route('password.edit')
                    ->with('warning', 'Debés cambiar tu contraseña antes de continuar.');
            }
        }

        return $next($request);
    }
}
