<?php

namespace API;

class Model {
    protected \PDO $pdo;

    public function __construct(\PDO $pdo = null) {
        if( is_null( $pdo ) ) {
            global $c;
            /* @var \Pdo $pdo */
            $pdo = $c['pdo'];
        }
        $this->pdo = $pdo;
    }
}
