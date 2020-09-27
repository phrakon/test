<?php

require __DIR__ . '/../vendor/autoload.php';

error_reporting(-1);

$config = require __DIR__ . '/../config/local.php';

$app = new \core\Application($config);

$app->run();
