<?php

return [
    'default' => env('CACHE_STORE', 'file'),
    'stores' => [
        'array' => ['driver' => 'array', 'serialize' => false],
        'file' => ['driver' => 'file', 'path' => storage_path('framework/cache/data'), 'lock_path' => storage_path('framework/cache/data')],
        'database' => ['driver' => 'database', 'connection' => env('DB_CACHE_CONNECTION'), 'table' => env('DB_CACHE_TABLE', 'cache'), 'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'), 'lock_table' => env('DB_CACHE_LOCK_TABLE', 'cache_locks')],
        'null' => ['driver' => 'null'],
    ],
    'prefix' => env('CACHE_PREFIX', 'faculty_ai_cache'),
];
