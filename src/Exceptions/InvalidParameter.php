<?php

namespace Core\Exceptions;

use Exception;

class InvalidParameter extends Exception {
    protected $code = 400;
}