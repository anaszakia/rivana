<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hidrologi ML API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration untuk koneksi ke Python Hidrologi ML API Server
    |
    */

    'hidrologi' => [
        // API Base URL
        'api_url' => env('RIVANA_API_URL', 'http://localhost:8000'),
        
        // Bearer Token untuk Authentication
        'api_token' => env('RIVANA_API_TOKEN'),
        
        // Request Timeout (dalam detik)
        'timeout' => env('RIVANA_API_TIMEOUT', 300),
        
        // Enable/Disable Logging
        'enable_logging' => env('RIVANA_API_LOGGING', true),
        
        // Retry Configuration
        'retry_times' => env('RIVANA_API_RETRY', 3),
        'retry_sleep' => env('RIVANA_API_RETRY_SLEEP', 100), // milliseconds
    ],
];
