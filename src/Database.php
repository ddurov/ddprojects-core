<?php

namespace Core;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

class Database implements Singleton
{
	private static ?Database $instance = null;
	private EntityManager $entityManager;

	/**
	 * @throws Exception
	 */
	public function __construct(string $url)
	{
		$this->entityManager = new EntityManager(
			DriverManager::getConnection(
				(new DsnParser())->parse($url)
			),
			ORMSetup::createAttributeMetadataConfiguration([getcwd() . "/src"])
		);
	}

	/**
	 * @throws Exception
	 */
	public function executeCLI(): void
	{
		ConsoleRunner::run(new SingleManagerProvider(self::getEntityManager()));
	}

	/**
	 * @throws Exception
	 */
	public function getEntityManager(): EntityManager
	{
		return self::getInstance()->entityManager;
	}

	/**
	 * @throws Exception
	 */
	public static function getInstance(): ?Database
	{
		if (self::$instance === null) {
			self::$instance = new Database((string)getenv("DATABASE_URL"));
		}
		return self::$instance;
	}
}