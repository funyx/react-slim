<?php

use App\Controller\AuthController;
use App\Controller\UserController;
use App\Model\UserModel;
use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';



$app = new App( $config[ 'api' ] );

$app->add(new JwtAuthentication([
    'path' => '/api', /* or ["/api", "/admin"] */
    'ignore' => [
        '/api/auth/login',
        '/api/auth/register'
    ],
    'attribute' => 'jwt',
    'secret' => $config['api']['jwt']['secret'],
    'algorithm' => $config['api']['jwt']['algorithm'],
    'error' => function ($response, $arguments) {
        $data['status'] = 'error';
        $data['message'] = $arguments['message'];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->withStatus(401)
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));

$c = $app->getContainer();

$c['pdo'] = function() {
    $db_env = config( 'db.default_environment' );
    $db = config( 'db.' . $db_env );
    $pdo = new PDO( sprintf( '%s:host=%s;dbname=%s;charset=%s', $db['adapter'], $db['host'], $db['name'], $db['charset'] ), $db['user'], $db['pass'] );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$c['UserController'] = fn($c) => new UserController( $c );
$c['AuthController'] = fn($c) => new AuthController( $c );
//$app->get('/', function() {
//    $m = new UserModel();
//});
$app->group( '/api', function($app) {
    // auth routes
    $app->group( '/auth', function($app) {
        $app->post( '/login', 'AuthController:login' );
        $app->post( '/register', 'AuthController:register' );
        $app->get( '/me', 'AuthController:me' );
    });
    // user routes
    $app->get( '/user', 'UserController:collection' );
    $app->get( '/user/{uuid:[a-zA-Z0-9\-]+}', 'UserController:read' );
    $app->post( '/user', 'UserController:create' );
    $app->put( '/user/{uuid:[a-zA-Z0-9\-]+}', 'UserController:update' );
    $app->delete( '/user/{uuid:[a-zA-Z0-9\-]+}', 'UserController:destroy' );
} );

return $app;
