<?php

namespace Core\Exceptions;

class InvalidParameterType extends \Exception
{
    public function __construct(string $parameter, string $type)
    {
        parent::__construct("parameter '{$parameter}' should be {$type} type", 400);
    }
}