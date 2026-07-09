<?php

return [
    'client' => [
        'input' => env('BRIDGE_OPENAPI_INPUT', 'http://localhost:8000/docs/api.json'),
        'output' => env('BRIDGE_CLIENT_OUTPUT', resource_path('js/client')),
    ],
];
