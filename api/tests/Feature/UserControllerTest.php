<?php

use function Pest\Faker\faker;

it('should create, read, collect, delete and update user(s)', function () {
    $dataset = [
        'username' => faker()->userName,
        'email' => faker()->email,
        'password' => faker()->regexify('[A-Za-z0-9]{20}'),

    ];

    // register/create
    $r = post(
        uri: '/api/auth/register',
        data: [
            'username' => $dataset['username'],
            'password' => $dataset['password'],
            'email' => $dataset['email']
        ]
    );

    expect($r->getStatusCode())->toBeScalar()->toEqual(201);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect($result)->toHaveKeys(['id', 'username', 'email', 'token', 'created_at', 'updated_at']);
    expect($result['username'])->toEqual($dataset['username']);
    expect($result['email'])->toEqual($dataset['email']);
    expect($result['updated_at'])->toBeNull();

    $dataset['id'] = $result['id'];
    $dataset['token'] = $result['token'];
    $dataset['created_at'] = $result['created_at'];
    $dataset['updated_at'] = $result['updated_at'];

    // collection
    $r = get(uri: '/api/user', headers: ['Authorization' => 'Bearer '.$dataset['token']]);
    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect(count($result))->toBeGreaterThan(0);

    // read
    $r = get(uri: '/api/user/'.$dataset['id'], headers: ['Authorization' => 'Bearer '.$dataset['token']]);
    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect($result)->toHaveKeys(['id', 'username', 'email', 'created_at', 'updated_at']);
    expect($result['id'])->toEqual($dataset['id']);
    expect($result['username'])->toEqual($dataset['username']);
    expect($result['email'])->toEqual($dataset['email']);
    expect($result['created_at'])->toEqual($dataset['created_at']);
    expect($result['updated_at'])->toBeNull();

    // update
    $new_dataset = [];
    $new_dataset['username'] = faker()->userName;
    $new_dataset['email'] = faker()->email;
    $r = put(
        uri: '/api/user/'.$dataset['id'],
        data: [
            'username' => $new_dataset['username'],
            'email' => $new_dataset['email']
        ],
        headers: ['Authorization' => 'Bearer '.$dataset['token']]
    );
    expect($r->getStatusCode())->toBeScalar()->toEqual(200);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $result = resJson($r);
    expect($result)->toHaveKeys(['id', 'username', 'email', 'created_at', 'updated_at']);
    expect($result['id'])->toEqual($dataset['id']);
    expect($result['username'])->toEqual($new_dataset['username']);
    expect($result['email'])->toEqual($new_dataset['email']);
    expect($result['created_at'])->toEqual($dataset['created_at']);
    expect($result['updated_at'])->not()->toBeNull();

    // delete
    $r = delete('/api/user/'.$dataset['id'], headers: ['Authorization' => 'Bearer '.$dataset['token']]);
    expect($r->getStatusCode())->toBeScalar()->toEqual(204);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
});

