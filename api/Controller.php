<?php

namespace API;

use Slim\Container;

class Controller {
    public function __construct(public Container $container) {
    }
}
