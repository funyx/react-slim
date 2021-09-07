<?php

use API\Controller\UserController;
use Slim\App;
use Slim\Container;

require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';



$app = new App( $config[ 'api' ] );

$c = $app->getContainer();

$c['pdo'] = function() {
    $db_env = config( 'db.default_environment' );
    $db = config( 'db.' . $db_env );

    return new PDO( sprintf( '%s:host=%s;dbname=%s;charset=%s', $db['adapter'], $db['host'], $db['name'], $db['charset'] ), $db['user'], $db['pass'] );
};

$c['UserController'] = fn($c) => new UserController( $c );

$app->group( '/api', function($app) {
    // user routes
    $app->get( '/user', 'UserController:collection' );
    $app->get( '/user/{id:[0-9]+}', 'UserController:read' );
    $app->post( '/user', 'UserController:create' );
    $app->put( '/user/{id:[0-9]+}', 'UserController:update' );
    $app->delete( '/user/{id:[0-9]+}', 'UserController:destroy' );
} );

return $app;
