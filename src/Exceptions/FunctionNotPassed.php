<?php

namespace Core\Exceptions;

use Exception;

class FunctionNotPassed extends Exception {
    protected $code = 400;
}