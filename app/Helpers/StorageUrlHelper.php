<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_public_url')) {
    /**
     * Public URL for a file on the public storage disk.
     * Uses current request host so it works locally (any port) and on server.
     * Falls back to configured APP_URL when not in an HTTP request (e.g. queue, console).
     */
    function storage_public_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $path = ltrim($path, '/');

        try {
            $host = request()->getHost();
            if (! empty($host)) {
                return rtrim(request()->getSchemeAndHttpHost(), '/').'/storage/'.$path;
            }
        } catch (\Throwable) {
            // Not in a request context (e.g. queue, artisan)
        }

        return Storage::disk('public')->url($path);
    }
}
