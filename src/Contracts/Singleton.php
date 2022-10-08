<?php declare(strict_types=1);

namespace Core\Contracts;

interface Singleton
{
    /**
     * @return static
     */
    public static function getInstance();
}