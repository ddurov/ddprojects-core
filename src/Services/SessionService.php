<?php declare(strict_types=1);

namespace Core\Services;

use Core\Exceptions\EntityNotFound;
use Core\ManagerDatabase;
use Core\Models\SessionModel;
use Core\Models\TokenModel;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Mobile_Detect;

class SessionService
{
    private EntityRepository $entityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(TokenModel::class);
    }

    /**
     * Возвращает новую сессию
     * @param string $token
     * @return string
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(string $token): string
    {
        $sessionId = bin2hex(openssl_random_pseudo_bytes(32));

        $newSession = new SessionModel();
        $newSession->setSessionId($sessionId);
        $newSession->setAId(ManagerDatabase::getInstance()->getRepository(TokenModel::class)->findOneBy(["token" => $token])->getAId());
        $newSession->setAuthTime(time());
        $newSession->setAuthDevice(((new Mobile_Detect)->isMobile() || (new Mobile_Detect)->isTablet()) ? 1 : 0);
        $newSession->setAuthIP($_SERVER['REMOTE_ADDR']);

        ManagerDatabase::getInstance()->persist($newSession);
        ManagerDatabase::getInstance()->flush();

        return $sessionId;
    }


    /**
     * Возвращает 1 сессию
     * * TODO: Возвращать предпочтительно уже существующую сессию для типа девайса
     * @param string $token
     * @return string
     * @throws Exception
     * @throws ORMException
     */
    public function get(string $token): string
    {
        return $this->entityRepository->findOneBy(["aId" =>
            ManagerDatabase::getInstance()->getRepository(TokenModel::class)->findOneBy(["token" => $token])->getAId()
        ])->getSessionId();
    }

    /**
     * Возвращает true в случае успешной проверки, выбрасывает исключение в ином
     * @param string $sessionId
     * @return bool
     * @throws EntityNotFound
     */
    public function check(string $sessionId): bool
    {
        if ($this->entityRepository->findOneBy(["sessionId" => $sessionId]) === null)
            throw new EntityNotFound("current entity 'session by id' not found");

        return true;
    }
}