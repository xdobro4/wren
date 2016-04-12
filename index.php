<?php

use Symfony\Component\ClassLoader\ClassLoader;
use Wren\Application;

require __DIR__ . '/vendor/autoload.php';

$classLoader = new ClassLoader();
$classLoader->addPrefix('Wren', __DIR__ . '/app');
$classLoader->setUseIncludePath(TRUE);
$classLoader->register();

$app = new Application();
$app->run();
