<?php

namespace App\Controller;

use App\Controller;
use App\Model\UserModel;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller {
    public function collection(Request $request, Response $response): Response {
        $m = new UserModel();
        $result = $m->export($m->getPublicFields())->get();
        return match ( gettype( $result ) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function create(Request $request, Response $response): Response {
        $m = new UserModel();
        $data = $request->getParsedBody();
        if(count($errors = $m->validate($data))){
            return $response->withStatus( 400 )->withJson( ['errors' => $errors] );
        }
        $result = $m->create( );
        return match ( gettype( $result ) ) {
            'array' => $response->withStatus( 201 )->withJson( $result ),
            'NULL' => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function destroy(Request $request, Response $response, array $params): Response {
        $m = new UserModel();
        $m->loadBy('uuid', $params['uuid']);
        if(!$m->loaded()){
            return $response->withStatus( 400 )->withJson( ['errors' => 'Not found'] );
        }
        $result = $m->destroy($params['uuid']);
        return match ( $result ) {
            true => $response->withStatus( 204 )->withJson( '' ),
            false => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function read(Request $request, Response $response, array $params): Response {
        $m = (new UserModel());
        $result = $m->loadBy('uuid', $params['uuid'])->get();
        return match ( gettype($result) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 404 )->withJson( [ 'error' => 'Not Found' ] ),
        };
    }

    public function update(Request $request, Response $response, array $params): Response {
        $m = new UserModel();

        $data = $request->getParsedBody();
        if(count($errors = $m->validate($data))){
            return $response->withStatus( 400 )->withJson( ['errors' => $errors] );
        }

        $m->loadBy('uuid', $params['uuid']);
        if(!$m->loaded()){
            return $response->withStatus( 404 )->withJson( ['errors' => 'Not Found'] );
        }
        $m->update($data);

        //reload
        $result = $m->loadBy('uuid', $params['uuid'])->get();
        return match ( gettype($result) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 404 )->withJson( [ 'error' => 'Not Found' ] ),
        };
    }
}
