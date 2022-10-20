<?php declare(strict_types=1);

namespace Core\Services;

use Core\Exceptions\EntityExists;
use Core\Exceptions\EntityNotFound;
use Core\Exceptions\InvalidParameter;
use Core\ManagerDatabase;
use Core\Models\SessionModel;
use Core\Models\TokenModel;
use Core\Models\UserModel;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class UserService
{
    private EntityRepository $entityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(UserModel::class);
    }

    /**
     * Регистрирует пользователя, возвращает айди
     * @param string $login
     * @param string $password
     * @param string $username
     * @param string $email
     * @return int
     * @throws ORMException
     * @throws Exception
     * @throws OptimisticLockException
     * @throws EntityExists
     */
    public function registerAccount(string $login, string $password, string $username, string $email): int
    {
        if ($this->entityRepository->findOneBy(["login" => $login]) !== null)
            throw new EntityExists("current entity 'account by login' are exists", 422);

        if ($this->entityRepository->findOneBy(["username" => $username]) !== null)
            throw new EntityExists("current entity 'account by username' are exists", 422);

        $passwordSalt = openssl_random_pseudo_bytes(16);

        $newUser = new UserModel();
        $newUser->setLogin($login);
        $newUser->setPassword(md5($password.$passwordSalt));
        $newUser->setPasswordSalt($passwordSalt);
        $newUser->setEmail($email);
        $newUser->setUsername($username);

        ManagerDatabase::getInstance()->persist($newUser);
        ManagerDatabase::getInstance()->flush();

        return $newUser->getId();
    }


    /**
     * Авторизует аккаунт, возвращает айди пользователя
     * * TODO: Перехуярить логику блокировок в админ функции и переписать проверку бана
     * @param string $login
     * @param string $password
     * @return int
     * @throws InvalidParameter
     * @throws EntityNotFound
     */
    public function auth(string $login, string $password): int
    {
        /** @var UserModel $account */
        $account = $this->entityRepository->findOneBy(["login" => $login]);

        if ($account === null) throw new EntityNotFound("current entity 'account by login' not found", 404);

        if (md5($password . $account->getPasswordSalt()) !== $account->getPassword()) throw new InvalidParameter("parameter 'password' are invalid", 400);

        /*
        $ban = Database::getInstance()->query("SELECT * FROM general.bans WHERE eid = ?i", $accountAsArray['id']);
        $banAsArray = $ban->fetchAssoc();

        if ($ban->getNumRows()) throw new selfThrows(["message" => "account has been banned", "details" => ["reason" => $banAsArray["reason"], "canRestoreAccount" => (time() > $banAsArray['unbanTime'])]], 500);

        if (Database::getInstance()->query("SELECT * FROM general.attempts_auth WHERE login = '?s'", $login)->getNumRows() >= 5) {

            $banAsArray = Database::getInstance()->query("SELECT * FROM general.bans WHERE eid = ?i", $accountAsArray['id'])->fetchAssoc();
            throw new selfThrows(["message" => "account has been banned", "details" => ["reason" => $banAsArray["reason"], "canRestoreAccount" => (time() > $banAsArray['unbanTime'])]], 500);

        }
        */

        /*Database::getInstance()->query("INSERT INTO auth_attempts (aid, `time`, authIp) VALUES (?i, ?i, '?s')",
            $accountAsArray['id'],
            time(),
            $_SERVER['REMOTE_ADDR']);*/

        return $account->getId();
    }

    /**
     * Изменяет пароль аккаунта, возвращает true
     * * TODO: Сделать удаление всех авторизованных сессий и токенов кроме текущей
     * @param string $newPassword
     * @param string $sessionId
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function resetPassword(string $newPassword, string $sessionId): bool
    {
        $salt = bin2hex(openssl_random_pseudo_bytes(16));

        /** @var UserModel $account */
        $account = $this->entityRepository->findOneBy(
            ["id" => ManagerDatabase::getInstance()->getRepository(SessionModel::class)->findOneBy(
                ["sessionId" => $sessionId]
            )->getAid()]
        );

        $account->setPassword(md5($newPassword . $salt));
        $account->setPasswordSalt($salt);

        ManagerDatabase::getInstance()->flush();

        return true;
    }

    /**
     * Возвращает информацию об пользователе по айди
     * @param int|null $aId
     * @param string $token
     * @return array
     * @throws Exception
     * @throws ORMException
     * @throws EntityNotFound
     */
    public function get(?int $aId, string $token): array
    {
        /** @var UserModel $account */
        $account = $this->entityRepository->find($aId ?? ManagerDatabase::getInstance()->getRepository(TokenModel::class)->findOneBy(["token" => $token])->getAid());

        if ($account === null) throw new EntityNotFound("current entity 'account by id' not found", 404);

        return [
            "aid" => $account->getId(),
            "username" => $account->getUsername()
        ];
    }

    /**
     * Возвращает массив информации найденных пользователей по поисковому запросу
     * @param string $query
     * @return array
     * @throws EntityNotFound
     */
    public function search(string $query): array
    {
        /** @var UserModel[] $accounts */
        $accounts = $this->entityRepository->createQueryBuilder("u")
            ->where("u.username LIKE :search")
            ->setParameter("search", "%{$query}%")
            ->getQuery()->getResult();

        if ($accounts === [])
            throw new EntityNotFound("current entities 'accounts by search' not found", 404);

        $preparedData = [];

        foreach ($accounts as $account) {
            $preparedData[] = [
                "aid" => $account->getId(),
                "username" => $account->getUsername(),
            ];
        }

        return $preparedData;
    }

    /**
     * Изменяет имя пользователя
     * @param string $newName
     * @param string $token
     * @return bool
     * @throws ORMException
     * @throws Exception
     */
    public function changeName(string $newName, string $token): bool
    {
        $this->entityRepository->find(ManagerDatabase::getInstance()->getRepository(TokenModel::class)->findOneBy(["token" => $token])->getAid())
            ->setUsername($newName);
        return true;
    }
}