<?php

use LunoxHoshizaki\Application;

define('LUNOX_START', microtime(true));

// Register the auto-loader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the application
$app = new Application(
    realpath(__DIR__.'/../')
);

// Handle the incoming request
$app->handleRequest();
