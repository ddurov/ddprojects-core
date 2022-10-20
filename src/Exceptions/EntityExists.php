<?php

namespace Core\Exceptions;

use Exception;

class EntityExists extends Exception {
    protected $code = 422;
}