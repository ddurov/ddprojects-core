<?php declare(strict_types=1);

namespace Core\Methods;

use Core\Database;
use Core\Mail;
use Core\Tools\selfThrows;
use Exception;
use Krugozor\Database\MySqlException;

class Email
{

    /**
     * Возвращает хэш кода подтверждения
     * @param string $email
     * @return string
     * @throws MySqlException|Exception|selfThrows
     */
    public function createCode(string $email): string
    {

        if (!preg_match("/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/", $email))
            throw new selfThrows(["message" => "email incorrect"]);

        if (preg_match("/(.*)?ddproj\.ru/isu", $email))
            throw new selfThrows(["message" => "email must not contain domains of any level ddproj.ru"]);

        $code = bin2hex(openssl_random_pseudo_bytes(8));
        $hash = bin2hex(openssl_random_pseudo_bytes(16));

        $emailCodeDetails = Database::getInstance()->query("SELECT * FROM email_codes WHERE email = '?s'", $email);
        $emailCodesAsArray = $emailCodeDetails->fetchAssoc();

        if ($emailCodeDetails->getNumRows()) {
            if (time() - $emailCodesAsArray['requestTime'] < 300)
                throw new selfThrows([
                    "message" => "code has already been requested",
                    "hash" => $emailCodesAsArray["hash"]
                ]);

            Database::getInstance()->query("UPDATE email_codes SET code = '?s', requestTime = ?i, hash = '?s' WHERE email = '?s'",
                $code,
                time(),
                $hash,
                $email);
        } else {
            Database::getInstance()->query("INSERT INTO email_codes (code, email, requestTime, requestIp, hash) VALUES ('?s', '?s', ?i, '?s', '?s')",
                $code,
                $email,
                time(),
                $_SERVER['REMOTE_ADDR'],
                $hash);
        }

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
     * Возвращает true в случае успешной проверки, выбрасывает исключение если не успешно
     * @param string $code
     * @param string $hash
     * @param int $needRemove
     * @return bool
     * @throws MySqlException|selfThrows
     */
    public function confirmCode(string $code, string $hash, int $needRemove = 0): bool
    {

        $emailCodeDetails = Database::getInstance()->query("SELECT * FROM email_codes WHERE code = '?s'", $code);

        $emailCodeDetailsAsArray = $emailCodeDetails->fetchAssoc();

        if (!$emailCodeDetails->getNumRows() || $emailCodeDetailsAsArray['code'] !== $code) throw new selfThrows(["message" => "invalid code"], 400);

        if ($emailCodeDetailsAsArray['hash'] !== $hash) throw new selfThrows(["message" => "invalid hash"], 400);

        if ($needRemove === 1) Database::getInstance()->query("DELETE FROM email_codes WHERE code = '?s'", $code);

        return true;

    }

}