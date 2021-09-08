<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

expect()->extend( 'toBeOne', function() {
    return $this->toBe( 1 );
} );

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

require_once __DIR__ . '/../app/bootstrap.php';

function getRoute($route_name, $params = []) {
    global $app;

    return $app->getContainer()->get( 'router' )->pathFor( $route_name, $params );
}

/**
 * @throws \Throwable
 */
function api($method, $uri, $data = null, $headers = null): ResponseInterface {
    global $app;

    $request = Request::createFromEnvironment( Environment::mock(
        [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri
        ]
    ) );

    if( isset( $headers ) && is_array($headers) ) {
        foreach($headers as $name => $value){
            $request = $request->withHeader($name, $value);
        }
    }

    if( isset( $data ) ) {
        $request = $request->withParsedBody( $data );
    }

    return $app->process( $request, new Response() );
}

/**
 * @throws \Throwable
 */
function get($uri, $data = null, $headers = null): ResponseInterface {
    return api( 'GET', $uri, $data, $headers );
}

/**
 * @throws \Throwable
 */
function post($uri, $data = null, $headers = null): ResponseInterface {
    return api( 'POST', $uri, $data, $headers );
}

/**
 * @throws \Throwable
 */
function put($uri, $data = null, $headers = null): ResponseInterface {
    return api( 'PUT', $uri, $data, $headers );
}

/**
 * @throws \Throwable
 */
function delete($uri, $data = null, $headers = null): ResponseInterface {
    return api( 'DELETE', $uri, $data, $headers );
}

function resJson(ResponseInterface $r, $associative = true){
    $record = (string) $r->getBody();
    return json_decode($record, $associative);
}
