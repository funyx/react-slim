<?php
$cfg = require_once __DIR__ . '/api/config.php';

return [
    'paths'         => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments'  => $cfg['db'],
    'version_order' => 'creation'
];
