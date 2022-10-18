<?php

namespace Core\Exceptions;

class InvalidParameterLength extends \Exception
{
    public function __construct(string $parameter)
    {
        parent::__construct("parameter '{$parameter}' has an incorrect length", 400);
    }
}