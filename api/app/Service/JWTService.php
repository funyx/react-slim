<?php

namespace App\Service;

use Firebase\JWT\JWT;

class JWTService {

    public function addToken(array $user): array|null {
        if(isset($user['uuid']) ){
            $now = \strtotime( 'now' );
            $exp = \strtotime( config( 'api.jwt.exp' ), $now );
            $user['token'] = JWT::encode( [
                'iat'      => $now,
//                'exp'      => $exp,
                'jid'       => $user['uuid']
            ],
                config( 'api.jwt.secret', 'secret' ),
                config( 'api.jwt.algorithm', 'HS256' )
            );
            return $user;
        }
        return null;
    }
}
