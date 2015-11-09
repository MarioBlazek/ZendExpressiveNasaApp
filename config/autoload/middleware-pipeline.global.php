<?php

return [
    'dependencies' => [
        'factories' => [
            App\Middleware\CacheMiddleware::class => App\Middleware\CacheFactory::class,
        ],
    ],
    // This can be used to seed pre- and/or post-routing middleware
    'middleware_pipeline' => [
        // An array of middleware to register prior to registration of the
        // routing middleware
        'pre_routing' => [
//            [ 'middleware' => App\Middleware\CacheMiddleware::class ],
        ],

        // An array of middleware to register after registration of the
        // routing middleware
        'post_routing' => [

        ],
    ],
];
