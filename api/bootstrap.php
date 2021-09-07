<?php
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

$app = new App( config('api.settings'));
$container = $app->getContainer();
