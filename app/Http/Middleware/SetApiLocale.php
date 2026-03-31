<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    private const ALLOWED = ['ar', 'en'];

    /**
     * Set app locale from header (Accept-Language or Lang).
     * Applied to all API routes; responses (e.g. name, display_name on models) follow this locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->applyLocaleFromRequest($request);

        return $next($request);
    }

    /**
     * Set app locale (ar|en). Default 'en'.
     * Only from request: Accept-Language, X-Locale, ?locale= (no server vars).
     */
    private function applyLocaleFromRequest(Request $request): void
    {
        $locale = $this->resolveLocaleValue($request) ?? 'en';

        app()->setLocale($locale);
    }

    private function resolveLocaleValue(Request $request): ?string
    {
        $acceptLanguage = $this->normalizeHeaderValue($request->header('Accept-Language'))
            ?? $this->acceptLanguageFromGetAllHeaders();

        $candidates = [
            $acceptLanguage,
            $this->normalizeHeaderValue($request->header('X-Locale')),
            $request->query('locale'),
        ];

        foreach ($candidates as $value) {
            if ($value !== null && $value !== '') {
                $parsed = $this->parseLocale((string) $value);
                if ($parsed !== null) {
                    return $parsed;
                }
            }
        }

        return null;
    }

    private function acceptLanguageFromGetAllHeaders(): ?string
    {
        if (! function_exists('getallheaders')) {
            return null;
        }
        $headers = getallheaders();
        if (! is_array($headers)) {
            return null;
        }
        foreach ($headers as $name => $value) {
            if (strtolower((string) $name) === 'accept-language' && $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }

    private function normalizeHeaderValue(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $value = is_array($raw) ? ($raw[0] ?? null) : $raw;

        return $value === null || $value === '' ? null : (string) $value;
    }

    private function parseLocale(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $parsed = strtolower(substr(trim($value), 0, 2));

        return in_array($parsed, self::ALLOWED, true) ? $parsed : null;
    }
}
