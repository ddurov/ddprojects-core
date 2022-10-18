<?php

namespace Core\Exceptions;

use Exception;

class EntityNotFound extends Exception
{
    public function __construct(string $entity)
    {
        parent::__construct("current entity '{$entity}' not found", 404);
    }
}