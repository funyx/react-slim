<?php

use App\Model\UserModel;
use App\Service\JWTService;
use function Pest\Faker\faker;

it('get /api/user should require JWT', function() {
    $r = get(
        uri: '/api/user'
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(401);
});

it('post /api/user should require JWT', function() {
    $r = post(
        uri: '/api/user'
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(401);
});

it('put /api/user/1 should require JWT', function() {
    $r = put(
        uri: '/api/user/1'
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(401);
});

it('delete /api/user/1 should require JWT', function() {
    $r = delete(
        uri: '/api/user/1'
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(401);
});

it('get /api/user/1 should require JWT', function() {
    $r = get(
        uri: '/api/user/1'
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(401);
});

it('should list user records if authenticated', function() {
    $dataset = [
        'username' => faker()->userName,
        'email' => faker()->email,
        'password' => faker()->regexify('[A-Za-z0-9]{20}'),

    ];
    $m = (new UserModel());
    $m->validate($dataset);
    $m->create();

    $user = $m->loadBy('username', $dataset['username'])->get();
    $user = (new JWTService())->addToken($user);

    $token = $user['token'];
    $r = get(uri: '/api/user', headers: ['Authorization' => 'Bearer '.$token]);

    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect(count($result))->toBeGreaterThan(0);

});

it('should get a single user record if authenticated', function(){
    $m = (new UserModel());
    $user = $m->loadAny()->get();
    $user = (new JWTService())->addToken($user);

    $token = $user['token'];
    $r = get(uri: '/api/user/'.$user['uuid'], headers: ['Authorization' => 'Bearer '.$token]);

    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect(count($result))->toBeGreaterThan(0);

});

it('should create a single user record if authenticated', function(){
    $m = (new UserModel());
    $user = $m->loadAny()->get();
    $user = (new JWTService())->addToken($user);

    $new_data = [
        'username' => faker()->userName,
        'password' => faker()->password(8),
        'email' => faker()->email,
        'first_name' => faker()->firstName,
        'last_name' => faker()->lastName,
    ];
    $token = $user['token'];
    $r = post(
        uri: '/api/user',
        data: $new_data,
        headers: [ 'Authorization' => 'Bearer '.$token]
    );

    expect($r->getStatusCode())->toBeScalar()->toEqual(201);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    $check = $m->loadBy('uuid', $user['uuid'])->get();
    // changed
    expect($new_data['first_name'])->toEqual($result['first_name']);
    expect($new_data['last_name'])->toEqual($result['last_name']);
    expect($new_data['email'])->toEqual($result['email']);
    expect($new_data['username'])->toEqual($result['username']);
});

it('should delete a single user record if authenticated', function(){
    $m = (new UserModel());
    $user = $m->loadAny()->get();
    $user = (new JWTService())->addToken($user);

    $token = $user['token'];
    $r = delete(
        uri: '/api/user/'.$user['uuid'],
        headers: [ 'Authorization' => 'Bearer '.$token]
    );

    expect($r->getStatusCode())->toBeScalar()->toEqual(204);
});

