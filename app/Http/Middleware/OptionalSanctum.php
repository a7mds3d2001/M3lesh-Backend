<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OptionalSanctum
{
    /**
     * If a Bearer token is present, resolve the user via Sanctum and set it on the request
     * so that $request->user('sanctum') works in the controller/FormRequest.
     * Never aborts; allows the request to continue as guest when no or invalid token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->bearerToken()) {
            return $next($request);
        }

        $guard = Auth::guard('sanctum');
        if (method_exists($guard, 'setRequest')) {
            $guard->setRequest($request);
        }
        // Resolve and cache the user so $request->user('sanctum') works in controller/FormRequest
        $guard->user();

        return $next($request);
    }
}
