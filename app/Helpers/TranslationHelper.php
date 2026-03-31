<?php

if (! function_exists('trans_both')) {
    /**
     * Get translation in both English and Arabic
     */
    function trans_both(string $key, array $replace = []): array
    {
        $originalLocale = app()->getLocale();

        app()->setLocale('en');
        $en = trans($key, $replace);

        app()->setLocale('ar');
        $ar = trans($key, $replace);

        app()->setLocale($originalLocale);

        return [
            'en' => $en,
            'ar' => $ar,
        ];
    }
}
