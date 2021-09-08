<?php

namespace API\Controller;

use API\Model\UserModel;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends \API\Controller {
    public function collection(Request $request, Response $response): Response {
        $result = ( new UserModel() )->collection(
            offset: $request->getParam( 'offset', 0 ),
            limit: $request->getParam( 'limit', config( 'api.collection.limit' ) ),
        );
        return match ( gettype( $result ) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function create(Request $request, Response $response): Response {
        $result = (new UserModel())->create( $request->getParsedBody() );
        return match ( gettype( $result ) ) {
            'array' => $response->withStatus( 201 )->withJson( $result ),
            'NULL' => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function destroy(Request $request, Response $response, array $params): Response {
        $result = (new UserModel())->destroy((int) $params['id']);
        return match ( $result ) {
            true => $response->withStatus( 204 )->withJson( '' ),
            false => $response->withStatus( 501 )->withJson( [ 'error' => 'Unhandled exception' ] ),
        };
    }

    public function read(Request $request, Response $response, array $params): Response {
        $result = (new UserModel())->read((int) $params['id']);
        return match ( gettype($result) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 404 )->withJson( [ 'error' => 'Not Found' ] ),
        };
    }

    public function update(Request $request, Response $response, array $params): Response {
        $result = (new UserModel())->udpate((int) $params['id'], $request->getParsedBody());
        return match ( gettype($result) ) {
            'array' => $response->withStatus( 200 )->withJson( $result ),
            'NULL' => $response->withStatus( 404 )->withJson( [ 'error' => 'Not Found' ] ),
        };
    }
}
