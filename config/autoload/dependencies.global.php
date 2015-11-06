<?php

return [
    'dependencies' => [
        'factories' => [
            Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
            Doctrine\Common\Cache\Cache::class => App\DoctrineCacheFactory::class,
        ]
    ],
    'application' => [
        'cache_path' => 'data/doctrine-cache/',
    ],
];
