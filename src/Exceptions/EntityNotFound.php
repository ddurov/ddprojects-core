<?php

namespace Core\Exceptions;

use Exception;

class EntityNotFound extends Exception {
    protected $code = 404;
}