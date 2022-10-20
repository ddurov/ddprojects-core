<?php declare(strict_types=1);

namespace Core\Services;

use Core\Exceptions\InvalidParameter;
use Core\Mail;
use Core\ManagerDatabase;
use Core\Models\EmailModel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class EmailService
{
    private EntityRepository $entityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(EmailModel::class);
    }

    /**
     * Возвращает хэш кода подтверждения
     * @param string $email
     * @return string
     * @throws Exception
     */
    public function createCode(string $email): string
    {
        $code = bin2hex(openssl_random_pseudo_bytes(8));
        $hash = bin2hex(openssl_random_pseudo_bytes(16));

        /** @var EmailModel $emailCodeDetails */
        $emailCodeDetails = $this->entityRepository->findOneBy(["email" => $email]);

        if ($emailCodeDetails !== null) {
            if ((time() - $emailCodeDetails->getRequestTime()) < 300) return $emailCodeDetails->getHash();

            $emailCodeDetails->setCode($code);
            $emailCodeDetails->setRequestTime(time());
            $emailCodeDetails->setHash($hash);
        } else {
            $newEmailCode = new EmailModel();
            $newEmailCode->setCode($code);
            $newEmailCode->setEmail($email);
            $newEmailCode->setRequestTime(time());
            $newEmailCode->setRequestIP($_SERVER['REMOTE_ADDR']);
            $newEmailCode->setHash($hash);

            ManagerDatabase::getInstance()->persist($newEmailCode);
        }

        ManagerDatabase::getInstance()->flush();

        Mail::getInstance()->setFrom(getenv("MAIL_USER"), explode("@", getenv("MAIL_USER"))[0]);
        Mail::getInstance()->addAddress($email);

        Mail::getInstance()->isHTML();
        Mail::getInstance()->Subject = "Код подтверждения";
        Mail::getInstance()->Body = "Код подтверждения: <b>$code</b><br>Данный код будет активен в течении часа с момента получения письма<br>Если вы не запрашивали данное письмо - <b>немедленно смените пароль</b>";
        Mail::getInstance()->AltBody = "Код подтверждения: $code\nДанный код будет активен в течении часа с момента получения письма\nЕсли вы не запрашивали данное письмо - немедленно смените пароль";
        Mail::getInstance()->send();

        return $hash;
    }

    /**
     * Возвращает true в случае успешной проверки, выбрасывает исключение если неуспешно
     * @param string $code
     * @param string $hash
     * @param int $needRemove
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     * @throws ORMException
     * @throws InvalidParameter
     */
    public function confirmCode(string $code, string $hash, int $needRemove = 0): bool
    {
        /** @var EmailModel $codeDetails */
        $codeDetails = $this->entityRepository->findOneBy(["code" => $code, "hash" => $hash]);

        if ($codeDetails === null || $codeDetails->getCode() !== $code) throw new InvalidParameter("parameter 'code' are invalid", 400);

        if ($codeDetails->getHash() !== $hash) throw new InvalidParameter("parameter 'hash' are invalid", 400);

        if ($needRemove === 1) ManagerDatabase::getInstance()->remove($codeDetails);

        ManagerDatabase::getInstance()->flush();

        return true;
    }

}