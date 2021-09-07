<?php
return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => require_once __DIR__ . '/api/config.php',
    'version_order' => 'creation'
];
