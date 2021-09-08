<?php

namespace App\Model;

use App\Model;

class UserModel extends Model {
    public function collection(int $offset = 0,int $limit = 10): array|null {
        $q = "select id, username, email, created_at, updated_at from user limit $offset, $limit";
        $stmt = $this->pdo->prepare( $q );
        if( $stmt->execute() ){
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return null;
    }
    public function create(object|bool|array|null $getParsedBody): array|null {
        if( is_array( $getParsedBody ) ) {
            $q = "insert into user (username,email,password,created_at) values (:username,:email,:password,:created_at)";
            $stmt = $this->pdo->prepare( $q );
            $data = $getParsedBody;
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
            if($stmt->execute()){
                $data['updated_at'] = null;
                $data['id'] = $this->pdo->lastInsertId();
                ksort($data, SORT_FLAG_CASE);
                return $data;
            }
        }
        return null;
    }

    public function destroy(int $id): bool {
        $q = "delete from user where id = :id";
        $stmt = $this->pdo->prepare( $q );
        $stmt->bindValue( ':id', $id, \PDO::PARAM_INT );
        return $stmt->execute();
    }

    public function read(int $id): array|null {
        $q = "select id, username, email, created_at, updated_at from user where id = $id";
        $stmt = $this->pdo->prepare( $q );
        if( $stmt->execute() ){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function udpate(int $id, object|bool|array|null $getParsedBody): array|null {
        if($id && is_array($getParsedBody)){
            $q = "update user set username = :username, email = :email, updated_at = :updated_at where id = $id";
            $stmt = $this->pdo->prepare( $q );
            $data = $getParsedBody;
            $data['updated_at'] = ( new \DateTime() )->format( config( 'format.datetime' ) );
            foreach( $data as $key => $value ) {
                match ( $key ) {
                    'username' => $stmt->bindValue( ':username', $value, \PDO::PARAM_STR ),
                    'email' => $stmt->bindValue( ':email', $value, \PDO::PARAM_STR ),
                    'updated_at' => $stmt->bindValue( ':updated_at', $value, \PDO::PARAM_STR ),
                };
            }
            if($stmt->execute()){
                return $this->read($id);
            }
        }
        return null;
    }
}
