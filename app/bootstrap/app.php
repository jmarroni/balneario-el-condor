<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'force.password.reset' => \App\Http\Middleware\ForcePasswordReset::class,
            'restrict.docs' => \App\Http\Middleware\RestrictDocsInProd::class,
            'require.2fa.admin' => \App\Http\Middleware\RequireTwoFactorForAdmin::class,
        ]);

        // Confiar en proxies (Cloudflare/Traefik) — lee TRUSTED_PROXIES de env.
        // En prod típico: TRUSTED_PROXIES=* (todo upstream confiable porque
        // CF/Traefik ya filtran). Default Laravel sin trust = lee solo direct peer.
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES', '*'),
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        \Sentry\Laravel\Integration::handles($exceptions);
    })->create();
