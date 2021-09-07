<?php

namespace API\Model;

use API\Model;

class UserModel extends Model {
    private ?\PDO $pdo;

    public function __construct(\PDO $pdo = null) {
        if( is_null( $pdo ) ) {
            global $c;
            $pdo = $c['pdo'];
        }
        $this->pdo = $pdo;
    }

    public function create(object|bool|array|null $getParsedBody): array {
        $q = "insert into user (username,email,password,created_at) values (:username,:email,:password,:created_at)";
        $stmt = $this->pdo->prepare( $q );
        $data = $getParsedBody;
        if( is_array( $getParsedBody ) ) {
            if( isset( $data['password'] ) ) {
                $data['password'] = password_hash( $data['password'], PASSWORD_BCRYPT );
            }
            $data['created_at'] = ( new \DateTime() )->format( config( 'format.datetime' ) );
            foreach( $data as $key => $value ) {
                match ( $key ) {
                    'username' => $stmt->bindValue( ':username', $value, \PDO::PARAM_STR ),
                    'email' => $stmt->bindValue( ':email', $value, \PDO::PARAM_STR ),
                    'password' => $stmt->bindValue( ':password', $value, \PDO::PARAM_STR ),
                    'created_at' => $stmt->bindValue( ':created_at', $value, \PDO::PARAM_STR ),
                };
            }
            $stmt->execute();
            $data['updated_at'] = null;
            $data['id'] = $this->pdo->lastInsertId();
            ksort($data, SORT_FLAG_CASE);
        }

        return $data;
    }
}
