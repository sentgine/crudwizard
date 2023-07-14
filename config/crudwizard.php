<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The default namespaces the crud generator use.
    |--------------------------------------------------------------------------
    |
    | There might be instances that you wanted to change the namespaces 
    | for the generated scaffold files. You can change it here.
    |
    */
    'namespace' => [
        'controller' => 'App\\Http\\Controllers',
        'controller_api' => 'App\\Http\\Controllers\\Api',
        'request' => 'App\\Http\\Requests',
        'model' => 'App\\Models',
        'tests' => 'Tests\\Feature',
        'service' => 'App\\Services\\Search',
        'factory' => 'Database\\Factories',
    ],
    'path' => [
        'view' => 'resources/views/',
        'layout' => 'resources/views/layouts/',
    ]
];
