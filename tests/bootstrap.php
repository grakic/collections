<?php

error_reporting(E_ALL);

$autoloadFile = __DIR__.'/../vendor/autoload.php';

if (!is_file($autoloadFile)) {
    echo 'Could not find "vendor/autoload.php". Run "composer install --dev"?'.PHP_EOL;
    exit(1);
}

$loader = require $autoloadFile;
$loader->add('Devbase', __DIR__);
