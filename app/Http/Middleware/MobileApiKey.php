<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gating ringan untuk API mobile read-only.
 * Aktif hanya bila MOBILE_API_KEY di-set (config services.mobile_api.key).
 * Selalu memaksa Accept: application/json agar error dirender sebagai JSON.
 */
class MobileApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        $expected = config('services.mobile_api.key');
        if (!empty($expected)) {
            $given = (string) $request->header('X-API-Key');
            if (!hash_equals((string) $expected, $given)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }

        return $next($request);
    }
}
