<?php

chdir(__DIR__.'/..');

include 'vendor/autoload.php';

$container = include 'config/container.php';


$shutdown = false;

declare(ticks=1);
pcntl_signal(SIGINT, function() use (&$shutdown) {
   $shutdown = true;
});

$newPictureHandler = function(array $picture) use (&$shutdown) {
    echo 'Added: ' . $picture['title'] . PHP_EOL;

    if ($shutdown) {
        die;
    }
};

$errorHandler = function(Exception $exception) use (&$shutdown) {
    echo (string) $exception . PHP_EOL;

    if ($shutdown) {
        die;
    }
};

$container->get(AndrewCarterUK\APOD\APIInterface::class)->updateStore(20, $newPictureHandler, $errorHandler);
