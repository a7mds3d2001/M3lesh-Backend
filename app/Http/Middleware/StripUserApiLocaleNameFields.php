<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StripUserApiLocaleNameFields
{
    private const STRIP_KEYS = ['name_ar', 'name_en', 'title_ar', 'title_en', 'content_ar', 'content_en'];

    /**
     * For GET /api/user/*: remove name_ar, name_en and title_ar, title_en, content_ar, content_en from JSON response (locale-based name/title/content only).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $request->is('api/user*')) {
            $response = $this->stripKeysFromJsonResponse($response);
        }

        return $response;
    }

    private function stripKeysFromJsonResponse(Response $response): Response
    {
        $content = $response->getContent();
        if ($content === false || $content === '') {
            return $response;
        }

        $data = json_decode($content, true);
        if (! is_array($data)) {
            return $response;
        }

        $this->stripKeysRecursive($data);

        $response->setContent(json_encode($data));

        return $response;
    }

    /**
     * @param  array<mixed>  $arr
     */
    private function stripKeysRecursive(array &$arr): void
    {
        foreach (self::STRIP_KEYS as $key) {
            unset($arr[$key]);
        }
        foreach ($arr as &$value) {
            if (is_array($value)) {
                $this->stripKeysRecursive($value);
            }
        }
    }
}
