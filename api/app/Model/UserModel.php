<?php

namespace App\Model;

use App\Model;
use Respect\Validation\Rules;

class UserModel extends Model {
    public string $default_role = 'user';
    protected string $table = 'user';
    protected array $fillable_fields = [
        'uuid',
        'password',
        'username',
        'email',
        'role',
        'first_name',
        'last_name',
        'created_at',
        'updated_at'
    ];

    protected array $public_fields = [
        'uuid',
        'username',
        'email',
        'role',
        'first_name',
        'last_name',
        'created_at',
        'updated_at'
    ];

    public function create(array|null $data = null): array|null {
        if( !is_null( $data ) ) {
            $this->set( $data );
        }
        $this->insert();

        return $this->publicFields();
    }

    public function destroy(string $uuid): bool {
        $q = "delete from user where uuid = :uuid";
        $stmt = $this->pdo->prepare( $q );
        $stmt->bindValue( ':uuid', $uuid, \PDO::PARAM_STR );

        return $stmt->execute();
    }

    public function put(int $uuid, array $data): array|null {
        if( $uuid ) {
            $this->addCondition( 'uuid', '=', 2 );
            $this->update( $data );
            $this->loadAny();

            return $this->publicFields();
        }

        return null;
    }

    public function login(array $data) {
        if( isset( $data['password'] ) && isset( $data['email'] ) ) {
            $user = $this->loadBy( 'email', $data['email'] );
            if( $user->loaded() ) {
                $userdata = $user->get();
                if( password_verify( $data['password'], $userdata['password'] ) ) {
                    return $user->publicFields();
                }
            }
        }

        return null;
    }

    protected function fields(array $fields = []): array {
        return [
            'uuid'       => [
                'type'     => \PDO::PARAM_STR,
                'default'  => fn() => $this->uuid_v4(),
                'required' => true,
                'nullable' => false,
            ],
            'username'   => [
                'type'       => \PDO::PARAM_STR,
                'required'   => true,
                'nullable'   => false,
                'validation' => new Rules\AllOf(
                    new Rules\NotBlank(),
                    new Rules\Alnum('.','-','_'),
                    new Rules\NoWhitespace(),
                    new Rules\Length( 4, 30 ),
                    (new Rules\Call( function($input) {
                        $check = (new self())->loadBy('username', $input);
                        if($check->loaded() && $this->loaded()){
                            return $check->get()['id'] != $this->get()['id'];
                        }elseif($this->intent === 'login' && $check->loaded()){
                            return false;
                        }
                        return $check->loaded();
                    }, (new Rules\FalseVal())->setTemplate( 'username is already taken' ) )),
                )
            ],
            'password'   => [
                'type'       => \PDO::PARAM_STR,
                'required'   => true,
                'nullable'   => false,
                'cast'       => fn($v) => password_hash( $v, PASSWORD_BCRYPT ),
                'validation' => new Rules\AllOf(
                    new Rules\NotBlank(),
                    ( new Rules\Regex( '@[A-Z]@' ) )->setTemplate( '{{name}} must contain a capital letter' ),
                    ( new Rules\Regex( '@[a-z]@' ) )->setTemplate( '{{name}} must contain a lower-case letter' ),
                    ( new Rules\Regex( '@[0-9]@' ) )->setTemplate( '{{name}} must contain a number' ),
                    ( new Rules\Regex( '@[\W]@' ) )->setTemplate( '{{name}} must contain a special character' ),
                    new Rules\Length( 8 )
                )
            ],
            'email'      => [
                'type'       => \PDO::PARAM_STR,
                'required'   => true,
                'nullable'   => false,
                'validation' => new Rules\AllOf(
                    new Rules\NotBlank(),
                    new Rules\NotOptional(),
                    new Rules\Email(),
                    (new Rules\Call( function($input) {
                        $check = (new self())->loadBy('email', $input);
                        if($check->loaded() && $this->loaded()){
                            return $check->get()['id'] != $this->get()['id'];
                        }elseif($this->intent === 'login' && $check->loaded()){
                            return false;
                        }
                        return $check->loaded();
                    }, (new Rules\FalseVal())->setTemplate( 'email is already taken' ) )),
                )
            ],
            'role'       => [
                'type'   => 'Expression',
                'select' => '(select name from role where id = (select role_id from user_role where user_role.user_id = user.id))',
            ],
            'role_uid'   => [
                'type'        => 'Expression',
                'select'      => '(select uid from role where id = (select role_id from user_role where user_role.user_id = user.id))',
                'insert'      => 'insert into user_role (user_id, role_id, created_at) values ((select id from user where uuid = :uuid), (select id from role where uid = :role_uid), :created_at)',
                'insert_bind' => [ 'uuid', 'created_at' ],
                //'update'   => 'update user_role set role_id = (select id from role where uid = :role_uid), updated_at = :updated_at where user_id = :id ',
                //'update_bind'  => [ 'id', 'updated_at' ],
                'required'    => true,
                'nullable'    => false,
                'default'     => $this->default_role,
                'validation'  => new Rules\AllOf(
                    new Rules\NotOptional(),
                    new Rules\In( [ 'admin', 'user' ], true ),
                )
            ],
            'first_name' => [
                'type'       => \PDO::PARAM_STR,
                'validation' => new Rules\AllOf(
                    new Rules\Alpha()
                )
            ],
            'last_name'  => [
                'type'       => \PDO::PARAM_STR,
                'validation' => new Rules\AllOf(
                    new Rules\Alpha()
                )
            ],
        ];
    }
}
