<?php

namespace Core\Exceptions;

use Exception;

class RouteNotFound extends Exception
{
    protected $message = "current route not found for this request method";
}