<?php

use App\Http\Middleware\OptionalSanctum;
use App\Http\Middleware\SetLocaleFromHeader;
use App\Http\Middleware\StripUserApiLocaleNameFields;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxy headers (e.g. Hostinger) so HTTPS and correct host are detected
        $middleware->trustProxies(at: '*');
        $middleware->alias(['optional.sanctum' => OptionalSanctum::class]);
        $middleware->prependToGroup('api', [SetLocaleFromHeader::class]);
        $middleware->appendToGroup('api', [StripUserApiLocaleNameFields::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render all API exceptions as consistent JSON.
        // Returning null defers to the default handler (e.g. for Filament web routes).
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            return match (true) {
                // 422: validation failures (FormRequest or manual $request->validate())
                $e instanceof ValidationException => response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422),

                // 401: missing or invalid Sanctum token
                $e instanceof AuthenticationException => response()->json([
                    'message' => 'Unauthenticated.',
                ], 401),

                // 403: Gate / Policy denial (future policy layer)
                $e instanceof AuthorizationException => response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403),

                // 404: model binding failure or explicit abort(404)
                $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => response()->json([
                    'message' => 'Not found.',
                ], 404),

                // All other HTTP exceptions: abort(403), abort(429), throttle, etc.
                // The message from abort() is passed through; falls back to 'Error.'
                // when abort() was called without a message.
                $e instanceof HttpException => response()->json([
                    'message' => $e->getMessage() ?: 'Error.',
                ], $e->getStatusCode()),

                // 500: unexpected exceptions.
                // Expose the real message only in debug mode to avoid leaking internals.
                default => response()->json([
                    'message' => app()->hasDebugModeEnabled()
                        ? $e->getMessage()
                        : 'Server Error.',
                ], 500),
            };
        });
    })
    ->create();
