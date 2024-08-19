<?php

namespace Core;

use Core\DTO\ErrorResponse;
use Core\Exceptions\InternalError;
use Core\Tools\Other;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

class Database
{
	private EntityManager $entityManager;

	/**
	 * @param string $attributeMetadataFolder
	 * @param string $dbName
	 * @param string $dbUser
	 * @param string $dbPassword
	 * @param string $dbServer
	 * @param int $dbPort
	 * @param string $dbDriver
	 * @throws InternalError
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
		try {
			$this->entityManager = new EntityManager(
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
		} catch (Exception|MissingMappingDriverImplementation $e) {
			Other::log(
				"logs",
				"database",
				"Error: " . $e->getMessage() .
				", on line: " . $e->getLine() .
				", in: " . $e->getFile()
			);
			throw new InternalError("internal error, try later", 500);
		}
	}

	public function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}

	public function executeCLI(): void
	{
		ConsoleRunner::run(new SingleManagerProvider($this->entityManager));
	}
}