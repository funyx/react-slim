<?php

namespace App\Controller;

use App\Controller;
use App\Model\UserModel;
use App\Service\JWTService;
use Slim\Http\Request;
use Slim\Http\Response;


class AuthController extends Controller {
    public function login(Request $request, Response $response): Response {
        $m = new UserModel();
        $data = $request->getParsedBody();
        $data['password'] = $data['password'] ?? null;
        $data['email'] = $data['email'] ?? null;
        if(count($errors = $m->validate($data, 'login'))){
            return $response->withStatus( 400 )->withJson( ['errors' => $errors] );
        }

        $result = $m->login( $data );

        if( $result ) {
            $result = ( new JWTService() )->addToken( $result );
        }

        return match ( gettype( $result ) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function register(Request $request, Response $response): Response {
        $m = new UserModel();
        $data = $request->getParsedBody();
        $data['password'] = $data['password'] ?? null;
        $data['username'] = $data['username'] ?? null;
        $data['email'] = $data['email'] ?? null;
        $data['role_uid'] = $m->default_role;
        if(count($errors = $m->validate($data))){
            return $response->withStatus( 400 )->withJson( ['errors' => $errors] );
        }

        $result = $m->create();

        if( $result ) $result = ( new JWTService() )->addToken( $result );

        return match ( gettype( $result ) ) {
            'array' => $response->withStatus( 201 )->withJson( $result ),
            'NULL' => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function me(Request $request, Response $response): Response {
        $jwt_user = $request->getAttribute('jwt');
        $result = (new UserModel())->loadBy('uuid', $jwt_user['jid'])->publicFields();
        return match ( gettype($result) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 404 )->withJson( [ 'error' => 'Not Found' ] ),
        };
    }
}
