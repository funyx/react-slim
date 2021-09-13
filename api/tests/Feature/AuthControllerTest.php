<?php

use App\Model\UserModel;
use App\Service\JWTService;
use function Pest\Faker\faker;

it('should not create user with empty data', function() {
    $r = post(
        uri: '/api/auth/register',
        data: []
    );
    expect( $r->getStatusCode() )->toBeScalar()->toEqual( 400 );
});
it('should not create user with only username set', function() {
    $r = post(
        uri: '/api/auth/register',
        data: [
            'username' => faker()->userName
        ]
    );
    expect( $r->getStatusCode() )->toBeScalar()->toEqual( 400 );
});
it('should not create user with only password set', function() {
    $r = post(
        uri: '/api/auth/register',
        data: [
            'password' => faker()->regexify( '[A-Za-z0-9]{20}' )
        ]
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(400);
});
it('should not create user with only email set', function() {
    $r = post(
        uri: '/api/auth/register',
        data: [
            'email' => faker()->email
        ]
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(400);
});

it('should crete user with username, email, password and return the db record with JWT', function() {
    $r = post(
        uri: '/api/auth/register',
        data: $dataset = [
            'username' => faker()->userName,
            'password' => 'test123A!aas',
            'email' => faker()->email,
        ]
    );

    expect($r->getStatusCode())->toBeScalar()->toEqual(201);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect($result)->toHaveKeys((new UserModel())->getPublicFields(['token']));
    expect($result['username'])->toEqual($dataset['username']);
    expect($result['email'])->toEqual($dataset['email']);
    expect($result['updated_at'])->toBeNull();
});

it('should authenticate user with username, password', function() {
    $dataset = [
        'uuid'       => faker()->uuid,
        'username'   => faker()->userName,
        'password'   => 'test123Aa@!',
        'email'      => faker()->email,
        'role_uid'   => 'user',
        'first_name' => faker()->firstName,
        'last_name'  => faker()->lastName,
        'created_at' => date( 'Y-m-d H:i:s' )

    ];
    ( new UserModel() )->create( $dataset );
    $r = post(
        uri: '/api/auth/login',
        data: [
            'password' => $dataset['password'],
            'email' => $dataset['email']
        ]
    );

    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect($result)->toHaveKeys((new UserModel())->getPublicFields(['token']));
    expect($result['username'])->toEqual($dataset['username']);
    expect($result['email'])->toEqual($dataset['email']);
});

it('should return profile data', function () {
    $dataset = [
        'username'   => faker()->userName,
        'password'   => 'test123A!',
        'email'      => faker()->email,
        'first_name' => faker()->firstName,
        'last_name'  => faker()->lastName

    ];
    $user = new UserModel();
    $user->validate( $dataset );
    $userdata = $user->create();
    $userdata = ( new JWTService() )->addToken($userdata);

    $r = get(uri: '/api/auth/me',headers: ['Authorization' => 'Bearer '.$userdata['token']]);
    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect($result)->toHaveKeys((new UserModel())->getPublicFields());
    expect($result['username'])->toEqual($dataset['username']);
    expect($result['email'])->toEqual($dataset['email']);
    expect($result['updated_at'])->toBeNull();
});

