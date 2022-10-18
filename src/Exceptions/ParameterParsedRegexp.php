<?php

namespace Core\Exceptions;

use Exception;

class ParameterParsedRegexp extends Exception
{
    public function __construct(string $parameter, string $regexp)
    {
        parent::__construct("parameter '{$parameter}' should doesn't parsed by regular expression ({$regexp})", 400);
    }
}