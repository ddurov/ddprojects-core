<?php

namespace Core\Exceptions;

use Exception;

class ParameterNotParsedRegexp extends Exception
{
    public function __construct(string $parameter, string $regexp)
    {
        parent::__construct("parameter '{$parameter}' should be parsed by regular expression ({$regexp})", 400);
    }
}