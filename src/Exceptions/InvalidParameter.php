<?php

namespace Core\Exceptions;

use Exception;

class InvalidParameter extends Exception
{
    public function __construct(string $parameter)
    {
        parent::__construct("parameter '{$parameter}' are invalid", 400);
    }
}