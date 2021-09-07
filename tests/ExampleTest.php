<?php

use function Pest\Faker\faker;

it('should create a user', function () {
    global $app;
    $username = faker()->userName;
    $email = faker()->email;
    $password = faker()->regexify('[A-Za-z0-9]{20}');
    /* @var \Slim\Http\Response $r */
    $r = post(uri: '/api/user',data: [
        'username' => $username,
        'password' => $password,
        'email' => $email
    ]);

    // created status code
    expect($r->getStatusCode())->toBeScalar()->toEqual(201);
    expect($r->getHeaderLine('Content-Type'))->toEqual('application/json');
    $record = resJson($r);
    expect($record['username'])->toEqual($username);
    expect($record['email'])->toEqual($email);
});
