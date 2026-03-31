<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    private const ALLOWED = ['ar', 'en'];

    /**
     * Set app locale from Accept-Language header.
     * Only allows "ar" or "en"; defaults to "en" if missing or unsupported.
     * Parses the first preferred language (comma-separated list) and its primary subtag.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->parseAcceptLanguage($request->header('Accept-Language'));

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Parse Accept-Language: use first preferred tag, then primary subtag (e.g. "en" from "en-US").
     * Ensures "en", "en-US", "en-GB" → en and "ar", "ar-SA" → ar; defaults to "en" when invalid/missing.
     */
    private function parseAcceptLanguage(mixed $value): string
    {
        $candidates = is_array($value) ? $value : ($value !== null ? [$value] : []);

        foreach ($candidates as $raw) {
            $str = trim((string) $raw);
            if ($str === '') {
                continue;
            }
            // First preferred language (before comma); explode always produces at least one element.
            $first = trim(explode(',', $str)[0]);
            if ($first === '') {
                continue;
            }
            // Primary subtag (before hyphen or semicolon, e.g. "en" from "en-US" or "en;q=0.9")
            $tag = preg_replace('/[-;].*$/s', '', $first);
            $tag = strtolower(trim($tag));
            $parsed = substr($tag, 0, 2);
            if (in_array($parsed, self::ALLOWED, true)) {
                return $parsed;
            }
        }

        return 'en';
    }
}
