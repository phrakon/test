<?php

require __DIR__ . '/../vendor/autoload.php';

error_reporting(-1);

$config = [];

$app = new \core\Application($config);

$app->run();
