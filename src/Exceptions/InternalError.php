<?php

namespace Core\Exceptions;

use Exception;

class InternalError extends Exception
{
    public function __construct()
    {
        parent::__construct("internal error, try update page", 500);
    }
}