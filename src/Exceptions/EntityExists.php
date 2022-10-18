<?php

namespace Core\Exceptions;

use Exception;

class EntityExists extends Exception
{
    public function __construct(string $entity)
    {
        parent::__construct("current entity '{$entity}' are exists", 422);
    }
}