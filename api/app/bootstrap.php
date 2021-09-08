<?php

use App\Controller\UserController;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';


function slugify($urlString): array|string|null {
    $search = array('Ș', 'Ț', 'ş', 'ţ', 'Ş', 'Ţ', 'ș', 'ț', 'î', 'â', 'ă', 'Î', ' ', 'Ă', 'ë', 'Ë');
    $replace = array('s', 't', 's', 't', 's', 't', 's', 't', 'i', 'a', 'a', 'i', 'a', 'a', 'e', 'E');
    $str = str_ireplace($search, $replace, strtolower(trim($urlString)));
    $str = preg_replace('/[^\w\d\-\ ]/', '', $str);
    $str = str_replace(' ', '-', $str);
    return preg_replace('/\-{2,}', '-', $str);
}



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
