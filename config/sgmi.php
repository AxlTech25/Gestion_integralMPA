<?php

return [
    'ml' => [
        'service_url' => env('ML_SERVICE_URL'),
        'api_token' => env('ML_API_TOKEN'),
        'timeout' => (int) env('ML_TIMEOUT', 60),
        'umbrales' => [
            'verde' => (float) env('ML_UMBRAL_VERDE', 0.33),
            'amarillo' => (float) env('ML_UMBRAL_AMARILLO', 0.66),
        ],
        'modelo_version' => env('ML_MODEL_VERSION', 'v1.0.0'),
        'schedule_time' => env('ML_SCHEDULE_TIME', '02:00'),
    ],
];
