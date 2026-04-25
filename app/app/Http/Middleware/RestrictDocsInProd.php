<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate /docs in production.
 *
 * - Non-production: passthrough.
 * - Production + docs disabled: 404.
 * - Production + docs enabled + basic auth configured: require valid credentials (timing-safe).
 * - Production + docs enabled + no basic auth configured: serve freely.
 */
class RestrictDocsInProd
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use config('app.env') for testability — Config::set('app.env', 'production')
        // makes this branch reachable from tests without rebuilding the application.
        if (config('app.env') !== 'production') {
            return $next($request);
        }

        if (! config('scribe.docs_enabled', false)) {
            abort(404);
        }

        $user = config('scribe.docs_basic_auth_user');
        $pass = config('scribe.docs_basic_auth_pass');

        if ($user !== null && $pass !== null && $user !== '' && $pass !== '') {
            $providedUser = (string) $request->getUser();
            $providedPass = (string) $request->getPassword();

            $userOk = hash_equals((string) $user, $providedUser);
            $passOk = hash_equals((string) $pass, $providedPass);

            if (! $userOk || ! $passOk) {
                return response('Unauthorized', 401, [
                    'WWW-Authenticate' => 'Basic realm="API Docs"',
                ]);
            }
        }

        return $next($request);
    }
}
