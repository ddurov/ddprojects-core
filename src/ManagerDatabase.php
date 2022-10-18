<?php

namespace Core;

use Core\Contracts\Singleton;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMSetup;

class ManagerDatabase implements Singleton
{
    private static ?EntityManager $instance = null;

    /**
     * @return EntityManager|ManagerDatabase|null
     * @throws Exception
     * @throws ORMException
     */
    public static function getInstance(): EntityManager|ManagerDatabase|null
    {
        if (self::$instance === null) {
            self::$instance = EntityManager::create(
                DriverManager::getConnection([
                    'dbname' => getenv("DATABASE_NAME"),
                    'user' => getenv("DATABASE_LOGIN"),
                    'password' => getenv("DATABASE_PASSWORD"),
                    'host' => getenv("DATABASE_SERVER"),
                    'driver' => 'mysqli',
                ]),
                ORMSetup::createAttributeMetadataConfiguration([__DIR__])
            );
        }

        return self::$instance;
    }

    //singleton
    private function __construct(){}

    private function __clone(){}
}