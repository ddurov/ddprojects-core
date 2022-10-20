<?php

namespace Core\Exceptions;

use Exception;

class InternalError extends Exception {
    protected $code = 500;
}