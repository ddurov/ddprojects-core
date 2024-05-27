<?php

namespace Core;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

class Database
{
	private Connection $connection;
	public EntityManager $entityManager;

	/**
	 * @param string $attributeMetadataFolder
	 * @param string $dbName
	 * @param string $dbUser
	 * @param string $dbPassword
	 * @param string $dbServer
	 * @param int $dbPort
	 * @param string $dbDriver
	 * @throws Exception
	 * @throws MissingMappingDriverImplementation
	 */
	public function __construct(
		string $attributeMetadataFolder,
		string $dbName,
		string $dbUser,
		string $dbPassword,
		string $dbServer,
		int $dbPort = 3306,
		string $dbDriver = "mysqli",
	) {
		$this->connection = DriverManager::getConnection([
			'dbname' => $dbName,
			'user' => $dbUser,
			'password' => $dbPassword,
			'host' => $dbServer,
			'port' => $dbPort,
			'driver' => $dbDriver,
		]);
		$this->entityManager = new EntityManager(
			$this->connection,
			ORMSetup::createAttributeMetadataConfiguration([$attributeMetadataFolder])
		);
	}

	public function executeCLI(): void
	{
		ConsoleRunner::run(new SingleManagerProvider($this->entityManager));
	}
}