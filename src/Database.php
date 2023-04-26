<?php

namespace Core;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMSetup;

class Database
{
    /**
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbServer
     * @param int $dbPort
     * @param string $attributeMetadataFolder
     * @param string $dbDriver
     * @return EntityManager
     * @throws Exception
     * @throws ORMException
     */
    public function create(string $dbName, string $dbUser, string $dbPassword, string $dbServer, int $dbPort, string $attributeMetadataFolder, string $dbDriver = "mysqli"): EntityManager
    {
        return EntityManager::create(
            DriverManager::getConnection([
                'dbname' => $dbName,
                'user' => $dbUser,
                'password' => $dbPassword,
                'host' => $dbServer,
                'port' => $dbPort,
                'driver' => $dbDriver,
            ]),
            ORMSetup::createAttributeMetadataConfiguration([$attributeMetadataFolder])
        );
    }
}