<?php

return [
    'siga' => [
        'driver' => env('INTEGRATION_SIGA_DRIVER', 'simulator'),
        'api' => [
            'base_url' => env('SIGA_API_BASE_URL'),
            'token' => env('SIGA_API_TOKEN'),
            'timeout' => (int) env('SIGA_API_TIMEOUT', 30),
            'retries' => (int) env('SIGA_API_RETRIES', 3),
        ],
        'fixtures_path' => env('SIGA_FIXTURES_PATH', database_path('fixtures/integrations/siga')),
    ],
    'siaf' => [
        'driver' => env('INTEGRATION_SIAF_DRIVER', 'simulator'),
        'api' => [
            'base_url' => env('SIAF_API_BASE_URL'),
            'token' => env('SIAF_API_TOKEN'),
            'timeout' => (int) env('SIAF_API_TIMEOUT', 30),
            'retries' => (int) env('SIAF_API_RETRIES', 3),
        ],
        'fixtures_path' => env('SIAF_FIXTURES_PATH', database_path('fixtures/integrations/siaf')),
    ],
    'schedule' => [
        'siga_patrimonio' => env('INTEGRATION_SIGA_PATRIMONIO_TIME', '02:00'),
        'siga_organigrama' => env('INTEGRATION_SIGA_ORGANIGRAMA_TIME', '02:15'),
        'siaf_ejecucion' => env('INTEGRATION_SIAF_EJECUCION_TIME', '03:00'),
    ],
];
