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
        'resource' => 'App\\Http\\Resources',
        'model' => 'App\\Models',
        'tests' => 'Tests\\Feature',
        'service' => 'App\\Services\\Search',
        'factory' => 'Database\\Factories',
    ],

    /*
    |--------------------------------------------------------------------------
    | The default path the crud generator use.
    |--------------------------------------------------------------------------
    |
    | There might be instances that you wanted to change the views & layouts path 
    | for the generated scaffold files. You can change it here.
    |
    */
    'path' => [
        'controller' => 'app/Http/Controllers/',
        'view' => 'resources/views/',
        'layout' => 'resources/views/layouts/',
    ],
    'blade' => [
        'layout' => 'layouts.crudwizard'
    ],
    'file_name' => [
        'layout' => 'crudwizard',
    ]
];
