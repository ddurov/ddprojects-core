<?php

namespace Core\Exceptions;

class RouteNotFound extends CoreExceptions
{
    protected $message = "current route not found for this request method";

    protected $code = 404;
}