<?php

namespace API\Controller;

use API\Model\UserModel;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends \API\Controller {
    public function collection(Request $request, Response $response): Response {
        //TODO;
        return $response;
    }

    public function create(Request $request, Response $response): Response {
        $m = new UserModel();
        $user = $m->create( $request->getParsedBody() );

        return $response
            ->withStatus(201)
            ->withJson( $user );
    }

    public function destroy(Request $request, Response $response): Response {
        //TODO;
        return $response;
    }

    public function read(Request $request, Response $response): Response {
        //TODO;
        return $response;
    }

    public function update(Request $request, Response $response): Response {
        //TODO;
        return $response;
    }
}
