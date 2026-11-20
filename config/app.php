<?php

return [
    'name' => env('APP_NAME', 'Cantina La 35'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://127.0.0.1:8000'),
    'timezone' => env('APP_TIMEZONE', 'America/Argentina/Buenos_Aires'),
    'locale' => env('APP_LOCALE', 'es'),
    'fallback_locale' => 'en',
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
];
