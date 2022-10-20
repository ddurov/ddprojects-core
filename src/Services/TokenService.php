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

class TokenService
{
    private EntityRepository $entityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(TokenModel::class);
    }

    /**
     * Возвращает новый токен
     * @param int $tokenType
     * @param string $sessionId
     * @return string
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(int $tokenType, string $sessionId): string
    {
        $token = bin2hex(openssl_random_pseudo_bytes(48));

        $newToken = new TokenModel();
        $newToken->setAId(ManagerDatabase::getInstance()->getRepository(SessionModel::class)->findOneBy(["sessionId" => $sessionId])->getAId());
        $newToken->setToken($token);
        $newToken->setTokenType($tokenType);

        ManagerDatabase::getInstance()->persist($newToken);
        ManagerDatabase::getInstance()->flush();

        return $token;
    }

    /**
     * Возвращает 1 токен
     * @param int $tokenType
     * @param string $sessionId
     * @return string
     * @throws Exception
     * @throws ORMException
     */
    public function get(int $tokenType, string $sessionId): string
    {
        return $this->entityRepository->findOneBy(
            ["aId" => ManagerDatabase::getInstance()->getRepository(SessionModel::class)->findOneBy(["sessionId" => $sessionId])->getAId(),
                "tokenType" => $tokenType
            ])->getToken();
    }

    /**
     * Возвращает true в случае успешной проверки, выбрасывает исключение в ином
     * @param string $token
     * @return bool
     * @throws EntityNotFound
     */
    public function check(string $token): bool
    {
        if ($this->entityRepository->findOneBy(["token" => $token]) === null)
            throw new EntityNotFound("current entity 'token' not found", 404);

        return true;
    }
}