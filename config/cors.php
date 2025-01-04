<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],  // Sesuaikan jika Anda menggunakan rute selain 'api/*'

    'allowed_methods' => ['*'],  // Mengizinkan semua metode HTTP (GET, POST, dll.)

    'allowed_origins' => ['http://localhost:3000'],  // Menambahkan origin React frontend Anda

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],  // Mengizinkan semua header

    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, 
];
