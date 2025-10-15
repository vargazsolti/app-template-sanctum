<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'spa/login',
        'spa/logout',
    ],
    'allowed_methods' => ['*'],
  'allowed_origins' => [
    'http://app.test','https://app.test',
    'http://ui.app.test','https://ui.app.test',
    'http://ui.app.test:5173','https://ui.app.test:5173',
],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
