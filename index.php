<?php

use Symfony\Component\ClassLoader\ClassLoader;
use Wren\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application();
$app->run();
