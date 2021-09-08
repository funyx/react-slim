<?php

use JmesPath\Env;

$config = [
    'db'     => [
        'default_migration_table' => 'migration',
        'default_environment'     => $_ENV['ENV'] ?? 'development',
        'production'              => [
            'adapter' => 'mysql',
            'host'    => $_ENV['MYSQL_HOST'] ?? 'db',
            'name'    => $_ENV['MYSQL_DATABASE'] ?? 'db',
            'user'    => $_ENV['MYSQL_USER'] ?? 'user',
            'pass'    => $_ENV['MYSQL_PASSWORD'] ?? 'password',
            'port'    => $_ENV['MYSQL_PORT'] ?? '3306',
            'charset' => 'utf8',
        ],
        'development'             => [
            'adapter' => 'mysql',
            'host'    => $_ENV['MYSQL_HOST'] ?? 'db',
            'name'    => $_ENV['MYSQL_DATABASE'] ?? 'db',
            'user'    => $_ENV['MYSQL_USER'] ?? 'user',
            'pass'    => $_ENV['MYSQL_PASSWORD'] ?? 'password',
            'port'    => $_ENV['MYSQL_PORT'] ?? '3306',
            'charset' => 'utf8',
        ],
        'testing'                 => [
            'adapter' => 'mysql',
            'host'    => $_ENV['MYSQL_HOST'] ?? 'db',
            'name'    => $_ENV['MYSQL_DATABASE'] ?? 'db',
            'user'    => $_ENV['MYSQL_USER'] ?? 'user',
            'pass'    => $_ENV['MYSQL_PASSWORD'] ?? 'password',
            'port'    => $_ENV['MYSQL_PORT'] ?? '3306',
            'charset' => 'utf8',
        ]
    ],
    'api'    => [
        'settings' => [
            'displayErrorDetails' => false
        ],
        'collection' => [
            'limit' => 10
        ]
    ],
    'format' => [
        'date'     => 'Y-m-d',
        'datetime' => 'Y-m-d H:i:s',
        'time'     => 'H:i:s'
    ]
];

if( !function_exists( 'config' ) ) {
    function config($value, $default = null) {
        global $config;

        return Env::search( $value, $config );
    }
}

return $config;
